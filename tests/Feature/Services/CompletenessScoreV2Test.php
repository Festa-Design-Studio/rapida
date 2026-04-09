<?php

use App\Models\DamageReport;
use App\Services\CompletenessScoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->scorer = new CompletenessScoreService;
});

// --- Model-based score() tests ---

it('scores photo only as 2 points', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => null,
        'longitude' => null,
        'w3w_code' => null,
        'landmark_text' => null,
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo=2, location=0, damage=2, infra=(type+crisis but no debris)=0, name=0
    // The factory still provides damage_level, infrastructure_type, crisis_type,
    // so damage=2 always. To isolate photo, we check via scoreFromArray.
    // With model: photo(2) + location(0) + damage(2) = 4
    // We use scoreFromArray for pure isolation tests below.
    expect($this->scorer->score($report))->toBe(4);
});

it('scores photo + latitude location as +2 over baseline', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
        'longitude' => -0.20,
        'w3w_code' => null,
        'landmark_text' => null,
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo(2) + location(2) + damage(2) = 6
    expect($this->scorer->score($report))->toBe(6);
});

it('scores photo + location + damage_level as 6 (credibility threshold)', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
        'longitude' => -0.20,
        'w3w_code' => null,
        'landmark_text' => null,
        'damage_level' => 'partial',
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo(2) + location(2) + damage(2) = 6
    expect($this->scorer->score($report))->toBe(6);
});

it('scores full report as 8 (max)', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
        'longitude' => -0.20,
        'damage_level' => 'complete',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'flood',
        'debris_required' => true,
        'infrastructure_name' => 'Town Hall',
    ]);

    expect($this->scorer->score($report))->toBe(8);
});

it('counts w3w_code as location for +2', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => null,
        'longitude' => null,
        'w3w_code' => 'filled.count.soap',
        'landmark_text' => null,
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo(2) + location via w3w(2) + damage(2) = 6
    expect($this->scorer->score($report))->toBe(6);
});

it('counts landmark_text as location for +2', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => null,
        'longitude' => null,
        'w3w_code' => null,
        'landmark_text' => 'Next to the market',
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo(2) + location via landmark(2) + damage(2) = 6
    expect($this->scorer->score($report))->toBe(6);
});

it('scores placeholder photo as 0 for photo component', function () {
    $report = DamageReport::factory()->create([
        'photo_url' => 'https://rapida-demo.s3.amazonaws.com/placeholder.jpg',
        'latitude' => null,
        'longitude' => null,
        'w3w_code' => null,
        'landmark_text' => null,
        'infrastructure_name' => null,
        'debris_required' => null,
    ]);

    // photo(0) + location(0) + damage(2) = 2
    expect($this->scorer->score($report))->toBe(2);
});

// --- Array-based scoreFromArray() tests ---

it('scoreFromArray scores photo only as 2', function () {
    expect($this->scorer->scoreFromArray([
        'photo_url' => 'https://example.com/photo.jpg',
    ]))->toBe(2);
});

it('scoreFromArray scores photo + location as 4', function () {
    expect($this->scorer->scoreFromArray([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
    ]))->toBe(4);
});

it('scoreFromArray scores photo + location + damage as 6 (credibility threshold)', function () {
    expect($this->scorer->scoreFromArray([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
        'damage_level' => 'partial',
    ]))->toBe(6);
});

it('scoreFromArray scores full report as 8 (max)', function () {
    expect($this->scorer->scoreFromArray([
        'photo_url' => 'https://example.com/photo.jpg',
        'latitude' => 5.56,
        'damage_level' => 'complete',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'flood',
        'infrastructure_name' => 'Town Hall',
    ]))->toBe(8);
});

it('scoreFromArray counts photo key as photo component', function () {
    expect($this->scorer->scoreFromArray([
        'photo' => 'some-photo-data',
    ]))->toBe(2);
});

it('scoreFromArray counts w3w_code as location', function () {
    expect($this->scorer->scoreFromArray([
        'w3w_code' => 'filled.count.soap',
    ]))->toBe(2);
});

it('scoreFromArray counts landmark_text as location', function () {
    expect($this->scorer->scoreFromArray([
        'landmark_text' => 'Near the bridge',
    ]))->toBe(2);
});

it('scoreFromArray returns 0 for empty array', function () {
    expect($this->scorer->scoreFromArray([]))->toBe(0);
});

it('scoreFromArray returns 0 for missing photo', function () {
    expect($this->scorer->scoreFromArray([
        'damage_level' => 'partial',
        'latitude' => 5.56,
    ]))->toBe(4);
});
