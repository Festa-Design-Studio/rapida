<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiHeatmapController extends Controller
{
    public function index(Request $request, string $slug): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $cells = DamageReport::where('crisis_id', $crisis->id)
            ->where('is_flagged', false)
            ->whereNotNull('h3_cell_id')
            ->groupBy('h3_cell_id')
            ->select([
                'h3_cell_id',
                DB::raw('COUNT(*) as report_count'),
                DB::raw("SUM(CASE WHEN damage_level = 'minimal' THEN 1 ELSE 0 END) as minimal_count"),
                DB::raw("SUM(CASE WHEN damage_level = 'partial' THEN 1 ELSE 0 END) as partial_count"),
                DB::raw("SUM(CASE WHEN damage_level = 'complete' THEN 1 ELSE 0 END) as complete_count"),
            ])
            ->get()
            ->map(fn ($cell) => [
                'cell_id' => $cell->h3_cell_id,
                'report_count' => (int) $cell->report_count,
                'minimal' => (int) $cell->minimal_count,
                'partial' => (int) $cell->partial_count,
                'complete' => (int) $cell->complete_count,
                'dominant' => match (true) {
                    $cell->complete_count >= $cell->partial_count && $cell->complete_count >= $cell->minimal_count => 'complete',
                    $cell->partial_count >= $cell->minimal_count => 'partial',
                    default => 'minimal',
                },
            ]);

        return response()->json(['cells' => $cells, 'crisis_slug' => $slug])
            ->header('Cache-Control', 'public, max-age=30');
    }
}
