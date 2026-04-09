<?php

use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a version snapshot when a damage report is updated', function () {
    $report = DamageReport::factory()->create([
        'damage_level' => 'minimal',
        'infrastructure_type' => 'residential',
    ]);

    expect($report->versions)->toHaveCount(0);

    $report->update(['damage_level' => 'complete']);

    $report->refresh();

    expect($report->versions)->toHaveCount(1);
    expect($report->versions->first()->changed_fields)->toContain('damage_level');
    expect($report->versions->first()->snapshot['damage_level'])->toBe('minimal');
    expect($report->versions->first()->version_number)->toBe(1);
});

it('increments version number on successive updates', function () {
    $report = DamageReport::factory()->create([
        'damage_level' => 'minimal',
    ]);

    $report->update(['damage_level' => 'partial']);
    $report->update(['damage_level' => 'complete']);

    $report->refresh();

    expect($report->versions)->toHaveCount(2);
    expect($report->versions->last()->version_number)->toBe(2);
});

it('does not create a version when no fields changed', function () {
    $report = DamageReport::factory()->create([
        'damage_level' => 'minimal',
    ]);

    $report->update(['damage_level' => 'minimal']);

    expect($report->versions)->toHaveCount(0);
});
