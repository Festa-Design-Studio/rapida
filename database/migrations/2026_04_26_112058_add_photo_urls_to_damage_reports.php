<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            // Gap-51: array of additional photo URLs when crisis.multi_photo_enabled
            // is true. The canonical photo_url column remains the primary
            // photo (first in array, displayed on map pin and confirmation).
            // Stored as JSON for SQLite/PG portability — query patterns are
            // "fetch the report, render all its photos", never "find reports
            // whose photo[2] matches X".
            $table->json('photo_urls')->nullable()->after('photo_url');
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropColumn('photo_urls');
        });
    }
};
