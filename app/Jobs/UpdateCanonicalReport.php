<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\DamageReport;
use App\Services\CanonicalReportService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateCanonicalReport implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DamageReport $report
    ) {}

    public function handle(CanonicalReportService $service): void
    {
        if (! $this->report->building_footprint_id) {
            return;
        }

        $building = Building::find($this->report->building_footprint_id);
        if (! $building) {
            return;
        }

        $service->recompute($building);
    }
}
