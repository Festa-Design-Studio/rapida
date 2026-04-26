<?php

namespace Database\Seeders;

use App\Enums\CrisisType;
use App\Enums\DamageLevel;
use App\Enums\InfrastructureType;
use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Database\Seeder;

/**
 * Seeds ~80 realistic damage reports against the Accra demo crisis so the
 * heatmap and analyst dashboard render meaningfully on first load. Reports
 * cluster around three Accra districts (Plateau, Achimota, Kaneshie) and
 * are distributed across the past 14 days at the documented damage-level
 * mix from the proposal (40/35/25 minimal/partial/complete).
 *
 * Idempotent by report count: skipped if the Accra crisis already has at
 * least 50 reports.
 */
class DamageReportSeeder extends Seeder
{
    /**
     * Three district centres around Accra. Reports are placed within ~0.015°
     * (~1.5 km) of each centre to produce visible heatmap clusters at zoom 11.
     *
     * @var array<int, array{name: string, lat: float, lng: float}>
     */
    private const ACCRA_DISTRICTS = [
        ['name' => 'Plateau', 'lat' => 5.5610, 'lng' => -0.1870],
        ['name' => 'Achimota', 'lat' => 5.6090, 'lng' => -0.2280],
        ['name' => 'Kaneshie', 'lat' => 5.5580, 'lng' => -0.2120],
    ];

    public function run(): void
    {
        $crisis = Crisis::where('slug', 'accra-flood-2026')->first();
        if (! $crisis) {
            return;
        }

        if (DamageReport::where('crisis_id', $crisis->id)->count() >= 50) {
            return;
        }

        $buildingIds = Building::where('crisis_id', $crisis->id)->pluck('id')->all();
        $infraTypes = array_map(fn ($c) => $c->value, InfrastructureType::cases());
        $crisisTypes = array_map(fn ($c) => $c->value, CrisisType::cases());

        // Damage-level distribution per the written proposal.
        $levels = array_merge(
            array_fill(0, 32, DamageLevel::Minimal->value),    // 40%
            array_fill(0, 28, DamageLevel::Partial->value),    // 35%
            array_fill(0, 20, DamageLevel::Complete->value),   // 25%
        );
        shuffle($levels);

        foreach ($levels as $i => $damageLevel) {
            $district = self::ACCRA_DISTRICTS[$i % count(self::ACCRA_DISTRICTS)];

            // Jitter the coordinates within ~0.015° for the cluster effect.
            $lat = $district['lat'] + (mt_rand(-15, 15) / 1000);
            $lng = $district['lng'] + (mt_rand(-15, 15) / 1000);

            DamageReport::factory()->create([
                'crisis_id' => $crisis->id,
                'building_footprint_id' => $buildingIds === [] ? null : $buildingIds[array_rand($buildingIds)],
                'damage_level' => $damageLevel,
                'infrastructure_type' => $infraTypes[array_rand($infraTypes)],
                'crisis_type' => $crisisTypes[array_rand($crisisTypes)],
                'latitude' => round($lat, 6),
                'longitude' => round($lng, 6),
                'completeness_score' => $damageLevel === DamageLevel::Complete->value ? mt_rand(5, 6) : mt_rand(3, 6),
                'submitted_via' => mt_rand(0, 9) === 0 ? 'whatsapp' : 'web', // ~10% whatsapp
                'submitted_at' => now()->subMinutes(mt_rand(0, 14 * 24 * 60)),
                'is_flagged' => mt_rand(0, 19) === 0, // ~5% flagged for verification queue
            ]);
        }
    }
}
