<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects ai callback without internal secret header', function () {
    $report = DamageReport::factory()->create();

    $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.92,
    ])->assertStatus(403);
});

it('rejects ai callback with invalid internal secret header', function () {
    $report = DamageReport::factory()->create();

    $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.92,
    ], [
        'X-Internal-Secret' => 'wrong-secret',
    ])->assertStatus(403);
});

it('accepts ai callback with valid internal secret header', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
    ]);

    $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.92,
    ], [
        'X-Internal-Secret' => config('services.ai.secret'),
    ])->assertStatus(200);
});
