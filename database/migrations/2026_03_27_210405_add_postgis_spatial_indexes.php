<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('
                CREATE INDEX damage_reports_location_idx
                ON damage_reports
                USING GIST (ST_MakePoint(longitude, latitude))
            ');

            DB::statement('
                CREATE INDEX crises_active_slug_idx
                ON crises (slug)
                WHERE status = \'active\'
            ');

            DB::statement('
                CREATE INDEX damage_reports_unflagged_idx
                ON damage_reports (crisis_id, submitted_at)
                WHERE is_flagged = false
            ');

            DB::statement('
                CREATE INDEX buildings_canonical_resolution_idx
                ON buildings (crisis_id, canonical_damage_level)
                WHERE canonical_report_id IS NOT NULL
            ');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS damage_reports_location_idx');
            DB::statement('DROP INDEX IF EXISTS crises_active_slug_idx');
            DB::statement('DROP INDEX IF EXISTS damage_reports_unflagged_idx');
            DB::statement('DROP INDEX IF EXISTS buildings_canonical_resolution_idx');
        }
    }
};
