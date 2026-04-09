<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landmarks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('crisis_id')->constrained('crises')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->foreignUuid('added_by')->constrained('undp_users')->cascadeOnDelete();
            $table->string('osm_id')->nullable();
            $table->timestamps();

            $table->index(['crisis_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landmarks');
    }
};
