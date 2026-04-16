<?php

namespace Database\Seeders;

use App\Models\Crisis;
use Illuminate\Database\Seeder;

class CrisisSeeder extends Seeder
{
    public function run(): void
    {
        // Primary demo crisis — Accra flood (English primary, standard mode).
        // Accra is anglophone Ghana. French and Arabic remain available for
        // francophone and Lebanese-diaspora residents, but the default must
        // be EN so browser-locale negotiation resolves sensibly when the
        // Accept-Language header is missing.
        Crisis::firstOrCreate(['slug' => 'accra-flood-2026'], [
            'name' => 'Accra Urban Flood 2026 (Demo)',
            'default_language' => 'en',
            'available_languages' => ['en', 'fr', 'ar'],
            'active_modules' => ['electricity', 'health', 'pressing_needs'],
            'map_tile_bbox' => [-0.4, 5.4, 0.1, 5.8],
            'h3_resolution' => 8,
            'status' => 'active',
            'conflict_context' => false,
            'whatsapp_enabled' => true,
            'wizard_mode' => 'full',
            'multi_photo_enabled' => false,
            'crisis_type_default' => 'flood',
            'data_retention_days' => 365,
        ]);

        // Conflict zone demo — Syria (Arabic primary, conflict mode)
        Crisis::firstOrCreate(['slug' => 'aleppo-conflict-2026'], [
            'name' => 'Aleppo Conflict Assessment 2026 (Demo)',
            'default_language' => 'ar',
            'available_languages' => ['ar', 'en', 'fr'],
            'active_modules' => ['pressing_needs'],
            'map_tile_bbox' => [36.8, 36.0, 37.4, 36.4],
            'h3_resolution' => 8,
            'status' => 'active',
            'conflict_context' => true,
            'whatsapp_enabled' => false,
            'wizard_mode' => 'full',
            'multi_photo_enabled' => false,
            'crisis_type_default' => 'conflict',
            'data_retention_days' => 180,
        ]);
    }
}
