<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('receives AI classification callback and updates report', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'ai_suggested_level' => null,
        'ai_confidence' => null,
    ]);

    $response = $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => $report->id,
        'status' => 'success',
        'damage_level' => 'partial',
        'confidence' => 0.85,
        'scores' => ['minimal' => 0.10, 'partial' => 0.85, 'complete' => 0.05],
    ]);

    $response->assertOk()->assertJson(['ok' => true]);
    expect($report->fresh()->ai_suggested_level->value)->toBe('partial');
    expect((float) $report->fresh()->ai_confidence)->toBeGreaterThan(0.8);
});

it('handles error status gracefully', function () {
    $response = $this->postJson('/api/v1/internal/ai-result', [
        'job_id' => 'nonexistent-id',
        'status' => 'error',
        'error' => 'Model inference failed',
    ]);

    $response->assertOk()->assertJson(['ok' => true]);
});

it('validates AI callback payload', function () {
    $this->postJson('/api/v1/internal/ai-result', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['job_id', 'status']);
});
