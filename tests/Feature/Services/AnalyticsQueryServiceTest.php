<?php

use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\AnalyticsQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->crisis = Crisis::factory()->create();
    $this->service = new AnalyticsQueryService;
});

it('returns total reports count for a crisis', function () {
    DamageReport::factory()->count(3)->create(['crisis_id' => $this->crisis->id]);
    DamageReport::factory()->create(); // different crisis

    $total = $this->service->totalReports($this->crisis->id);

    expect($total)->toBe(3);
});

it('returns reports grouped by damage level', function () {
    DamageReport::factory()->create(['crisis_id' => $this->crisis->id, 'damage_level' => 'minimal']);
    DamageReport::factory()->count(2)->create(['crisis_id' => $this->crisis->id, 'damage_level' => 'partial']);

    $result = $this->service->byDamageLevel($this->crisis->id);

    expect($result)->toHaveKey('minimal', 1)
        ->toHaveKey('partial', 2);
});

it('returns reports grouped by infrastructure type', function () {
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $this->crisis->id,
        'infrastructure_type' => 'residential',
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $this->crisis->id,
        'infrastructure_type' => 'commercial',
    ]);

    $result = $this->service->byInfrastructureType($this->crisis->id);

    expect($result)->toHaveKey('residential', 2)
        ->toHaveKey('commercial', 1);
});

it('returns reports grouped by day', function () {
    DamageReport::factory()->create([
        'crisis_id' => $this->crisis->id,
        'submitted_at' => now()->subDay(),
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $this->crisis->id,
        'submitted_at' => now(),
    ]);

    $result = $this->service->reportsByDay($this->crisis->id);

    expect($result)->toHaveCount(2);
});

it('returns top buildings ordered by report count', function () {
    Building::factory()->create([
        'crisis_id' => $this->crisis->id,
        'report_count' => 5,
    ]);
    Building::factory()->create([
        'crisis_id' => $this->crisis->id,
        'report_count' => 10,
    ]);
    Building::factory()->create([
        'crisis_id' => $this->crisis->id,
        'report_count' => 0, // should be excluded
    ]);

    $result = $this->service->topBuildings($this->crisis->id);

    expect($result)->toHaveCount(2)
        ->and($result->first()->report_count)->toBe(10);
});

it('returns recent reports ordered by submitted_at descending', function () {
    $older = DamageReport::factory()->create([
        'crisis_id' => $this->crisis->id,
        'submitted_at' => now()->subHours(2),
    ]);
    $newer = DamageReport::factory()->create([
        'crisis_id' => $this->crisis->id,
        'submitted_at' => now(),
    ]);

    $result = $this->service->recentReports($this->crisis->id);

    expect($result)->toHaveCount(2)
        ->and($result->first()->id)->toBe($newer->id);
});

it('caches results with a 5-minute TTL', function () {
    DamageReport::factory()->create(['crisis_id' => $this->crisis->id]);

    $this->service->totalReports($this->crisis->id);

    expect(Cache::has("analytics:{$this->crisis->id}:total"))->toBeTrue();

    // Create another report — cached value should remain unchanged
    DamageReport::factory()->create(['crisis_id' => $this->crisis->id]);

    $cachedTotal = $this->service->totalReports($this->crisis->id);

    expect($cachedTotal)->toBe(1);
});
