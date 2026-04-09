<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('crisis_id')->constrained('crises')->cascadeOnDelete();
            $table->string('ms_building_id')->nullable()->index();
            $table->string('canonical_damage_level')->nullable();
            $table->unsignedInteger('report_count')->default(0);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->index('crisis_id');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE buildings ADD COLUMN footprint_geom geometry(POLYGON, 4326)');
            DB::statement('CREATE INDEX buildings_footprint_geom_gist ON buildings USING GIST (footprint_geom)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
