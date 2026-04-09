<?php

namespace App\Services;

use App\Models\Building;
use App\Models\DamageReport;

class ProgressRingService
{
    public function coverage(string $crisisId, ?string $h3CellId = null): array
    {
        $buildingQuery = Building::where('crisis_id', $crisisId);
        $reportQuery = DamageReport::where('crisis_id', $crisisId)
            ->where('is_flagged', false)
            ->whereNotNull('building_footprint_id');

        if ($h3CellId) {
            $reportQuery->where('h3_cell_id', $h3CellId);
        }

        $totalBuildings = $buildingQuery->count();
        $reportedBuildings = $reportQuery->distinct('building_footprint_id')->count('building_footprint_id');

        $percentage = $totalBuildings > 0
            ? min(100, round(($reportedBuildings / $totalBuildings) * 100))
            : 0;

        return [
            'total' => $totalBuildings,
            'reported' => $reportedBuildings,
            'percentage' => $percentage,
        ];
    }

    public function leaderboard(string $crisisId, int $limit = 10): array
    {
        return DamageReport::where('crisis_id', $crisisId)
            ->where('is_flagged', false)
            ->whereNotNull('account_id')
            ->selectRaw('account_id, COUNT(*) as report_count')
            ->groupBy('account_id')
            ->orderByDesc('report_count')
            ->limit($limit)
            ->get()
            ->map(fn ($row, $index) => [
                'rank' => $index + 1,
                'account_id' => $row->account_id,
                'report_count' => (int) $row->report_count,
            ])
            ->toArray();
    }
}
