<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_number', 20)->index();
            $table->string('crisis_slug')->index();
            $table->string('state')->default('AWAITING_PHOTO');
            $table->json('partial_data')->default('{}');
            $table->string('language', 5)->default('en');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
