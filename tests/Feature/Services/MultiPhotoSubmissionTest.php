<?php

use App\DataTransferObjects\SubmitReportData;
use App\Models\Crisis;
use App\Services\ReportSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('stores only the primary photo when multi_photo_enabled is false', function () {
    Storage::fake('public');
    $crisis = Crisis::factory()->create([
        'multi_photo_enabled' => false,
        'multi_photo_max' => 5,
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        photoFile: UploadedFile::fake()->image('primary.jpg'),
        additionalPhotoFiles: [
            UploadedFile::fake()->image('extra1.jpg'),
            UploadedFile::fake()->image('extra2.jpg'),
        ],
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->photo_urls)->toBeArray()->toHaveCount(1);
    expect($report->photo_urls[0])->toBe($report->photo_url);
});

it('stores the primary plus up to (multi_photo_max - 1) additional photos when enabled', function () {
    Storage::fake('public');
    $crisis = Crisis::factory()->create([
        'multi_photo_enabled' => true,
        'multi_photo_max' => 3,  // primary + 2 additional
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        photoFile: UploadedFile::fake()->image('primary.jpg'),
        additionalPhotoFiles: [
            UploadedFile::fake()->image('extra1.jpg'),
            UploadedFile::fake()->image('extra2.jpg'),
            UploadedFile::fake()->image('extra3.jpg'),  // exceeds cap, should be dropped
            UploadedFile::fake()->image('extra4.jpg'),  // also dropped
        ],
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->photo_urls)->toBeArray()->toHaveCount(3); // primary + 2 additional
});

it('stores additional photos from URLs (e.g., WhatsApp MediaUrl1+)', function () {
    Storage::fake('public');
    Http::fake([
        'https://example.com/p1.jpg' => Http::response('photo-bytes-1', 200, ['Content-Type' => 'image/jpeg']),
        'https://example.com/p2.jpg' => Http::response('photo-bytes-2', 200, ['Content-Type' => 'image/jpeg']),
    ]);

    $crisis = Crisis::factory()->create([
        'multi_photo_enabled' => true,
        'multi_photo_max' => 5,
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'complete',
        infrastructureType: 'utility',
        crisisType: 'cyclone',
        photoFile: UploadedFile::fake()->image('primary.jpg'),
        additionalPhotoUrls: ['https://example.com/p1.jpg', 'https://example.com/p2.jpg'],
        submittedVia: 'whatsapp',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    expect($report->photo_urls)->toHaveCount(3);
});

it('survives a single failed additional-photo download without rejecting the whole submission', function () {
    Storage::fake('public');
    Http::fake([
        'https://example.com/good.jpg' => Http::response('good-bytes', 200, ['Content-Type' => 'image/jpeg']),
        'https://example.com/broken.jpg' => Http::response('', 500),
    ]);

    $crisis = Crisis::factory()->create([
        'multi_photo_enabled' => true,
        'multi_photo_max' => 5,
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        photoFile: UploadedFile::fake()->image('primary.jpg'),
        additionalPhotoUrls: ['https://example.com/good.jpg', 'https://example.com/broken.jpg'],
        submittedVia: 'whatsapp',
    );

    $report = app(ReportSubmissionService::class)->submit($data);

    // Primary + 1 successfully-downloaded additional. Broken URL is dropped.
    expect($report->photo_urls)->toHaveCount(2);
});
