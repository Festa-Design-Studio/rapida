<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danger_zones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('crisis_id')->constrained()->cascadeOnDelete();
            $table->string('h3_cell_id'); // H3 cell that the zone covers
            $table->string('severity')->default('caution'); // caution | warning | critical
            $table->text('note')->nullable(); // operator's optional explanation
            $table->foreignUuid('created_by')->nullable()->constrained('undp_users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // A crisis cannot have two active zones for the same H3 cell.
            // Operators replace by deleting + adding rather than editing in place.
            $table->unique(['crisis_id', 'h3_cell_id']);
            $table->index(['crisis_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danger_zones');
    }
};
