<?php

namespace App\Services;

use App\Models\Building;
use App\Models\DamageReport;

class CanonicalReportService
{
    public function recompute(Building $building): void
    {
        $canonical = DamageReport::where('building_footprint_id', $building->id)
            ->orderByDesc('completeness_score')
            ->orderByDesc('submitted_at')
            ->first();

        $building->update([
            'canonical_report_id' => $canonical?->id,
            'canonical_damage_level' => $canonical?->damage_level instanceof \App\Enums\DamageLevel
                ? $canonical->damage_level->value
                : $canonical?->damage_level,
            'report_count' => DamageReport::where('building_footprint_id', $building->id)->count(),
            'last_updated_at' => now(),
        ]);
    }
}
