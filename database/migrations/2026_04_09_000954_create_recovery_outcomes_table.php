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
        Schema::create('recovery_outcomes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('crisis_id')->constrained('crises')->cascadeOnDelete();
            $table->string('h3_cell_id')->index();
            $table->text('message');
            $table->foreignUuid('triggered_by')->constrained('undp_users')->cascadeOnDelete();
            $table->timestamp('triggered_at');
            $table->timestamps();

            $table->index(['crisis_id', 'h3_cell_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_outcomes');
    }
};
