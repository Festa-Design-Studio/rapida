<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Crisis;
use App\Models\Landmark;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->call(CrisisSeeder::class);
        $this->call(UndpUserSeeder::class);

        $this->seedAccraDemoData();
        $this->seedConflictDemoData();
    }

    private function seedAccraDemoData(): void
    {
        $crisis = Crisis::where('slug', 'accra-flood-2026')->first();

        if (! $crisis) {
            return;
        }

        $undpUser = UndpUser::first();

        // Accra landmarks
        $landmarks = [
            ['name' => 'Makola Market', 'type' => 'market', 'lat' => 5.5560, 'lng' => -0.1969],
            ['name' => 'Korle Bu Hospital', 'type' => 'hospital', 'lat' => 5.5500, 'lng' => -0.2050],
            ['name' => 'Independence Square', 'type' => 'public', 'lat' => 5.5610, 'lng' => -0.1870],
            ['name' => 'Kaneshie Market', 'type' => 'market', 'lat' => 5.5580, 'lng' => -0.2120],
            ['name' => 'Osu Castle', 'type' => 'government', 'lat' => 5.5650, 'lng' => -0.1780],
            ['name' => 'National Theatre', 'type' => 'public', 'lat' => 5.5490, 'lng' => -0.1850],
        ];

        // Buildings
        $buildingLocations = [
            ['lat' => 5.5560, 'lng' => -0.1969, 'name' => 'Makola Market'],
            ['lat' => 5.5500, 'lng' => -0.2050, 'name' => 'Korle Bu Hospital'],
            ['lat' => 5.5610, 'lng' => -0.1870, 'name' => 'Independence Square'],
            ['lat' => 5.5580, 'lng' => -0.2120, 'name' => 'Kaneshie Market'],
            ['lat' => 5.5650, 'lng' => -0.1780, 'name' => 'Osu Castle'],
            ['lat' => 5.5490, 'lng' => -0.1850, 'name' => 'National Theatre'],
            ['lat' => 5.5530, 'lng' => -0.2000, 'name' => 'Accra Central Mosque'],
            ['lat' => 5.5700, 'lng' => -0.2070, 'name' => 'University of Ghana Gate'],
            ['lat' => 5.5620, 'lng' => -0.1950, 'name' => 'Accra Mall'],
            ['lat' => 5.5670, 'lng' => -0.1900, 'name' => 'Ridge Hospital'],
        ];

        foreach ($buildingLocations as $loc) {
            Building::create([
                'crisis_id' => $crisis->id,
                'ms_building_id' => 'demo-'.Str::slug($loc['name']),
            ]);
        }

        if ($undpUser) {
            foreach ($landmarks as $lm) {
                Landmark::create([
                    'crisis_id' => $crisis->id,
                    'name' => $lm['name'],
                    'type' => $lm['type'],
                    'latitude' => $lm['lat'],
                    'longitude' => $lm['lng'],
                    'added_by' => $undpUser->id,
                ]);
            }

            // Seed one recovery outcome for the demo
            RecoveryOutcome::create([
                'crisis_id' => $crisis->id,
                'h3_cell_id' => '882a10c969fffff',
                'message' => '14 homes in Plateau District that were reported through RAPIDA have been added to the emergency shelter program.',
                'triggered_by' => $undpUser->id,
                'triggered_at' => now(),
            ]);
        }
    }

    private function seedConflictDemoData(): void
    {
        $crisis = Crisis::where('slug', 'aleppo-conflict-2026')->first();

        if (! $crisis) {
            return;
        }

        // Minimal seed data for conflict zone — no fingerprints, no accounts
        $buildingLocations = [
            ['lat' => 36.2100, 'lng' => 37.1300, 'name' => 'Old City Market'],
            ['lat' => 36.2000, 'lng' => 37.1600, 'name' => 'Al-Madina Hospital'],
            ['lat' => 36.1950, 'lng' => 37.1450, 'name' => 'Community Centre'],
        ];

        foreach ($buildingLocations as $loc) {
            Building::create([
                'crisis_id' => $crisis->id,
                'ms_building_id' => 'demo-'.Str::slug($loc['name']),
            ]);
        }
    }
}
