<?php

namespace App\Jobs;

use App\Enums\DamageLevel;
use App\Models\DamageReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ExportReportsGeoJson implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $crisisId,
        public ?string $damageFilter = null,
    ) {}

    public function handle(): string
    {
        $query = DamageReport::where('crisis_id', $this->crisisId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($this->damageFilter) {
            $query->where('damage_level', $this->damageFilter);
        }

        $features = $query->get()->map(fn ($r) => [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Point',
                'coordinates' => [(float) $r->longitude, (float) $r->latitude],
            ],
            'properties' => [
                'report_id' => $r->id,
                'damage_level' => $r->damage_level instanceof DamageLevel ? $r->damage_level->value : $r->damage_level,
                'infrastructure_type' => $r->infrastructure_type,
                'submitted_at' => $r->submitted_at?->toIso8601String(),
                'completeness_score' => $r->completeness_score,
            ],
        ]);

        $geojson = json_encode([
            'type' => 'FeatureCollection',
            'features' => $features,
        ], JSON_PRETTY_PRINT);

        $filename = 'exports/rapida-reports-'.now()->format('Y-m-d-His').'.geojson';
        Storage::put($filename, $geojson);

        return $filename;
    }
}
