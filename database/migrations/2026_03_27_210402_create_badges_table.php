<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('badge_key');
            $table->foreignUuid('crisis_id')->constrained('crises')->cascadeOnDelete();
            $table->timestamp('awarded_at');
            $table->timestamps();

            $table->unique(['account_id', 'badge_key', 'crisis_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badges');
    }
};
