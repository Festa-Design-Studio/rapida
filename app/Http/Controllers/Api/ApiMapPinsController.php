<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiMapPinsController extends Controller
{
    public function index(Request $request, string $slug): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->where('status', 'active')->firstOrFail();

        $query = DamageReport::where('crisis_id', $crisis->id)
            ->where('is_flagged', false)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select(['id', 'damage_level', 'infrastructure_type', 'submitted_at', 'photo_url', 'latitude', 'longitude']);

        if ($since = $request->query('since')) {
            $query->where('submitted_at', '>', $since);
        }

        if ($damageLevel = $request->query('damage_level')) {
            $query->whereIn('damage_level', explode(',', $damageLevel));
        }

        $features = $query->latest('submitted_at')->limit(2000)->get()->map(fn ($r) => [
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [(float) $r->longitude, (float) $r->latitude]],
            'properties' => [
                'report_id' => $r->id,
                'damage_level' => $r->damage_level instanceof \App\Enums\DamageLevel ? $r->damage_level->value : $r->damage_level,
                'infrastructure_type' => $r->infrastructure_type,
                'submitted_at' => $r->submitted_at?->toIso8601String(),
                'photo_url' => $r->photo_url,
            ],
        ]);

        return response()->json(['type' => 'FeatureCollection', 'features' => $features])
            ->header('Cache-Control', 'no-store');
    }
}
