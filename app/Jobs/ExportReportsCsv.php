<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Enums\SubmissionChannel;
use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportReportsCsv implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $crisisId,
        public ?string $damageFilter = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    public function handle(): string
    {
        $query = DamageReport::where('crisis_id', $this->crisisId);

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        if ($this->startDate) {
            $query->where('submitted_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('submitted_at', '<=', $this->endDate);
        }

        $reports = $query->orderByDesc('submitted_at')->get();

        $csv = "report_id,damage_level,infrastructure_type,crisis_type,latitude,longitude,submitted_at,completeness_score,submitted_via,ai_confidence,ai_suggested_level\n";

        foreach ($reports as $r) {
            $damageLevel = $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level;
            $submittedVia = $r->submitted_via instanceof SubmissionChannel ? $r->submitted_via->value : $r->submitted_via;

            $aiSuggestedLevel = $r->ai_suggested_level instanceof DamageLevel ? $r->ai_suggested_level->value : $r->ai_suggested_level;

            $csv .= implode(',', [
                $r->id,
                $damageLevel,
                $r->infrastructure_type,
                $r->crisis_type,
                $r->latitude,
                $r->longitude,
                $r->submitted_at,
                $r->completeness_score,
                $submittedVia,
                $r->ai_confidence,
                $aiSuggestedLevel,
            ])."\n";
        }

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.csv';
        Storage::put($filename, $csv);

        return $filename;
    }
}
