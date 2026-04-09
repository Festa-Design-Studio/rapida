<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Services\DuplicateDetectionService;

class DetectDuplicateReports
{
    public function __construct(private DuplicateDetectionService $detectionService) {}

    public function handle(ReportSubmitted $event): void
    {
        $this->detectionService->checkForDuplicates($event->report);
    }
}
