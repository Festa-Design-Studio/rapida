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

    /**
     * Gap-49: count reports in the same H3 cell as the just-submitted report.
     * Powers the "Your report joins N others from your area" line on the
     * submission-confirmation page (PRD V2 §4 state-1 Impact Counter).
     *
     * Cached briefly (60s) so repeat reloads of the confirmation page don't
     * re-run the query, but kept short enough that a fresh report from the
     * same area updates the visible count quickly.
     */
    public function reportsInH3Cell(string $crisisId, ?string $h3CellId): int
    {
        if ($h3CellId === null) {
            return 0;
        }

        return Cache::remember(
            "analytics:{$crisisId}:h3_cell:{$h3CellId}",
            60,
            fn () => DamageReport::where('crisis_id', $crisisId)
                ->where('h3_cell_id', $h3CellId)
                ->count(),
        );
    }

    /**
     * Gap-49: total reports an account has submitted to a crisis.
     * Powers the personalised "your N reports" copy on the engagement panel
     * for logged-in reporters. Returns 0 for anonymous reporters.
     */
    public function reportsByAccount(string $crisisId, ?string $accountId): int
    {
        if ($accountId === null) {
            return 0;
        }

        return DamageReport::where('crisis_id', $crisisId)
            ->where('account_id', $accountId)
            ->count();
    }

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
        $data = Cache::remember(
            "analytics:{$crisisId}:by_damage",
            self::CACHE_TTL,
            function () use ($crisisId): array {
                $query = DamageReport::query()
                    ->selectRaw('damage_level, count(*) as count')
                    ->groupBy('damage_level');

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->pluck('count', 'damage_level')->toArray();
            }
        );

        return collect($data);
    }

    /**
     * @return Collection<string, int>
     */
    public function byInfrastructureType(string $crisisId): Collection
    {
        $data = Cache::remember(
            "analytics:{$crisisId}:by_infra",
            self::CACHE_TTL,
            function () use ($crisisId): array {
                $query = DamageReport::query()
                    ->selectRaw('infrastructure_type, count(*) as count')
                    ->groupBy('infrastructure_type');

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->pluck('count', 'infrastructure_type')->toArray();
            }
        );

        return collect($data);
    }

    public function reportsByDay(string $crisisId, int $limit = 14): Collection
    {
        $data = Cache::remember(
            "analytics:{$crisisId}:by_day:{$limit}",
            self::CACHE_TTL,
            function () use ($crisisId, $limit): array {
                $query = DamageReport::query()
                    ->selectRaw('DATE(submitted_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->limit($limit);

                if ($crisisId) {
                    $query->where('crisis_id', $crisisId);
                }

                return $query->get()->toArray();
            }
        );

        // Convert arrays back to objects so blade can use $day->count syntax
        return collect($data)->map(fn ($item) => (object) $item);
    }

    /**
     * @return EloquentCollection<int, Building>
     */
    public function topBuildings(string $crisisId, int $limit = 10): EloquentCollection
    {
        // Not cached — returns Eloquent models with UUIDs that don't survive serialization
        $query = Building::where('report_count', '>', 0)
            ->orderByDesc('report_count')
            ->limit($limit);

        if ($crisisId) {
            $query->where('crisis_id', $crisisId);
        }

        return $query->get();
    }

    /**
     * @return EloquentCollection<int, DamageReport>
     */
    public function recentReports(string $crisisId, int $limit = 10): EloquentCollection
    {
        // Not cached — returns Eloquent models with UUIDs that don't survive serialization
        $query = DamageReport::query()
            ->orderByDesc('submitted_at')
            ->limit($limit);

        if ($crisisId) {
            $query->where('crisis_id', $crisisId);
        }

        return $query->get();
    }
}
