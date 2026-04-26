<?php

namespace App\Jobs;

use App\Models\DamageReport;
use App\Services\PhotoExifService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPhotoUpload implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DamageReport $report
    ) {}

    public function handle(PhotoExifService $exifService): void
    {
        if (! $this->report->photo_url || ! str_starts_with($this->report->photo_url, 'photos/')) {
            return;
        }

        $path = storage_path('app/public/'.$this->report->photo_url);
        if (! file_exists($path)) {
            return;
        }

        $imageInfo = getimagesize($path);
        if (! $imageInfo) {
            return;
        }

        // Strip device-identifying EXIF tags while preserving GPS + DateTimeOriginal.
        // Only JPEGs carry EXIF; PNG/WebP have no EXIF block to sanitise here.
        if ($imageInfo['mime'] === 'image/jpeg') {
            $exifService->sanitize($path);
        }

        $pHash = $this->computePerceptualHash($path);

        $this->report->update([
            'photo_hash' => hash_file('sha256', $path),
            'photo_phash' => $pHash,
        ]);

        Log::info("Photo processed for report {$this->report->id}".($pHash ? " (pHash: {$pHash})" : ''));
    }

    /**
     * Compute a perceptual hash (average hash) using GD.
     * Resize to 8x8 grayscale, compare each pixel to the mean brightness.
     * Returns a 16-char hex string (64-bit hash).
     */
    private function computePerceptualHash(string $path): ?string
    {
        $image = @imagecreatefromstring(file_get_contents($path));
        if (! $image) {
            return null;
        }

        $small = imagecreatetruecolor(8, 8);
        imagecopyresampled($small, $image, 0, 0, 0, 0, 8, 8, imagesx($image), imagesy($image));
        imagedestroy($image);

        $pixels = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $rgb = imagecolorat($small, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $pixels[] = (int) (0.299 * $r + 0.587 * $g + 0.114 * $b);
            }
        }
        imagedestroy($small);

        $mean = array_sum($pixels) / count($pixels);
        $bits = '';
        foreach ($pixels as $pixel) {
            $bits .= $pixel >= $mean ? '1' : '0';
        }

        // Convert 64-bit binary to hex in 4-bit nibbles to avoid float precision loss
        $hex = '';
        for ($i = 0; $i < 64; $i += 4) {
            $hex .= dechex(bindec(substr($bits, $i, 4)));
        }

        return $hex;
    }
}
