import { registerRoute, setCatchHandler } from 'workbox-routing';
import { CacheFirst, NetworkFirst, StaleWhileRevalidate } from 'workbox-strategies';
import { ExpirationPlugin } from 'workbox-expiration';
import { BackgroundSyncPlugin } from 'workbox-background-sync';

// 1. App shell — Cache First
registerRoute(
    ({ request }) => request.destination === 'document' ||
                     request.destination === 'script' ||
                     request.destination === 'style',
    new CacheFirst({
        cacheName: 'rapida-app-shell-v1',
        plugins: [new ExpirationPlugin({ maxAgeSeconds: 7 * 24 * 60 * 60 })],
    })
);

// 2. Fonts — Cache First (self-hosted in /fonts/, no CDN dependency)
registerRoute(
    ({ url }) => url.pathname.startsWith('/fonts/') && url.pathname.endsWith('.woff2'),
    new CacheFirst({
        cacheName: 'rapida-fonts-v1',
        plugins: [new ExpirationPlugin({ maxAgeSeconds: 365 * 24 * 60 * 60, maxEntries: 20 })],
    })
);

// 3. Map tiles — Cache First with large quota
registerRoute(
    ({ url }) => url.hostname.includes('tile.openstreetmap.org'),
    new CacheFirst({
        cacheName: 'rapida-tiles-v1',
        plugins: [new ExpirationPlugin({ maxEntries: 500, maxAgeSeconds: 30 * 24 * 60 * 60 })],
    })
);

// 4. Building footprints — Stale While Revalidate
registerRoute(
    ({ url }) => url.pathname.includes('/api/v1/crises') && url.pathname.includes('/buildings'),
    new StaleWhileRevalidate({ cacheName: 'rapida-footprints-v1' })
);

// 5. Live pins — Network First
registerRoute(
    ({ url }) => url.pathname.includes('/api/v1/crises') && url.pathname.includes('/pins'),
    new NetworkFirst({ cacheName: 'rapida-pins-v1', networkTimeoutSeconds: 3 })
);

// 6. Report submission — Background Sync (reporter path, /api/v1/reports)
const bgSyncPlugin = new BackgroundSyncPlugin('rapida-report-queue', {
    maxRetentionTime: 24 * 60,
});

registerRoute(
    ({ url, request }) => url.pathname === '/api/v1/reports' && request.method === 'POST',
    new NetworkFirst({ plugins: [bgSyncPlugin] }),
    'POST'
);

// 6b. Field coordinator writes — Background Sync (gap-33). Field staff in
// the dashboard hit flag/assign/verify endpoints from a phone with
// intermittent signal. Without queueing, those writes silently fail when
// connectivity drops mid-action. Same plugin, separate queue name so the
// retry budget for staff writes is independent of reporter submissions.
const staffSyncPlugin = new BackgroundSyncPlugin('rapida-staff-write-queue', {
    maxRetentionTime: 24 * 60,
});

const STAFF_WRITE_PATTERN = /^\/dashboard\/reports\/[a-z0-9-]+\/(flag|assign|verify)$/;

registerRoute(
    ({ url, request }) => STAFF_WRITE_PATTERN.test(url.pathname) && (request.method === 'POST' || request.method === 'PATCH'),
    new NetworkFirst({ plugins: [staffSyncPlugin] }),
    'POST'
);
registerRoute(
    ({ url, request }) => STAFF_WRITE_PATTERN.test(url.pathname) && request.method === 'PATCH',
    new NetworkFirst({ plugins: [staffSyncPlugin] }),
    'PATCH'
);

// 7. Offline fallback
setCatchHandler(async ({ event }) => {
    if (event.request.destination === 'document') {
        return caches.match('/offline.html') || new Response('You are offline. Your reports are saved and will sync when connected.', {
            headers: { 'Content-Type': 'text/html' },
        });
    }
    return Response.error();
});
