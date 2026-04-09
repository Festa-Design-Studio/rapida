<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Crisis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApiBuildingController extends Controller
{
    public function footprints(Request $request, string $slug): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $query = Building::where('crisis_id', $crisis->id);

        // Only use PostGIS spatial queries on PostgreSQL
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            $query->whereNotNull('footprint_geom')
                ->select([
                    'id', 'ms_building_id', 'canonical_damage_level', 'report_count',
                    DB::raw('ST_AsGeoJSON(footprint_geom) AS geom_json'),
                ]);

            if ($bbox = $request->query('bbox')) {
                [$minLng, $minLat, $maxLng, $maxLat] = explode(',', $bbox);
                $query->whereRaw(
                    'footprint_geom && ST_MakeEnvelope(?, ?, ?, ?, 4326)',
                    [$minLng, $minLat, $maxLng, $maxLat]
                );
            }

            $features = $query->get()->map(fn ($b) => [
                'type' => 'Feature',
                'id' => $b->id,
                'geometry' => json_decode($b->geom_json),
                'properties' => [
                    'id' => $b->id,
                    'ms_building_id' => $b->ms_building_id,
                    'canonical_damage_level' => $b->canonical_damage_level,
                    'report_count' => $b->report_count,
                ],
            ]);
        } else {
            // SQLite fallback — no geometry, return point-based features
            $features = $query->select(['id', 'ms_building_id', 'canonical_damage_level', 'report_count'])
                ->get()
                ->map(fn ($b) => [
                    'type' => 'Feature',
                    'id' => $b->id,
                    'geometry' => null,
                    'properties' => [
                        'id' => $b->id,
                        'ms_building_id' => $b->ms_building_id,
                        'canonical_damage_level' => $b->canonical_damage_level,
                        'report_count' => $b->report_count,
                    ],
                ]);
        }

        return response()->json(['type' => 'FeatureCollection', 'features' => $features])
            ->header('Cache-Control', 'public, max-age=30');
    }
}
