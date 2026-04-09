import './bootstrap';
import './rapida-map';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { queueReport, syncQueue, getPendingCount, getCurrentPressure } from './offline-queue';

// Register Alpine plugins and stores BEFORE Alpine starts.
Alpine.plugin(collapse);

Alpine.store('offlineQueue', {
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

// Expose Alpine globally ONCE — Livewire 4 detects window.Alpine and uses it
// instead of injecting its own copy. This prevents the "multiple instances" warning.
if (!window.Alpine) {
    window.Alpine = Alpine;
}

// Start Alpine only on non-Livewire pages.
// Livewire will call Alpine.start() on its own when it loads.
// We use a small delay to let Livewire claim Alpine first if present.
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (!Alpine.started) {
            Alpine.start();
        }
    }, 50);
});

// Expose offline queue for Livewire access
window.rapidaOfflineQueue = { queueReport, syncQueue, getPendingCount, getCurrentPressure };
