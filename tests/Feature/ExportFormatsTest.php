<?php

use App\Jobs\ExportReportsCsv;
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

it('isolates exports to the crisis named by ?crisis_slug', function () {
    // Use distinct, far-apart coordinates as markers — the CSV does not include
    // the description column, so we identify reports by their lat/lng pair.
    $accra = Crisis::factory()->create(['status' => 'active', 'slug' => 'accra-flood-test', 'name' => 'Accra Flood Test']);
    $aleppo = Crisis::factory()->create(['status' => 'active', 'slug' => 'aleppo-test', 'name' => 'Aleppo Test']);

    DamageReport::factory()->create([
        'crisis_id' => $accra->id,
        'latitude' => 5.5601, 'longitude' => -0.2001,
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $aleppo->id,
        'latitude' => 36.2102, 'longitude' => 37.1602,
    ]);

    $job = new ExportReportsCsv(crisisId: $accra->id);
    $filename = $job->handle();
    $content = Storage::get($filename);

    expect($content)->toContain('5.5601')
        ->not->toContain('36.2102');

    Storage::delete($filename);
});

it('uses ?crisis_slug query param to resolve crisis at the controller', function () {
    $accra = Crisis::factory()->create(['status' => 'active', 'slug' => 'accra-flood-test', 'name' => 'Accra Flood Test']);
    $aleppo = Crisis::factory()->create(['status' => 'active', 'slug' => 'aleppo-test', 'name' => 'Aleppo Test']);

    DamageReport::factory()->create([
        'crisis_id' => $accra->id,
        'latitude' => 5.56, 'longitude' => -0.20,
        'damage_level' => 'partial',
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $aleppo->id,
        'latitude' => 36.21, 'longitude' => 37.16,
        'damage_level' => 'complete',
    ]);

    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $accraResponse = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/kml?crisis_slug=accra-flood-test');
    $accraResponse->assertOk();
    expect($accraResponse->streamedContent())->toContain('partial')
        ->not->toContain('complete');

    $aleppoResponse = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/kml?crisis_slug=aleppo-test');
    $aleppoResponse->assertOk();
    expect($aleppoResponse->streamedContent())->toContain('complete')
        ->not->toContain('partial');
});

it('returns 404 when ?crisis_slug names an unknown crisis', function () {
    Crisis::factory()->create(['status' => 'active', 'slug' => 'real-crisis']);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $this->actingAs($user, 'undp')
        ->get('/dashboard/export/kml?crisis_slug=no-such-crisis')
        ->assertNotFound();
});

it('falls back to most-recently-created active crisis when no slug given', function () {
    $older = Crisis::factory()->create(['status' => 'active', 'slug' => 'older', 'created_at' => now()->subDays(10)]);
    $newer = Crisis::factory()->create(['status' => 'active', 'slug' => 'newer', 'created_at' => now()->subDay()]);

    DamageReport::factory()->create([
        'crisis_id' => $older->id,
        'latitude' => 5.56, 'longitude' => -0.20,
        'damage_level' => 'minimal',
    ]);
    DamageReport::factory()->create([
        'crisis_id' => $newer->id,
        'latitude' => 5.57, 'longitude' => -0.21,
        'damage_level' => 'complete',
    ]);

    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/kml');

    $response->assertOk();
    expect($response->streamedContent())->toContain('complete')
        ->not->toContain('minimal');
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
