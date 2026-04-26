<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crisis;
use App\Services\DangerZoneService;
use Illuminate\Http\JsonResponse;

/**
 * Public read-only API exposing operator-flagged danger zones for a crisis.
 * Cached on the client (PWA service-worker) for offline reach.
 *
 * Returns an empty list when:
 *   - the crisis has danger_zones_enabled=false (operator hasn't opted in), or
 *   - the crisis is in conflict mode (privacy gate)
 *
 * Both gates live in DangerZoneService::isAvailable so this controller stays
 * thin and the policy is testable in isolation.
 */
class DangerZoneController extends Controller
{
    public function __construct(
        private readonly DangerZoneService $dangerZoneService,
    ) {}

    public function index(string $slug): JsonResponse
    {
        $crisis = Crisis::where('slug', $slug)->firstOrFail();

        $zones = $this->dangerZoneService->activeZonesFor($crisis)
            ->map(fn ($zone) => [
                'h3_cell_id' => $zone->h3_cell_id,
                'severity' => $zone->severity,
                'note' => $zone->note,
                'expires_at' => $zone->expires_at?->toIso8601String(),
            ])
            ->values();

        return response()->json([
            'feature_enabled' => $this->dangerZoneService->isAvailable($crisis),
            'zones' => $zones,
        ]);
    }
}
