<?php

use App\Models\Building;
use App\Models\DamageReport;
use App\Services\DuplicateDetectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('flags reports with matching phash within hamming distance of 5', function () {
    $building = Building::factory()->create();

    // Two reports with very similar pHash values (hamming distance = 0)
    $existing = DamageReport::factory()->create([
        'crisis_id' => $building->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'aaa',
        'photo_phash' => 'aaaaaaaaaaaaaaaa',
        'submitted_at' => now()->subHour(),
    ]);

    $report = DamageReport::factory()->create([
        'crisis_id' => $existing->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'bbb',
        'photo_phash' => 'aaaaaaaaaaaaaaaa',
        'submitted_at' => now(),
        'is_flagged' => false,
    ]);

    $service = new DuplicateDetectionService;
    $result = $service->checkForDuplicates($report);

    expect($result)->toBeTrue()
        ->and($report->fresh()->is_flagged)->toBeTrue();
});

it('does not flag reports when phash hamming distance exceeds threshold', function () {
    $building = Building::factory()->create();

    // pHash values that are completely different (hamming distance = 64)
    $existing = DamageReport::factory()->create([
        'crisis_id' => $building->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'aaa',
        'photo_phash' => '0000000000000000',
        'submitted_at' => now()->subHour(),
    ]);

    $report = DamageReport::factory()->create([
        'crisis_id' => $existing->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'bbb',
        'photo_phash' => 'ffffffffffffffff',
        'submitted_at' => now(),
        'is_flagged' => false,
    ]);

    $service = new DuplicateDetectionService;
    $result = $service->checkForDuplicates($report);

    expect($result)->toBeFalse()
        ->and($report->fresh()->is_flagged)->toBeFalse();
});

it('skips phash check when report has no phash', function () {
    $building = Building::factory()->create();

    $existing = DamageReport::factory()->create([
        'crisis_id' => $building->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'aaa',
        'photo_phash' => 'aaaaaaaaaaaaaaaa',
        'submitted_at' => now()->subHour(),
    ]);

    $report = DamageReport::factory()->create([
        'crisis_id' => $existing->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'bbb',
        'photo_phash' => null,
        'submitted_at' => now(),
        'is_flagged' => false,
    ]);

    $service = new DuplicateDetectionService;
    $result = $service->checkForDuplicates($report);

    expect($result)->toBeFalse();
});

it('only checks phash against reports from same crisis within 24 hours', function () {
    $building = Building::factory()->create();

    // Old report with same phash but outside the 24-hour window
    $existing = DamageReport::factory()->create([
        'crisis_id' => $building->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'aaa',
        'photo_phash' => 'aaaaaaaaaaaaaaaa',
        'submitted_at' => now()->subHours(25),
    ]);

    $report = DamageReport::factory()->create([
        'crisis_id' => $existing->crisis_id,
        'building_footprint_id' => $building->id,
        'photo_hash' => 'bbb',
        'photo_phash' => 'aaaaaaaaaaaaaaaa',
        'submitted_at' => now(),
        'is_flagged' => false,
    ]);

    $service = new DuplicateDetectionService;
    $result = $service->checkForDuplicates($report);

    expect($result)->toBeFalse();
});
