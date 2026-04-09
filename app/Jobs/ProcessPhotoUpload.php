<?php

namespace App\Jobs;

use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPhotoUpload implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DamageReport $report
    ) {}

    public function handle(): void
    {
        if (! $this->report->photo_url || ! str_starts_with($this->report->photo_url, 'photos/')) {
            return;
        }

        $path = storage_path('app/public/'.$this->report->photo_url);
        if (! file_exists($path)) {
            return;
        }

        // Strip EXIF by re-encoding the image
        $imageInfo = getimagesize($path);
        if (! $imageInfo) {
            return;
        }

        $mime = $imageInfo['mime'];
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            default => null,
        };

        if (! $image) {
            return;
        }

        // Re-save without EXIF metadata
        match ($mime) {
            'image/jpeg' => imagejpeg($image, $path, 85),
            'image/png' => imagepng($image, $path),
            'image/webp' => imagewebp($image, $path, 85),
            default => null,
        };

        imagedestroy($image);

        // Update photo hash after stripping
        $this->report->update([
            'photo_hash' => hash_file('sha256', $path),
        ]);

        Log::info("EXIF stripped for report {$this->report->id}");
    }
}
