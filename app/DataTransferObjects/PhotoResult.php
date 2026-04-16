<?php

namespace App\DataTransferObjects;

class PhotoResult
{
    public function __construct(
        public readonly string $url,
        public readonly string $hash,
        public readonly int $sizeBytes,
    ) {}
}
