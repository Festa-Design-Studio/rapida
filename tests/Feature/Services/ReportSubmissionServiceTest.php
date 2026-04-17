<?php

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\ReportSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('creates a damage report from SubmitReportData', function () {
    $crisis = Crisis::factory()->create();

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'other',
        crisisType: 'flood',
        latitude: 5.6037,
        longitude: -0.1870,
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report)->toBeInstanceOf(DamageReport::class)
        ->and($report->crisis_id)->toBe($crisis->id)
        ->and($report->completeness_score)->toBeGreaterThan(0);
});

it('returns existing report for duplicate idempotency key', function () {
    $crisis = Crisis::factory()->create();
    $existing = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'idempotency_key' => 'unique-key-123',
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'other',
        crisisType: 'flood',
        idempotencyKey: 'unique-key-123',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->id)->toBe($existing->id);
    expect(DamageReport::where('idempotency_key', 'unique-key-123')->count())->toBe(1);
});

it('forces anonymous tier in conflict mode', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'other',
        crisisType: 'flood',
        deviceFingerprintId: 'should-be-nulled',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->device_fingerprint_id)->toBeNull()
        ->and($report->reporter_tier)->toBe('anonymous');
});

it('sets device tier when fingerprint provided and not conflict', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'other',
        crisisType: 'flood',
        deviceFingerprintId: 'abc123hash',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->reporter_tier)->toBe('device');
});

it('stores photo from URL when photoUrl is provided', function () {
    Storage::fake('public');
    Http::fake([
        'https://api.twilio.com/*' => Http::response('whatsapp-photo-bytes', 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $crisis = Crisis::factory()->create();

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'other',
        crisisType: 'flood',
        latitude: 5.6037,
        longitude: -0.1870,
        submittedVia: 'whatsapp',
        photoUrl: 'https://api.twilio.com/2010-04-01/Accounts/AC123/Messages/SM456/Media/ME789',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->photo_url)->not->toBe('https://rapida-demo.s3.amazonaws.com/placeholder.jpg')
        ->and($report->photo_url)->toStartWith('photos/')
        ->and($report->photo_size_bytes)->toBeGreaterThan(0);
});
