<?php

namespace App\DataTransferObjects;

use App\Models\Crisis;

class SubmitReportData
{
    public function __construct(
        public Crisis $crisis,
        public ?string $photoPath = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $w3wCode = null,
        public ?string $landmarkText = null,
        public ?string $damageLevel = null,
        public ?string $infrastructureType = null,
        public ?string $crisisType = null,
        public ?string $infrastructureName = null,
        public ?bool $debrisRequired = null,
        public ?string $description = null,
        public ?string $deviceFingerprintId = null,
        public ?string $idempotencyKey = null,
        public ?string $accountId = null,
        public ?string $buildingFootprintId = null,
        public ?string $locationMethod = 'coordinate_only',
        public string $submittedVia = 'web',
        public string $reporterTier = 'anonymous',
        public bool $photoGuidanceShown = false,
        public array $moduleResponses = [],
        public mixed $photoFile = null,
        public ?string $photoUrl = null,
        // Gap-51: optional additional photos when crisis.multi_photo_enabled
        // is true. Each entry is either a temporary upload (UploadedFile) or
        // a remote URL string (e.g., from WhatsApp MediaUrl1, MediaUrl2...).
        // Capped by crisis.multi_photo_max in ReportSubmissionService.
        public array $additionalPhotoFiles = [],
        public array $additionalPhotoUrls = [],
    ) {}
}
