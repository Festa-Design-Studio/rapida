<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_modules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('report_id')->constrained('damage_reports')->cascadeOnDelete();
            $table->string('module_key');
            $table->string('field_key');
            $table->json('value');
            $table->timestamps();

            $table->index(['report_id', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_modules');
    }
};
