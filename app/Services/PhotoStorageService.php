<?php

namespace App\Services;

use App\DataTransferObjects\PhotoResult;
use Illuminate\Http\UploadedFile;
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

    public function placeholder(): PhotoResult
    {
        return new PhotoResult(
            url: 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
            hash: hash('sha256', 'placeholder'),
            sizeBytes: 0,
        );
    }
}
