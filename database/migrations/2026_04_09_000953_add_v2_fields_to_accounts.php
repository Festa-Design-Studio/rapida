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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('verification_tier', 20)->default('device')->after('preferred_language');
            $table->boolean('is_trusted_device')->default(false)->after('verification_tier');
            $table->timestamp('trusted_since')->nullable()->after('is_trusted_device');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['verification_tier', 'is_trusted_device', 'trusted_since']);
        });
    }
};
