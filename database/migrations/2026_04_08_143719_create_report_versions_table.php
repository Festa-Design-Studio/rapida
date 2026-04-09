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
        Schema::create('report_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('damage_reports')->cascadeOnDelete();
            $table->unsignedSmallInteger('version_number');
            $table->string('changed_by_type')->nullable();
            $table->string('changed_by_id')->nullable();
            $table->json('snapshot');
            $table->json('changed_fields')->nullable();
            $table->timestamp('created_at');

            $table->index(['report_id', 'version_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_versions');
    }
};
