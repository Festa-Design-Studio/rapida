<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crises', function (Blueprint $table) {
            // Gap-51: per-crisis cap on multi-photo submissions. Default 5
            // matches the proposal language and was the user's confirmed
            // pick. Operators can raise per crisis (cyclone/hurricane
            // documentation often needs 8-10) or lower for low-bandwidth
            // pilots. Only takes effect when crises.multi_photo_enabled
            // is true (existing column).
            $table->unsignedSmallInteger('multi_photo_max')->default(5)->after('multi_photo_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('crises', function (Blueprint $table) {
            $table->dropColumn('multi_photo_max');
        });
    }
};
