<?php

use App\Jobs\ExportReportsGpkg;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('writes a SQLite-format GPKG file with the GeoPackage application_id', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $job = new ExportReportsGpkg(crisisId: $crisis->id);
    $filename = $job->handle();

    $bytes = Storage::get($filename);

    // SQLite header magic.
    expect(substr($bytes, 0, 16))->toBe("SQLite format 3\x00");

    // GeoPackage application_id is at byte offset 68 (4 bytes, big-endian).
    // The bytes ASCII-spell "GPKG" (0x47 0x50 0x4B 0x47).
    $appIdBytes = substr($bytes, 68, 4);
    expect($appIdBytes)->toBe('GPKG');

    Storage::delete($filename);
});

it('contains the three GPKG-required tables and the damage_reports feature table', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $job = new ExportReportsGpkg(crisisId: $crisis->id);
    $filename = $job->handle();

    $tempCopy = tempnam(sys_get_temp_dir(), 'gpkg-verify-').'.gpkg';
    file_put_contents($tempCopy, Storage::get($filename));

    $pdo = new PDO('sqlite:'.$tempCopy);
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")
        ->fetchAll(PDO::FETCH_COLUMN);

    expect($tables)->toContain('gpkg_spatial_ref_sys')
        ->toContain('gpkg_contents')
        ->toContain('gpkg_geometry_columns')
        ->toContain('damage_reports');

    unlink($tempCopy);
    Storage::delete($filename);
});

it('writes one feature row per damage report, with EPSG:4326 srs_id', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(7)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);

    $job = new ExportReportsGpkg(crisisId: $crisis->id);
    $filename = $job->handle();

    $tempCopy = tempnam(sys_get_temp_dir(), 'gpkg-verify-').'.gpkg';
    file_put_contents($tempCopy, Storage::get($filename));

    $pdo = new PDO('sqlite:'.$tempCopy);

    $count = (int) $pdo->query('SELECT COUNT(*) FROM damage_reports')->fetchColumn();
    expect($count)->toBe(7);

    $srsId = (int) $pdo->query("SELECT srs_id FROM gpkg_geometry_columns WHERE table_name='damage_reports'")->fetchColumn();
    expect($srsId)->toBe(4326);

    unlink($tempCopy);
    Storage::delete($filename);
});

it('honors the damage_level filter', function () {
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

    $job = new ExportReportsGpkg(crisisId: $crisis->id, damageFilter: 'minimal');
    $filename = $job->handle();

    $tempCopy = tempnam(sys_get_temp_dir(), 'gpkg-verify-').'.gpkg';
    file_put_contents($tempCopy, Storage::get($filename));

    $pdo = new PDO('sqlite:'.$tempCopy);
    $levels = $pdo->query('SELECT damage_level FROM damage_reports')->fetchAll(PDO::FETCH_COLUMN);

    expect($levels)->toBe(['minimal']);

    unlink($tempCopy);
    Storage::delete($filename);
});

it('serves the GPKG download via the controller for an authenticated analyst', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')->get('/dashboard/export/gpkg');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/geopackage+sqlite3');
});

it('requires authentication for GPKG export', function () {
    $this->get('/dashboard/export/gpkg')->assertRedirect('/login');
});
