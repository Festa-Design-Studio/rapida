<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crises', function (Blueprint $table) {
            // Operator-controlled feature flag for the per-H3-cell danger zones layer.
            // Default false so existing crises do not surface the feature without
            // explicit opt-in. In conflict crises, the API ignores this flag and
            // never returns zones (privacy gate in DangerZoneService).
            $table->boolean('danger_zones_enabled')->default(false)->after('multi_photo_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('crises', function (Blueprint $table) {
            $table->dropColumn('danger_zones_enabled');
        });
    }
};
