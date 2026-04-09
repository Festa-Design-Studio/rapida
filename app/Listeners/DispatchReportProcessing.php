<?php

namespace App\Listeners;

use App\Events\ReportSubmitted;
use App\Jobs\ProcessPhotoUpload;
use App\Jobs\TranslateDescription;
use App\Jobs\UpdateCanonicalReport;

class DispatchReportProcessing
{
    public function handle(ReportSubmitted $event): void
    {
        ProcessPhotoUpload::dispatch($event->report);
        UpdateCanonicalReport::dispatch($event->report);

        if ($event->report->description) {
            TranslateDescription::dispatch($event->report);
        }
    }
}
