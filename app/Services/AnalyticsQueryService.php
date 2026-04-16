<?php

namespace App\Services;

use App\Models\Building;
use App\Models\DamageReport;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AnalyticsQueryService
{
    private const CACHE_TTL = 300; // 5 minutes

    public function totalReports(string $crisisId): int
    {
        return Cache::remember(
            "analytics:{$crisisId}:total",
            self::CACHE_TTL,
            function () use ($crisisId): int {
                $query = DamageReport::query();

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->count();
            }
        );
    }

    /**
     * @return Collection<string, int>
     */
    public function byDamageLevel(string $crisisId): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:by_damage",
            self::CACHE_TTL,
            function () use ($crisisId): Collection {
                $query = DamageReport::query()
                    ->selectRaw('damage_level, count(*) as count')
                    ->groupBy('damage_level');

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->pluck('count', 'damage_level');
            }
        );
    }

    /**
     * @return Collection<string, int>
     */
    public function byInfrastructureType(string $crisisId): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:by_infra",
            self::CACHE_TTL,
            function () use ($crisisId): Collection {
                $query = DamageReport::query()
                    ->selectRaw('infrastructure_type, count(*) as count')
                    ->groupBy('infrastructure_type');

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->pluck('count', 'infrastructure_type');
            }
        );
    }

    public function reportsByDay(string $crisisId, int $limit = 14): Collection
    {
        return Cache::remember(
            "analytics:{$crisisId}:by_day:{$limit}",
            self::CACHE_TTL,
            function () use ($crisisId, $limit): Collection {
                $query = DamageReport::query()
                    ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit($limit);

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->get();
            }
        );
    }

    /**
     * @return EloquentCollection<int, Building>
     */
    public function topBuildings(string $crisisId, int $limit = 10): EloquentCollection
    {
        return Cache::remember(
            "analytics:{$crisisId}:top_buildings:{$limit}",
            self::CACHE_TTL,
            function () use ($crisisId, $limit): EloquentCollection {
                $query = Building::where('report_count', '>', 0)
                    ->orderByDesc('report_count')
                    ->limit($limit);

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->get();
            }
        );
    }

    /**
     * @return EloquentCollection<int, DamageReport>
     */
    public function recentReports(string $crisisId, int $limit = 10): EloquentCollection
    {
        return Cache::remember(
            "analytics:{$crisisId}:recent:{$limit}",
            self::CACHE_TTL,
            function () use ($crisisId, $limit): EloquentCollection {
                $query = DamageReport::query()
                    ->orderByDesc('submitted_at')
                    ->limit($limit);

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->get();
            }
        );
    }
}
