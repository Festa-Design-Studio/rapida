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
        Schema::table('crises', function (Blueprint $table) {
            $table->boolean('conflict_context')->default(false)->after('status');
            $table->boolean('whatsapp_enabled')->default(true)->after('conflict_context');
            $table->string('wizard_mode', 20)->default('full')->after('whatsapp_enabled');
            $table->boolean('multi_photo_enabled')->default(false)->after('wizard_mode');
            $table->string('crisis_type_default', 50)->nullable()->after('default_language');
            $table->unsignedInteger('data_retention_days')->default(365)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('crises', function (Blueprint $table) {
            $table->dropColumn([
                'conflict_context',
                'whatsapp_enabled',
                'wizard_mode',
                'multi_photo_enabled',
                'crisis_type_default',
                'data_retention_days',
            ]);
        });
    }
};
