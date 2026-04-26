<?php

namespace App\Services;

use App\Models\Crisis;
use App\Models\DangerZone;
use Illuminate\Support\Collection;

/**
 * Resolves which danger zones a reporter should see for a given crisis.
 * Two gates:
 *
 *   1. Per-crisis flag (operator-controlled): crises.danger_zones_enabled
 *      must be true. Default false; operators opt in on a per-crisis basis.
 *   2. Conflict-mode privacy gate: any crisis with conflict_context = true
 *      returns no zones, regardless of the flag — surfacing operator-flagged
 *      zones in a conflict crisis would draw attention to those areas, the
 *      opposite of what conflict mode is for.
 *
 * The combination means a conflict-zone reporter never receives a danger-area
 * alert via this service even if an operator forgets to disable the flag.
 *
 * UNDP webinar reference: information-as-service incentives. The danger-zone
 * layer is non-monetary value the reporter receives for showing up.
 */
class DangerZoneService
{
    public function __construct(
        private readonly ConflictModeService $conflictMode,
    ) {}

    /**
     * Returns active (non-expired) danger zones for the crisis, or an empty
     * collection if either gate is closed. Active zones are returned ordered
     * by severity (critical -> warning -> caution) so a UI that shows "the
     * worst nearby" picks the right one first.
     *
     * @return Collection<int, DangerZone>
     */
    public function activeZonesFor(Crisis $crisis): Collection
    {
        if (! $this->isAvailable($crisis)) {
            return collect();
        }

        return DangerZone::query()
            ->where('crisis_id', $crisis->id)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->get()
            ->sortByDesc(fn (DangerZone $z) => $this->severityRank($z->severity))
            ->values();
    }

    /**
     * Returns the active zones whose H3 cell IDs appear in $h3CellIds — the
     * cells the reporter is currently in or near. Caller is responsible for
     * computing the cell ring (h3-js client-side); this service trusts the
     * input and just filters.
     *
     * @param  array<int, string>  $h3CellIds
     * @return Collection<int, DangerZone>
     */
    public function nearbyZones(Crisis $crisis, array $h3CellIds): Collection
    {
        if ($h3CellIds === []) {
            return collect();
        }

        return $this->activeZonesFor($crisis)
            ->filter(fn (DangerZone $z) => in_array($z->h3_cell_id, $h3CellIds, true))
            ->values();
    }

    /**
     * Whether the danger-zones feature is currently surfaced for this crisis.
     * Both gates must be open. Used by the API controller to short-circuit.
     */
    public function isAvailable(Crisis $crisis): bool
    {
        if (! ($crisis->danger_zones_enabled ?? false)) {
            return false;
        }

        if ($this->conflictMode->isConflict($crisis)) {
            return false;
        }

        return true;
    }

    /**
     * Severity ranking for ordering. Higher number = more severe.
     */
    private function severityRank(string $severity): int
    {
        return match ($severity) {
            'critical' => 3,
            'warning' => 2,
            default => 1, // 'caution' or unknown
        };
    }
}
