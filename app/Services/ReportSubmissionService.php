<?php

namespace App\Services;

use App\DataTransferObjects\SubmitReportData;
use App\Events\ReportSubmitted;
use App\Models\DamageReport;
use Illuminate\Support\Facades\DB;

class ReportSubmissionService
{
    public function __construct(
        private readonly ConflictModeService $conflictMode,
        private readonly CompletenessScoreService $completeness,
        private readonly PhotoStorageService $photoStorage,
        private readonly ModularFieldService $modularFields,
    ) {}

    public function submit(SubmitReportData $data): DamageReport
    {
        // 1. Apply conflict mode rules
        $this->conflictMode->applyToSubmission($data);

        // 2. Idempotency check
        if ($data->idempotencyKey) {
            $existing = DamageReport::where('idempotency_key', $data->idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        // 3. Detect reporter tier
        $data->reporterTier = $this->resolveReporterTier($data);

        return DB::transaction(function () use ($data) {
            // 4. Handle photo
            $photoResult = null;
            if ($data->photoFile) {
                try {
                    $photoResult = $this->photoStorage->store($data->photoFile);
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            if (! $photoResult) {
                $photoResult = $this->photoStorage->placeholder();
            }

            // 5. Create damage report
            $report = DamageReport::create([
                'crisis_id' => $data->crisis->id,
                'building_footprint_id' => $data->buildingFootprintId,
                'account_id' => $data->accountId,
                'device_fingerprint_id' => $data->deviceFingerprintId,
                'photo_url' => $photoResult->url,
                'photo_hash' => $photoResult->hash,
                'photo_size_bytes' => $photoResult->sizeBytes,
                'photo_guidance_shown' => $data->photoGuidanceShown,
                'damage_level' => $data->damageLevel ?: 'partial',
                'infrastructure_type' => $data->infrastructureType ?: 'other',
                'crisis_type' => $data->crisisType ?: 'flood',
                'infrastructure_name' => $data->infrastructureName,
                'debris_required' => $data->debrisRequired,
                'location_method' => $data->locationMethod,
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'w3w_code' => $data->w3wCode,
                'landmark_text' => $data->landmarkText,
                'description' => $data->description,
                'submitted_via' => $data->submittedVia,
                'reporter_tier' => $data->reporterTier,
                'idempotency_key' => $data->idempotencyKey,
                'submitted_at' => now(),
                'synced_at' => now(),
                'is_flagged' => false,
            ]);

            // 6. Store modular field responses
            $this->modularFields->storeResponses($report, $data->moduleResponses);

            // 7. Score completeness
            $report->update([
                'completeness_score' => $this->completeness->score($report),
            ]);

            // 8. Dispatch event (AI, translation, canonical, badges)
            try {
                event(new ReportSubmitted($report));
            } catch (\Throwable $e) {
                report($e);
            }

            return $report;
        });
    }

    private function resolveReporterTier(SubmitReportData $data): string
    {
        if ($this->conflictMode->isConflict($data->crisis)) {
            return 'anonymous';
        }

        if ($data->accountId) {
            return 'account';
        }

        if ($data->deviceFingerprintId) {
            return 'device';
        }

        return 'anonymous';
    }
}
