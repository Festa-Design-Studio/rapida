<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crises', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('default_language', 5)->default('en');
            $table->json('available_languages');
            $table->json('active_modules');
            $table->json('map_tile_bbox')->nullable();
            $table->integer('h3_resolution')->default(8);
            $table->string('status')->default('draft');
            $table->string('qr_code_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE crises ADD COLUMN region_bbox geography(POLYGON, 4326)');
            DB::statement('CREATE INDEX crises_region_bbox_gist ON crises USING GIST (region_bbox)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crises');
    }
};
