<?php

use App\Jobs\ExportReportsKml;
use App\Jobs\ExportReportsPdf;
use App\Jobs\ExportReportsShapefile;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('exports KML for authenticated user', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/kml');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/vnd.google-earth.kml+xml');
});

it('exports Shapefile ZIP for authenticated user', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/shapefile');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/zip');
});

it('exports PDF summary for authenticated user', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
    ]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/pdf');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
});

it('generates valid KML output with placemarks', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $job = new ExportReportsKml(crisisId: $crisis->id);
    $filename = $job->handle();

    $content = Storage::get($filename);
    expect($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>')
        ->toContain('<kml xmlns="http://www.opengis.net/kml/2.2">')
        ->toContain('<Placemark>')
        ->toContain('<Point><coordinates>');

    Storage::delete($filename);
});

it('generates shapefile ZIP with required files', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $job = new ExportReportsShapefile(crisisId: $crisis->id);
    $filename = $job->handle();

    $zipPath = storage_path('app/'.$filename);
    expect(file_exists($zipPath))->toBeTrue();

    $zip = new ZipArchive;
    $zip->open($zipPath);

    $fileNames = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $fileNames[] = $zip->getNameIndex($i);
    }
    $zip->close();

    expect($fileNames)->toContain('rapida-reports.shp')
        ->toContain('rapida-reports.dbf')
        ->toContain('rapida-reports.shx')
        ->toContain('rapida-reports.prj');

    unlink($zipPath);
});

it('generates PDF with crisis summary data', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'name' => 'Test Crisis Export',
    ]);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
    ]);

    $job = new ExportReportsPdf(crisisId: $crisis->id);
    $filename = $job->handle();

    $content = Storage::get($filename);
    expect($content)->not->toBeEmpty();
    expect(str_starts_with($content, '%PDF'))->toBeTrue();

    Storage::delete($filename);
});

it('requires authentication for KML export', function () {
    $this->get('/dashboard/export/kml')->assertRedirect('/login');
});

it('requires authentication for shapefile export', function () {
    $this->get('/dashboard/export/shapefile')->assertRedirect('/login');
});

it('requires authentication for PDF export', function () {
    $this->get('/dashboard/export/pdf')->assertRedirect('/login');
});

it('filters KML by damage level', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'damage_level' => 'minimal',
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'damage_level' => 'complete',
        'latitude' => 5.57,
        'longitude' => -0.21,
    ]);

    $job = new ExportReportsKml(crisisId: $crisis->id, damageFilter: 'minimal');
    $filename = $job->handle();

    $content = Storage::get($filename);
    expect($content)->toContain('minimal')
        ->not->toContain('complete');

    Storage::delete($filename);
});
