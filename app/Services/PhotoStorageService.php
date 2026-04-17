<?php

namespace App\Services;

use App\DataTransferObjects\PhotoResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PhotoStorageService
{
    public function store(UploadedFile|TemporaryUploadedFile $file, string $disk = 'public'): PhotoResult
    {
        $hash = hash_file('sha256', $file->getRealPath());
        $size = $file->getSize();
        $path = $file->storeAs(
            'photos',
            Str::uuid().'.'.$file->guessExtension(),
            $disk
        );

        return new PhotoResult(
            url: $path,
            hash: $hash,
            sizeBytes: $size,
        );
    }

    /**
     * Download a photo from a URL and store it locally.
     *
     * @return PhotoResult|null Returns null on failure so callers can fall back to placeholder().
     */
    public function storeFromUrl(string $url, ?string $authUser = null, ?string $authPass = null, string $disk = 'public'): ?PhotoResult
    {
        try {
            $request = Http::timeout(15);

            if ($authUser && $authPass) {
                $request = $request->withBasicAuth($authUser, $authPass);
            }

            $response = $request->get($url);

            if (! $response->successful()) {
                return null;
            }

            $contentType = $response->header('Content-Type');
            $extension = match (true) {
                str_contains($contentType, 'png') => 'png',
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'gif') => 'gif',
                default => 'jpg',
            };

            $body = $response->body();
            $path = 'photos/'.Str::uuid().'.'.$extension;

            Storage::disk($disk)->put($path, $body);

            return new PhotoResult(
                url: $path,
                hash: hash('sha256', $body),
                sizeBytes: strlen($body),
            );
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public function placeholder(): PhotoResult
    {
        return new PhotoResult(
            url: 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            hash: hash('sha256', 'placeholder'),
            sizeBytes: 0,
        );
    }
}
