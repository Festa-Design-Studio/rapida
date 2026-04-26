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

// Gap-50: expose the photo-compression helper so the step-photo Alpine
// component can intercept the file upload, compress on the client, then
// hand the smaller File to Livewire's $wire.upload(). Lazy-imported so
// the ~30KB browser-image-compression bundle is only loaded if the
// reporter actually visits the wizard.
window.rapidaCompressPhoto = async (file) => {
    const { compressPhoto, PhotoTooLargeError } = await import('./photo-compression.js');
    try {
        return { ok: true, file: await compressPhoto(file) };
    } catch (err) {
        if (err instanceof PhotoTooLargeError) {
            return { ok: false, reason: 'photo_too_large' };
        }
        return { ok: false, reason: 'unknown', message: err.message };
    }
};
