<?php

namespace App\Services;

use App\Models\Building;
use Illuminate\Support\Facades\Schema;

class BuildingFootprintService
{
    public function snapToNearest(float $lat, float $lng, string $crisisId, float $radiusMeters = 100): ?Building
    {
        // On PostgreSQL with PostGIS, use spatial query
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            return Building::where('crisis_id', $crisisId)
                ->whereNotNull('footprint_geom')
                ->whereRaw(
                    'ST_DWithin(footprint_geom::geography, ST_MakePoint(?, ?)::geography, ?)',
                    [$lng, $lat, $radiusMeters]
                )
                ->orderByRaw(
                    'ST_Distance(footprint_geom::geography, ST_MakePoint(?, ?)::geography)',
                    [$lng, $lat]
                )
                ->first();
        }

        // SQLite fallback — no spatial queries, return null
        return null;
    }
}
