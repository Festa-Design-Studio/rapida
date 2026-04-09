<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->string('device_fingerprint_id', 64)->nullable()->index()->after('account_id');
            $table->boolean('photo_guidance_shown')->default(false)->after('photo_hash');
            $table->string('reporter_tier', 20)->default('anonymous')->after('submitted_via');

            $table->index(['crisis_id', 'device_fingerprint_id']);
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            $table->dropIndex(['crisis_id', 'device_fingerprint_id']);
            $table->dropIndex(['device_fingerprint_id']);
            $table->dropColumn(['device_fingerprint_id', 'photo_guidance_shown', 'reporter_tier']);
        });
    }
};
