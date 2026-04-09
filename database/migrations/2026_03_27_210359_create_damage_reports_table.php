<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('crisis_id')->constrained('crises')->cascadeOnDelete();
            $table->foreignUuid('building_footprint_id')->nullable()->constrained('buildings')->nullOnDelete();
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignUuid('landmark_id')->nullable()->constrained('landmarks')->nullOnDelete();

            // Photo
            $table->string('photo_url');
            $table->string('photo_hash', 64)->index();
            $table->unsignedBigInteger('photo_size_bytes')->nullable();

            // Damage
            $table->string('damage_level');
            $table->string('ai_suggested_level')->nullable();
            $table->decimal('ai_confidence', 4, 3)->nullable();

            // Infrastructure
            $table->string('infrastructure_type');
            $table->string('crisis_type');
            $table->string('infrastructure_name')->nullable();
            $table->boolean('debris_required')->nullable();

            // Location
            $table->string('location_method');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('w3w_code')->nullable();
            $table->text('landmark_text')->nullable();
            $table->string('h3_cell_id')->nullable()->index();

            // Description
            $table->text('description')->nullable();
            $table->string('description_original_lang', 5)->nullable();
            $table->text('description_en')->nullable();

            // Scoring
            $table->unsignedTinyInteger('completeness_score')->default(0);
            $table->string('submitted_via')->default('web');
            $table->string('idempotency_key', 64)->unique()->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->timestamp('submitted_at');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes
            $table->index(['crisis_id', 'damage_level']);
            $table->index(['crisis_id', 'infrastructure_type']);
            $table->index(['crisis_id', 'submitted_at']);
            $table->index(['crisis_id', 'is_flagged']);
            $table->index(['building_footprint_id', 'completeness_score']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
