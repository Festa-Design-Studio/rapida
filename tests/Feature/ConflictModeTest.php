<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('null-forces device_fingerprint_id in conflict crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => true,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'partial',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'conflict',
        'device_fingerprint_id' => 'abc-fingerprint-123',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->device_fingerprint_id)->toBeNull();
});

it('forces reporter_tier to anonymous in conflict crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => true,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'complete',
        'infrastructure_type' => 'commercial',
        'crisis_type' => 'conflict',
        'reporter_tier' => 'device',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->reporter_tier)->toBe('anonymous');
});

it('preserves device_fingerprint_id in normal crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => false,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'minimal',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'flood',
        'device_fingerprint_id' => 'abc-fingerprint-456',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->device_fingerprint_id)->toBe('abc-fingerprint-456');
});

it('preserves reporter_tier in normal crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => false,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'partial',
        'infrastructure_type' => 'government',
        'crisis_type' => 'earthquake',
        'reporter_tier' => 'device',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->reporter_tier)->toBe('device');
});
