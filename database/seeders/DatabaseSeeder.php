<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\Landmark;
use App\Models\UndpUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(CrisisSeeder::class);
        $this->call(UndpUserSeeder::class);

        // Seed demo data for the Accra crisis
        $crisis = Crisis::where('slug', 'accra-flood-2026')->first();
        if ($crisis) {
            // Seed demo buildings (Accra landmarks area)
            $buildingLocations = [
                ['lat' => 5.5560, 'lng' => -0.1969, 'name' => 'Makola Market'],
                ['lat' => 5.5500, 'lng' => -0.2050, 'name' => 'Korle Bu Hospital'],
                ['lat' => 5.5610, 'lng' => -0.1870, 'name' => 'Independence Square'],
                ['lat' => 5.5580, 'lng' => -0.2120, 'name' => 'Kaneshie Market'],
                ['lat' => 5.5650, 'lng' => -0.1780, 'name' => 'Osu Castle'],
                ['lat' => 5.5470, 'lng' => -0.1930, 'name' => 'Jamestown Lighthouse'],
                ['lat' => 5.5530, 'lng' => -0.2000, 'name' => 'Accra Central Mosque'],
                ['lat' => 5.5700, 'lng' => -0.2070, 'name' => 'University of Ghana Gate'],
                ['lat' => 5.5490, 'lng' => -0.1850, 'name' => 'National Theatre'],
                ['lat' => 5.5620, 'lng' => -0.1950, 'name' => 'Accra Mall'],
                ['lat' => 5.5550, 'lng' => -0.2100, 'name' => 'Abeka Junction Bridge'],
                ['lat' => 5.5670, 'lng' => -0.1900, 'name' => 'Ridge Hospital'],
                ['lat' => 5.5440, 'lng' => -0.2030, 'name' => 'Bukom Boxing Arena'],
                ['lat' => 5.5590, 'lng' => -0.1810, 'name' => 'Labadi Beach Hotel'],
                ['lat' => 5.5510, 'lng' => -0.1990, 'name' => 'Holy Spirit Cathedral'],
                ['lat' => 5.5630, 'lng' => -0.2080, 'name' => 'Darkuman Overpass'],
                ['lat' => 5.5480, 'lng' => -0.1870, 'name' => 'Arts Centre Market'],
                ['lat' => 5.5720, 'lng' => -0.1920, 'name' => 'Achimota School'],
                ['lat' => 5.5460, 'lng' => -0.2060, 'name' => 'Agbogbloshie Market'],
                ['lat' => 5.5540, 'lng' => -0.1830, 'name' => 'Osu Oxford Street'],
            ];

            foreach ($buildingLocations as $loc) {
                Building::create([
                    'crisis_id' => $crisis->id,
                    'ms_building_id' => 'demo-'.Str::slug($loc['name']),
                ]);
            }

            // Seed demo landmarks
            $undpUser = UndpUser::first();
            if ($undpUser) {
                foreach ([
                    ['name' => 'Makola Market', 'type' => 'market', 'lat' => 5.5560, 'lng' => -0.1969],
                    ['name' => 'Korle Bu Hospital', 'type' => 'hospital', 'lat' => 5.5500, 'lng' => -0.2050],
                    ['name' => 'Independence Square', 'type' => 'public', 'lat' => 5.5610, 'lng' => -0.1870],
                    ['name' => 'Kaneshie Market', 'type' => 'market', 'lat' => 5.5580, 'lng' => -0.2120],
                    ['name' => 'Osu Castle', 'type' => 'government', 'lat' => 5.5650, 'lng' => -0.1780],
                    ['name' => 'National Theatre', 'type' => 'public', 'lat' => 5.5490, 'lng' => -0.1850],
                ] as $lm) {
                    Landmark::create([
                        'crisis_id' => $crisis->id,
                        'name' => $lm['name'],
                        'type' => $lm['type'],
                        'latitude' => $lm['lat'],
                        'longitude' => $lm['lng'],
                        'added_by' => $undpUser->id,
                    ]);
                }
            }

            // Seed demo damage reports with coordinates matching some buildings
            $damageLevels = ['minimal', 'partial', 'complete'];
            $infraTypes = ['commercial', 'community', 'government', 'utility', 'transport'];
            $crisisTypes = ['flood', 'flood', 'flood', 'earthquake', 'flood'];

            for ($i = 0; $i < 8; $i++) {
                $loc = $buildingLocations[$i % count($buildingLocations)];
                DamageReport::factory()->create([
                    'crisis_id' => $crisis->id,
                    'latitude' => $loc['lat'] + fake()->randomFloat(4, -0.002, 0.002),
                    'longitude' => $loc['lng'] + fake()->randomFloat(4, -0.002, 0.002),
                    'damage_level' => $damageLevels[$i % 3],
                    'infrastructure_type' => $infraTypes[$i % 5],
                    'crisis_type' => $crisisTypes[$i % 5],
                    'infrastructure_name' => $loc['name'],
                    'landmark_text' => 'Near '.$loc['name'],
                    'description' => 'Damage observed near '.$loc['name'].'. Structure shows signs of '.$damageLevels[$i % 3].' damage from flooding.',
                ]);
            }
        }
    }
}
