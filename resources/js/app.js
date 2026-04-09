import './bootstrap';
import './rapida-map';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { queueReport, syncQueue, getPendingCount, getCurrentPressure } from './offline-queue';

// Register Alpine plugins and stores.
// Livewire 4 auto-starts Alpine on pages WITH Livewire components.
// On pages WITHOUT Livewire (map home, report detail), we start Alpine ourselves.
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

// Expose Alpine globally — Livewire 4 checks window.Alpine before injecting its own
window.Alpine = Alpine;

// Start Alpine after DOM is ready.
// On Livewire pages, Livewire calls Alpine.start() itself via its injected script.
// On non-Livewire pages, we need to start it ourselves.
document.addEventListener('DOMContentLoaded', () => {
    // If Livewire hasn't already started Alpine, start it now
    if (!Alpine.started) {
        Alpine.start();
    }
});

// Expose offline queue for Livewire access
window.rapidaOfflineQueue = { queueReport, syncQueue, getPendingCount, getCurrentPressure };
