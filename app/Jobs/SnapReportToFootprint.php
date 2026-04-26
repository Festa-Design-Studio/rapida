<?php

namespace App\Jobs;

use App\Models\DamageReport;
use App\Services\BuildingFootprintService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Server-side spatial snap for orphaned reports — gap-38 fix.
 *
 * The wizard sends an explicit building_footprint_id when the user tapped
 * a footprint on the map. But reports submitted via API, WhatsApp, or any
 * fallback location method (GPS coord only, w3w, landmark text) arrive
 * with building_footprint_id = null. Without this job those reports never
 * participate in canonical-report ranking and never appear on the
 * building-footprint pin layer — they show up only as raw points.
 *
 * This job runs after ReportSubmitted and attempts to associate orphans
 * with the nearest building footprint within 100m via PostGIS. If the
 * snap finds nothing, the report stays unassociated (correct behaviour
 * — informal settlements without footprint coverage shouldn't be
 * forced into the wrong building).
 *
 * No-op when:
 *   - report already has a building_footprint_id (wizard already snapped)
 *   - report has no lat/lng (location method was landmark_text only)
 *   - underlying database isn't PostgreSQL/PostGIS (SQLite test fallback)
 */
class SnapReportToFootprint implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public DamageReport $report,
    ) {}

    public function handle(BuildingFootprintService $footprintService): void
    {
        if ($this->report->building_footprint_id !== null) {
            return; // Wizard already snapped — leave alone.
        }

        if ($this->report->latitude === null || $this->report->longitude === null) {
            return; // Landmark-text-only reports have no coords to snap.
        }

        $building = $footprintService->snapToNearest(
            (float) $this->report->latitude,
            (float) $this->report->longitude,
            (string) $this->report->crisis_id,
        );

        if ($building === null) {
            return; // No footprint within 100m — leave unassociated.
        }

        $this->report->update(['building_footprint_id' => $building->id]);
    }
}
