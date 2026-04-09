import { openDB } from 'idb';

const DB_NAME = 'rapida-offline';
const STORE_NAME = 'reports';
const DB_VERSION = 1;

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
            }
        } catch (e) {
            // Remain in queue for next sync attempt
        }
    }
}

export async function getPendingCount() {
    const db = await getDb();
    return await db.count(STORE_NAME);
}

// Auto-sync when connectivity returns
window.addEventListener('online', syncQueue);
