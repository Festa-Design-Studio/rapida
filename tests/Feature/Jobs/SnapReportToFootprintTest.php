<?php

use App\Jobs\SnapReportToFootprint;
use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\BuildingFootprintService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;

uses(RefreshDatabase::class);

it('skips reports that already have a building_footprint_id', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'pre-snapped']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    // BuildingFootprintService should never be called for already-snapped reports.
    $this->mock(BuildingFootprintService::class, function (MockInterface $mock) {
        $mock->shouldNotReceive('snapToNearest');
    });

    app()->call([new SnapReportToFootprint($report), 'handle']);

    expect($report->fresh()->building_footprint_id)->toBe($building->id);
});

it('skips reports without lat/lng (landmark-text-only submissions)', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => null,
        'latitude' => null,
        'longitude' => null,
    ]);

    $this->mock(BuildingFootprintService::class, function (MockInterface $mock) {
        $mock->shouldNotReceive('snapToNearest');
    });

    app()->call([new SnapReportToFootprint($report), 'handle']);

    expect($report->fresh()->building_footprint_id)->toBeNull();
});

it('snaps an orphan report to the nearest building when one is found', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $nearby = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'nearby-building']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => null,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $this->mock(BuildingFootprintService::class, function (MockInterface $mock) use ($nearby) {
        $mock->shouldReceive('snapToNearest')
            ->once()
            ->withArgs(fn ($lat, $lng, $crisisId) => $lat === 5.56 && $lng === -0.20)
            ->andReturn($nearby);
    });

    app()->call([new SnapReportToFootprint($report), 'handle']);

    expect($report->fresh()->building_footprint_id)->toBe($nearby->id);
});

it('leaves the report unassociated when no footprint is within range', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => null,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $this->mock(BuildingFootprintService::class, function (MockInterface $mock) {
        $mock->shouldReceive('snapToNearest')->once()->andReturn(null);
    });

    app()->call([new SnapReportToFootprint($report), 'handle']);

    expect($report->fresh()->building_footprint_id)->toBeNull();
});
