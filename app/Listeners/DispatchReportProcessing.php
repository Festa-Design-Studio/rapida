<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Jobs\CalculateTrustedDevice;
use App\Jobs\ClassifyDamageWithAI;
use App\Jobs\ProcessPhotoUpload;
use App\Jobs\TranslateDescription;
use App\Jobs\UpdateCanonicalReport;
use App\Services\CircuitBreakerService;
use App\Services\ConflictModeService;

class DispatchReportProcessing
{
    public function __construct(
        private readonly CircuitBreakerService $circuitBreaker,
        private readonly ConflictModeService $conflictMode,
    ) {}

    public function handle(ReportSubmitted $event): void
    {
        $report = $event->report;
        $report->loadMissing('crisis');
        $isConflict = $report->crisis !== null && $this->conflictMode->isConflict($report->crisis);
        $pressure = request()?->attributes?->get('queue_pressure', 'normal') ?? 'normal';

        // Always dispatch — core report processing (never skipped)
        ProcessPhotoUpload::dispatch($report)->onQueue('photos');
        UpdateCanonicalReport::dispatch($report)->onQueue('default');

        // Trusted device calculation requires a fingerprint, which conflict mode
        // already nullifies via ConflictModeService::applyToSubmission. The
        // fingerprint check below is therefore the conflict-mode gate too.
        if ($report->device_fingerprint_id) {
            CalculateTrustedDevice::dispatch($report)->onQueue('default');
        }

        // AI classification posts the photo URL to an external sidecar. In
        // conflict-mode crises this is a real privacy leak — even pseudonymous
        // photos leave the controlled environment. Skip entirely when conflict
        // context is on (PRD V2 Persona E).
        if (! $isConflict && $pressure === 'normal' && $this->circuitBreaker->isAvailable('ai')) {
            ClassifyDamageWithAI::dispatch($report->photo_url, $report->id)
                ->onQueue('ai');
        }

        // Translation talks to LibreTranslate; same external-egress concern.
        if (! $isConflict && $report->description && in_array($pressure, ['normal', 'moderate'])) {
            if ($this->circuitBreaker->isAvailable('libretranslate')) {
                TranslateDescription::dispatch($report)->onQueue('translation');
            }
        }
    }
}
