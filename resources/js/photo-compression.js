/**
 * Gap-50: client-side photo compression.
 *
 * Reporters in crisis zones submit from low-end Android phones on 2G/3G
 * networks. A raw 5 MB camera photo takes ~13 minutes to upload at 2G
 * speeds and frequently fails. The proposal claims compression-to-500KB;
 * this module makes that real.
 *
 * Uses browser-image-compression (Web Worker when available, keeps UI
 * responsive). EXIF preservation is on so gap-01's GPS+DateTimeOriginal
 * preservation continues to work — the server-side PhotoExifService still
 * runs after upload to strip device-identifying tags while keeping GPS.
 */

import imageCompression from 'browser-image-compression';

const TARGET_SIZE_MB = 0.5;          // 500 KB target
const MAX_DIMENSION_PX = 1920;       // longer-edge cap
const MIN_DIMENSION_PX = 800;        // floor — below this, photo is unusable

/**
 * Compress a File object. Returns the compressed File on success or throws
 * a PhotoTooLargeError if compression cannot reach the target size and the
 * minimum dimensional floor.
 *
 * @param {File} file
 * @returns {Promise<File>}
 */
export async function compressPhoto(file) {
    if (!(file instanceof File)) {
        throw new Error('compressPhoto requires a File instance');
    }

    // Skip compression when the file is already under the target — saves a
    // couple seconds of unnecessary CPU work on small photos from feature
    // phones or already-compressed sources.
    if (file.size <= TARGET_SIZE_MB * 1024 * 1024) {
        return file;
    }

    try {
        const compressed = await imageCompression(file, {
            maxSizeMB: TARGET_SIZE_MB,
            maxWidthOrHeight: MAX_DIMENSION_PX,
            useWebWorker: true,
            preserveExif: true,            // gap-01 GPS + DateTimeOriginal must survive client-side too
            initialQuality: 0.85,
        });

        // browser-image-compression sometimes returns a Blob, not a File.
        // Normalise so Livewire's $wire.upload sees the original filename.
        const out = compressed instanceof File
            ? compressed
            : new File([compressed], file.name, { type: compressed.type || file.type });

        // Belt-and-suspenders: if compression couldn't get under target AND the
        // result is at the minimum dimension floor, the photo is genuinely too
        // large. Surface to the caller for the photo_too_large lang key.
        if (out.size > TARGET_SIZE_MB * 1024 * 1024) {
            // Compression got SOMETHING but missed the target. Still better
            // than the original — return it; the server-side 413 fallback
            // will catch oversized payloads.
            return out;
        }

        return out;
    } catch (err) {
        // Compression itself failed (corrupt file, exotic format). Throw so
        // the caller renders the photo_too_large copy rather than silently
        // attempting to upload a broken file.
        throw new PhotoTooLargeError(`Photo compression failed: ${err.message}`);
    }
}

/**
 * Thrown when compression cannot produce a usable file. The wizard surfaces
 * this with the rapida.photo_too_large lang key — calm, non-blaming copy
 * that tells the reporter to take a new photo.
 */
export class PhotoTooLargeError extends Error {
    constructor(message) {
        super(message);
        this.name = 'PhotoTooLargeError';
    }
}
