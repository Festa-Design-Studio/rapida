import { openDB } from 'idb';

const DB_NAME = 'rapida-offline';
const STORE_NAME = 'reports';
const DB_VERSION = 1;

// Backpressure sync delays (ms) by queue pressure level
const PRESSURE_DELAYS = {
    normal: 0,
    moderate: 5000,
    high: 15000,
    critical: 30000,
};

let currentPressure = 'normal';
let syncTimeout = null;

export async function getDb() {
    return openDB(DB_NAME, DB_VERSION, {
        upgrade(db) {
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'idempotency_key' });
            }
        },
    });
}

export async function queueReport(report) {
    const db = await getDb();
    await db.put(STORE_NAME, {
        ...report,
        queued_at: new Date().toISOString(),
    });
}

export async function syncQueue() {
    const db = await getDb();
    const reports = await db.getAll(STORE_NAME);

    for (const report of reports) {
        try {
            const res = await fetch('/api/v1/reports', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Idempotency-Key': report.idempotency_key,
                },
                body: JSON.stringify(report),
            });

            if (res.ok) {
                await db.delete(STORE_NAME, report.idempotency_key);

                // Read backpressure header for next sync timing
                const pressure = res.headers.get('X-Rapida-Queue-Pressure') || 'normal';
                currentPressure = pressure;
            } else if (res.status === 429 || res.status === 503) {
                // Rate limited or paused — back off using Retry-After or pressure delay
                const retryAfter = parseInt(res.headers.get('Retry-After') || '30', 10);
                scheduleSyncRetry(retryAfter * 1000);
                return; // Stop processing queue, retry later
            }
        } catch (e) {
            // Network error — remain in queue for next sync attempt
            scheduleSyncRetry(PRESSURE_DELAYS.high);
            return;
        }
    }
}

function scheduleSyncRetry(delayMs) {
    if (syncTimeout) clearTimeout(syncTimeout);
    syncTimeout = setTimeout(syncQueue, delayMs);
}

export async function getPendingCount() {
    const db = await getDb();
    return await db.count(STORE_NAME);
}

export function getCurrentPressure() {
    return currentPressure;
}

// Auto-sync when connectivity returns, respecting backpressure
window.addEventListener('online', () => {
    const delay = PRESSURE_DELAYS[currentPressure] || 0;
    scheduleSyncRetry(delay);
});
