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

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.csv';
        $tempPath = sys_get_temp_dir().'/rapida-export-'.uniqid().'.csv';

        $handle = fopen($tempPath, 'w');

        fputcsv($handle, [
            'report_id', 'damage_level', 'infrastructure_type', 'crisis_type',
            'latitude', 'longitude', 'submitted_at', 'completeness_score',
            'submitted_via', 'ai_confidence', 'ai_suggested_level',
        ]);

        foreach ($query->orderByDesc('submitted_at')->cursor() as $r) {
            $damageLevel = $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level;
            $submittedVia = $r->submitted_via instanceof SubmissionChannel ? $r->submitted_via->value : $r->submitted_via;
            $aiSuggestedLevel = $r->ai_suggested_level instanceof DamageLevel ? $r->ai_suggested_level->value : $r->ai_suggested_level;

            fputcsv($handle, [
                $r->id, $damageLevel, $r->infrastructure_type, $r->crisis_type,
                $r->latitude, $r->longitude, $r->submitted_at, $r->completeness_score,
                $submittedVia, $r->ai_confidence, $aiSuggestedLevel,
            ]);
        }

        fclose($handle);

        Storage::put($filename, file_get_contents($tempPath));
        unlink($tempPath);

        return $filename;
    }
}
