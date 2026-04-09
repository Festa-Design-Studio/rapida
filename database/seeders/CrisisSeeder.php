<?php

namespace Database\Seeders;

use App\Models\Crisis;
use Illuminate\Database\Seeder;

class CrisisSeeder extends Seeder
{
    public function run(): void
    {
        Crisis::create([
            'name' => 'Accra Urban Flood 2026 (Demo)',
            'slug' => 'accra-flood-2026',
            'default_language' => 'fr',
            'available_languages' => ['fr', 'en', 'ar'],
            'active_modules' => ['electricity', 'health', 'pressing_needs'],
            'map_tile_bbox' => [-0.4, 5.4, 0.1, 5.8],
            'h3_resolution' => 8,
            'status' => 'active',
        ]);
    }
}
