<?php

namespace Database\Seeders;

use App\Enums\CrisisType;
use App\Enums\DamageLevel;
use App\Enums\InfrastructureType;
use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeds realistic damage reports against the Accra demo crisis so the
 * heatmap, analyst dashboard, AND the anonymous re-find loop all render
 * meaningfully on first load.
 *
 * Three "demo persona" device fingerprints are stable UUIDs documented
 * in `docs/submission/evaluator-access.md` — a UNDP evaluator can preset
 * one of these as the `rapida_device_fingerprint` cookie in DevTools and
 * see a populated `/my-reports` page (gap-32). Each persona reports from
 * a single district to mirror real reporter behavior.
 *
 * Bulk anonymous reports (no fingerprint) populate the map heatmap and
 * the analyst dashboard but do not appear on `/my-reports` — that is
 * correct: those reports weren't submitted from any specific device.
 *
 * Idempotent: truncates Accra reports at the start so repeated
 * `db:seed --class=DamageReportSeeder` runs produce identical state.
 */
class DamageReportSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Demo persona fingerprints. Each is the value an evaluator pastes
     * into the `rapida_device_fingerprint` cookie to "become" that
     * reporter for the duration of their browsing session. Documented
     * in `docs/submission/evaluator-access.md` so the demo URL is
     * actually walkable, not just observable.
     */
    public const PERSONA_RESIDENT = 'demo-persona-resident-plateau-001';

    public const PERSONA_SHOPKEEPER = 'demo-persona-shopkeeper-kaneshie-002';

    public const PERSONA_VOLUNTEER = 'demo-persona-volunteer-achimota-003';

    /**
     * Three district centres around Accra. Reports are placed within ~0.015°
     * (~1.5 km) of each centre to produce visible heatmap clusters at zoom 11.
     *
     * @var array<int, array{name: string, lat: float, lng: float, persona: string}>
     */
    private const ACCRA_DISTRICTS = [
        ['name' => 'Plateau', 'lat' => 5.5610, 'lng' => -0.1870, 'persona' => self::PERSONA_RESIDENT],
        ['name' => 'Kaneshie', 'lat' => 5.5580, 'lng' => -0.2120, 'persona' => self::PERSONA_SHOPKEEPER],
        ['name' => 'Achimota', 'lat' => 5.6090, 'lng' => -0.2280, 'persona' => self::PERSONA_VOLUNTEER],
    ];

    private const REPORTS_PER_PERSONA = 6;

    private const BULK_ANONYMOUS_REPORTS = 60;

    public function run(): void
    {
        $crisis = Crisis::where('slug', 'accra-flood-2026')->first();
        if (! $crisis) {
            return;
        }

        // Idempotent: clear Accra reports first so re-running the seeder
        // produces a known shape rather than appending duplicates.
        DamageReport::where('crisis_id', $crisis->id)->forceDelete();

        $buildingIds = Building::where('crisis_id', $crisis->id)->pluck('id')->all();
        $infraTypes = array_map(fn ($c) => $c->value, InfrastructureType::cases());
        $crisisTypes = array_map(fn ($c) => $c->value, CrisisType::cases());

        $this->seedPersonaReports($crisis->id, $buildingIds, $infraTypes, $crisisTypes);
        $this->seedBulkAnonymousReports($crisis->id, $buildingIds, $infraTypes, $crisisTypes);
    }

    /**
     * Seed REPORTS_PER_PERSONA reports for each of the three personas.
     * Persona reports cluster tighter (~0.005° / ~500m) since they
     * mimic one person walking through one neighborhood.
     */
    private function seedPersonaReports(string $crisisId, array $buildingIds, array $infraTypes, array $crisisTypes): void
    {
        foreach (self::ACCRA_DISTRICTS as $district) {
            for ($i = 0; $i < self::REPORTS_PER_PERSONA; $i++) {
                $damageLevel = $this->personaDamageLevel($district['persona'], $i);
                $hasAi = $i % 2 === 0;

                DamageReport::factory()->create([
                    'crisis_id' => $crisisId,
                    'building_footprint_id' => $this->maybeBuilding($buildingIds, 0.7),
                    'device_fingerprint_id' => $district['persona'],
                    'damage_level' => $damageLevel,
                    'infrastructure_type' => $infraTypes[array_rand($infraTypes)],
                    'crisis_type' => $crisisTypes[array_rand($crisisTypes)],
                    'latitude' => round($district['lat'] + (mt_rand(-5, 5) / 1000), 6),
                    'longitude' => round($district['lng'] + (mt_rand(-5, 5) / 1000), 6),
                    'location_method' => 'footprint_tap',
                    'completeness_score' => $damageLevel === DamageLevel::Complete->value ? mt_rand(5, 6) : mt_rand(4, 6),
                    'submitted_via' => $district['persona'] === self::PERSONA_VOLUNTEER ? 'web' : (mt_rand(0, 4) === 0 ? 'whatsapp' : 'web'),
                    'submitted_at' => now()->subHours(mt_rand(1, 14 * 24)),
                    'is_flagged' => false,
                    'ai_suggested_level' => $hasAi ? $this->aiSuggestion($damageLevel) : null,
                    'ai_confidence' => $hasAi ? mt_rand(72, 96) / 100 : null,
                    'reporter_tier' => 'device',
                ]);
            }
        }
    }

    /**
     * Seed BULK_ANONYMOUS_REPORTS without device_fingerprint_id. These
     * populate the map and analyst dashboard but, by design, do not
     * appear on /my-reports for any cookie value.
     */
    private function seedBulkAnonymousReports(string $crisisId, array $buildingIds, array $infraTypes, array $crisisTypes): void
    {
        // Damage-level distribution per the written proposal (40/35/25),
        // adjusted to BULK_ANONYMOUS_REPORTS total: 24 / 21 / 15.
        $levels = array_merge(
            array_fill(0, 24, DamageLevel::Minimal->value),
            array_fill(0, 21, DamageLevel::Partial->value),
            array_fill(0, 15, DamageLevel::Complete->value),
        );
        shuffle($levels);

        foreach ($levels as $i => $damageLevel) {
            $district = self::ACCRA_DISTRICTS[$i % count(self::ACCRA_DISTRICTS)];
            $hasAi = mt_rand(0, 1) === 1;

            DamageReport::factory()->create([
                'crisis_id' => $crisisId,
                'building_footprint_id' => $this->maybeBuilding($buildingIds, 0.6),
                'damage_level' => $damageLevel,
                'infrastructure_type' => $infraTypes[array_rand($infraTypes)],
                'crisis_type' => $crisisTypes[array_rand($crisisTypes)],
                'latitude' => round($district['lat'] + (mt_rand(-15, 15) / 1000), 6),
                'longitude' => round($district['lng'] + (mt_rand(-15, 15) / 1000), 6),
                'location_method' => 'coordinate_only',
                'completeness_score' => $damageLevel === DamageLevel::Complete->value ? mt_rand(4, 6) : mt_rand(3, 5),
                'submitted_via' => mt_rand(0, 9) === 0 ? 'whatsapp' : 'web',
                'submitted_at' => now()->subMinutes(mt_rand(0, 14 * 24 * 60)),
                'is_flagged' => mt_rand(0, 19) === 0,
                'ai_suggested_level' => $hasAi ? $this->aiSuggestion($damageLevel) : null,
                'ai_confidence' => $hasAi ? mt_rand(65, 95) / 100 : null,
                'reporter_tier' => 'anonymous',
            ]);
        }
    }

    /**
     * Persona-flavoured damage distribution. Resident reports fewer
     * complete-collapse cases (their own neighborhood, mostly partial);
     * shopkeeper reports skew toward commercial damage (partial/complete
     * mix); volunteer covers the full range across a wider radius.
     */
    private function personaDamageLevel(string $persona, int $i): string
    {
        $palettes = [
            self::PERSONA_RESIDENT => [DamageLevel::Minimal, DamageLevel::Partial, DamageLevel::Partial, DamageLevel::Minimal, DamageLevel::Partial, DamageLevel::Complete],
            self::PERSONA_SHOPKEEPER => [DamageLevel::Partial, DamageLevel::Complete, DamageLevel::Partial, DamageLevel::Complete, DamageLevel::Partial, DamageLevel::Minimal],
            self::PERSONA_VOLUNTEER => [DamageLevel::Minimal, DamageLevel::Partial, DamageLevel::Complete, DamageLevel::Minimal, DamageLevel::Partial, DamageLevel::Complete],
        ];

        return ($palettes[$persona][$i] ?? DamageLevel::Partial)->value;
    }

    /**
     * Simulate AI agreeing with the human ~75% of the time and
     * disagreeing by one severity step ~25% — so the analyst dashboard
     * has both "AI matched human" and "AI suggested different" cases
     * to demonstrate the human-in-the-loop story.
     */
    private function aiSuggestion(string $humanLevel): string
    {
        if (mt_rand(0, 3) > 0) {
            return $humanLevel;
        }

        return match ($humanLevel) {
            DamageLevel::Minimal->value => DamageLevel::Partial->value,
            DamageLevel::Complete->value => DamageLevel::Partial->value,
            default => mt_rand(0, 1) ? DamageLevel::Minimal->value : DamageLevel::Complete->value,
        };
    }

    /**
     * Return a building ID with the given probability, else null.
     * Reports without a building become "orphans" the operator can
     * snap server-side (gap-38 territory).
     */
    private function maybeBuilding(array $buildingIds, float $probability): ?string
    {
        if ($buildingIds === [] || mt_rand(0, 99) / 100 >= $probability) {
            return null;
        }

        return $buildingIds[array_rand($buildingIds)];
    }
}
