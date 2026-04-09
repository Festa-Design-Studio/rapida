import './bootstrap';
import './rapida-map';
import collapse from '@alpinejs/collapse';
import { queueReport, syncQueue, getPendingCount } from './offline-queue';

// Register Alpine plugins and stores via the alpine:init event.
// This fires BEFORE Alpine.start() regardless of who starts it
// (Livewire injects Alpine automatically — we must NOT import or start it ourselves).
document.addEventListener('alpine:init', () => {
    window.Alpine.plugin(collapse);

    window.Alpine.store('offlineQueue', {
        pendingCount: 0,
        isSyncing: false,
        isOnline: navigator.onLine,

        async init() {
            this.pendingCount = await getPendingCount();
            window.addEventListener('online', () => { this.isOnline = true; this.sync(); });
            window.addEventListener('offline', () => { this.isOnline = false; });
        },

        async sync() {
            this.isSyncing = true;
            await syncQueue();
            this.pendingCount = await getPendingCount();
            this.isSyncing = false;
        },
    });
});

// Expose offline queue for Livewire access
window.rapidaOfflineQueue = { queueReport, syncQueue, getPendingCount };
