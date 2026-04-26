<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Jobs\CalculateTrustedDevice;
use App\Jobs\ClassifyDamageWithAI;
use App\Jobs\ProcessPhotoUpload;
use App\Jobs\SnapReportToFootprint;
use App\Jobs\TranslateDescription;
use App\Jobs\UpdateCanonicalReport;
use App\Services\CircuitBreakerService;

class DispatchReportProcessing
{
    public function __construct(
        private readonly CircuitBreakerService $circuitBreaker,
    ) {}

    public function handle(ReportSubmitted $event): void
    {
        $report = $event->report;
        $pressure = request()?->attributes?->get('queue_pressure', 'normal') ?? 'normal';

        // Always dispatch — core report processing (never skipped)
        ProcessPhotoUpload::dispatch($report)->onQueue('photos');

        // gap-38: snap orphans (no building_footprint_id) to nearest footprint
        // BEFORE recomputing canonical ranking, so the snap result is in place
        // when UpdateCanonicalReport runs. Both run on the default queue so
        // they execute in dispatch order; SnapReportToFootprint is a no-op
        // when the wizard already snapped.
        SnapReportToFootprint::dispatch($report)->onQueue('default');
        UpdateCanonicalReport::dispatch($report)->onQueue('default');

        // Trusted device calculation (always, unless conflict mode)
        if ($report->device_fingerprint_id) {
            CalculateTrustedDevice::dispatch($report)->onQueue('default');
        }

        // AI classification — skip under moderate+ pressure or if circuit is open
        if ($pressure === 'normal' && $this->circuitBreaker->isAvailable('ai')) {
            ClassifyDamageWithAI::dispatch($report->photo_url, $report->id)
                ->onQueue('ai');
        }

        // Translation — skip under high+ pressure or if circuit is open
        if ($report->description && in_array($pressure, ['normal', 'moderate'])) {
            if ($this->circuitBreaker->isAvailable('libretranslate')) {
                TranslateDescription::dispatch($report)->onQueue('translation');
            }
        }
    }
}
