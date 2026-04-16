<?php

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Services\ConflictModeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns true for conflict context crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);
    $service = new ConflictModeService;

    expect($service->isConflict($crisis))->toBeTrue();
});

it('returns false for standard crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $service = new ConflictModeService;

    expect($service->isConflict($crisis))->toBeFalse();
});

it('nullifies device fingerprint in conflict mode', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);
    $service = new ConflictModeService;

    $data = new SubmitReportData(
        crisis: $crisis,
        deviceFingerprintId: 'abc123hash',
        reporterTier: 'device',
    );

    $service->applyToSubmission($data);

    expect($data->deviceFingerprintId)->toBeNull()
        ->and($data->reporterTier)->toBe('anonymous');
});

it('preserves device fingerprint for standard crisis', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $service = new ConflictModeService;

    $data = new SubmitReportData(
        crisis: $crisis,
        deviceFingerprintId: 'abc123hash',
        reporterTier: 'device',
    );

    $service->applyToSubmission($data);

    expect($data->deviceFingerprintId)->toBe('abc123hash')
        ->and($data->reporterTier)->toBe('device');
});

it('disables whatsapp when conflict mode and whatsapp not explicitly enabled', function () {
    $crisis = Crisis::factory()->create([
        'conflict_context' => true,
        'whatsapp_enabled' => false,
    ]);
    $service = new ConflictModeService;

    expect($service->shouldDisableWhatsApp($crisis))->toBeTrue();
});
