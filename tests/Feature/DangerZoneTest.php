<?php

use App\Models\Crisis;
use App\Models\DangerZone;
use App\Services\DangerZoneService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns no zones when the per-crisis flag is disabled', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => false,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->count(3)->create(['crisis_id' => $crisis->id]);

    $zones = app(DangerZoneService::class)->activeZonesFor($crisis);

    expect($zones)->toBeEmpty()
        ->and(app(DangerZoneService::class)->isAvailable($crisis))->toBeFalse();
});

it('returns no zones when the crisis is in conflict mode', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => true,  // operator enabled the flag
        'conflict_context' => true,      // but conflict mode overrides
    ]);
    DangerZone::factory()->count(3)->create(['crisis_id' => $crisis->id]);

    $zones = app(DangerZoneService::class)->activeZonesFor($crisis);

    expect($zones)->toBeEmpty()
        ->and(app(DangerZoneService::class)->isAvailable($crisis))->toBeFalse();
});

it('returns active zones when feature is enabled and not in conflict mode', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => true,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->count(4)->create(['crisis_id' => $crisis->id]);

    $zones = app(DangerZoneService::class)->activeZonesFor($crisis);

    expect($zones)->toHaveCount(4)
        ->and(app(DangerZoneService::class)->isAvailable($crisis))->toBeTrue();
});

it('excludes expired zones', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => true,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->count(2)->create(['crisis_id' => $crisis->id]);
    DangerZone::factory()->expired()->count(3)->create(['crisis_id' => $crisis->id]);

    $zones = app(DangerZoneService::class)->activeZonesFor($crisis);

    expect($zones)->toHaveCount(2);
});

it('orders zones by severity descending — critical before warning before caution', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => true,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'severity' => 'caution', 'h3_cell_id' => 'cell-c']);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'severity' => 'critical', 'h3_cell_id' => 'cell-a']);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'severity' => 'warning', 'h3_cell_id' => 'cell-b']);

    $zones = app(DangerZoneService::class)->activeZonesFor($crisis);

    expect($zones->pluck('severity')->all())->toBe(['critical', 'warning', 'caution']);
});

it('filters nearby zones by the cell-ring the caller passes in', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'danger_zones_enabled' => true,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'h3_cell_id' => 'cell-here']);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'h3_cell_id' => 'cell-near']);
    DangerZone::factory()->create(['crisis_id' => $crisis->id, 'h3_cell_id' => 'cell-far']);

    $nearby = app(DangerZoneService::class)->nearbyZones($crisis, ['cell-here', 'cell-near']);

    expect($nearby)->toHaveCount(2)
        ->and($nearby->pluck('h3_cell_id')->all())->toEqualCanonicalizing(['cell-here', 'cell-near']);
});

it('exposes the API endpoint with feature_enabled + zones structure', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'slug' => 'gap-08-test-crisis',
        'danger_zones_enabled' => true,
        'conflict_context' => false,
    ]);
    DangerZone::factory()->count(2)->create([
        'crisis_id' => $crisis->id,
        'severity' => 'caution',
        'note' => 'Standing water — take care if you must walk through.',
    ]);

    $response = $this->getJson('/api/v1/crises/gap-08-test-crisis/danger-zones');

    $response->assertOk()
        ->assertJson(['feature_enabled' => true])
        ->assertJsonCount(2, 'zones')
        ->assertJsonStructure(['feature_enabled', 'zones' => [['h3_cell_id', 'severity', 'note', 'expires_at']]]);
});

it('API returns feature_enabled false and empty zones when conflict mode is on', function () {
    Crisis::factory()->create([
        'status' => 'active',
        'slug' => 'gap-08-conflict-test',
        'danger_zones_enabled' => true,
        'conflict_context' => true,
    ]);

    $response = $this->getJson('/api/v1/crises/gap-08-conflict-test/danger-zones');

    $response->assertOk()
        ->assertJson(['feature_enabled' => false, 'zones' => []]);
});

it('API returns feature_enabled false when operator has not opted in', function () {
    Crisis::factory()->create([
        'status' => 'active',
        'slug' => 'gap-08-disabled-test',
        'danger_zones_enabled' => false,
        'conflict_context' => false,
    ]);

    $response = $this->getJson('/api/v1/crises/gap-08-disabled-test/danger-zones');

    $response->assertOk()
        ->assertJson(['feature_enabled' => false, 'zones' => []]);
});

it('API returns 404 for unknown crisis slug', function () {
    $this->getJson('/api/v1/crises/no-such-crisis/danger-zones')->assertNotFound();
});
