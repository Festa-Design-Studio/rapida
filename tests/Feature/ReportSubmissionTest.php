<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a damage report via API', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    $response = $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'partial',
        'infrastructure_type' => 'commercial',
        'crisis_type' => 'flood',
        'latitude' => 5.56,
        'longitude' => -0.20,
        'location_method' => 'coordinate_only',
        'submitted_at' => now()->toIso8601String(),
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['report_id', 'completeness_score', 'message']);

    expect(DamageReport::count())->toBe(1);
});

it('handles idempotent submissions', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $key = fake()->uuid();

    $payload = [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'complete',
        'infrastructure_type' => 'government',
        'crisis_type' => 'earthquake',
        'submitted_at' => now()->toIso8601String(),
    ];

    $this->postJson('/api/v1/reports', $payload, ['Idempotency-Key' => $key])
        ->assertStatus(201);

    $this->postJson('/api/v1/reports', $payload, ['Idempotency-Key' => $key])
        ->assertStatus(201);

    expect(DamageReport::count())->toBe(1);
});

it('returns pins as GeoJSON', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $response = $this->getJson("/api/v1/crises/{$crisis->slug}/pins");

    $response->assertOk()
        ->assertJsonStructure(['type', 'features'])
        ->assertJsonCount(3, 'features');
});

it('returns buildings as GeoJSON', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    $response = $this->getJson("/api/v1/crises/{$crisis->slug}/buildings");

    $response->assertOk()
        ->assertJsonStructure(['type', 'features']);
});

it('validates required fields on report submission', function () {
    $this->postJson('/api/v1/reports', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['crisis_slug', 'damage_level', 'infrastructure_type', 'crisis_type', 'submitted_at']);
});
