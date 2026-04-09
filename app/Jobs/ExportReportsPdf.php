<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Models\Crisis;
use App\Models\DamageReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportReportsPdf implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $crisisId,
        public ?string $damageFilter = null,
    ) {}

    public function handle(): string
    {
        $crisis = Crisis::findOrFail($this->crisisId);

        $query = DamageReport::where('crisis_id', $this->crisisId);

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        $reports = $query->get();

        $totalReports = $reports->count();

        $damageBreakdown = [
            'minimal' => $reports->filter(fn ($r) => ($r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level) === 'minimal')->count(),
            'partial' => $reports->filter(fn ($r) => ($r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level) === 'partial')->count(),
            'complete' => $reports->filter(fn ($r) => ($r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level) === 'complete')->count(),
        ];

        $dateRange = [
            'start' => $reports->min('submitted_at')?->format('Y-m-d'),
            'end' => $reports->max('submitted_at')?->format('Y-m-d'),
        ];

        $topH3Cells = $reports
            ->whereNotNull('h3_cell_id')
            ->groupBy('h3_cell_id')
            ->map(fn ($group) => $group->count())
            ->sortDesc()
            ->take(5);

        $pdf = Pdf::loadView('exports.pdf-summary', [
            'crisis' => $crisis,
            'totalReports' => $totalReports,
            'damageBreakdown' => $damageBreakdown,
            'dateRange' => $dateRange,
            'topH3Cells' => $topH3Cells,
        ]);

        $filename = 'exports/rapida-summary-'.now()->format('Y-m-d-His').'.pdf';

        if (! is_dir(storage_path('app/exports'))) {
            mkdir(storage_path('app/exports'), 0755, true);
        }

        Storage::put($filename, $pdf->output());

        return $filename;
    }
}
