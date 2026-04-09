<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_or_email_hash', 64)->unique();
            $table->foreignUuid('crisis_id')->nullable()->constrained('crises')->nullOnDelete();
            $table->unsignedInteger('badge_count')->default(0);
            $table->unsignedInteger('leaderboard_score')->default(0);
            $table->string('preferred_language', 5)->default('en');
            $table->string('h3_cell_id')->nullable();
            $table->boolean('is_suspended')->default(false);
            $table->unsignedInteger('flagged_report_count')->default(0);
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['crisis_id', 'leaderboard_score']);
            $table->index('h3_cell_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
