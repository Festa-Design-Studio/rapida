<?php

use App\Models\Crisis;
use App\Models\UndpUser;
use App\Services\LandmarkBulkImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function writeCsv(string $contents): string
{
    $path = tempnam(sys_get_temp_dir(), 'landmark-csv-');
    file_put_contents($path, $contents);

    return $path;
}

it('imports valid rows and inserts Landmark models', function () {
    $crisis = Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude,crisis_slug\nMakola Market,market,5.5560,-0.1969,accra-flood-2026\nKorle Bu Hospital,hospital,5.55,-0.205,accra-flood-2026\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(2)
        ->and($result->skipped)->toBe(0)
        ->and($result->errors)->toBe([])
        ->and($crisis->landmarks()->count())->toBe(2);
});

it('rejects rows with missing required columns', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude\nMakola,market,5.55,-0.19\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(0)
        ->and($result->errors[0]['reason'])->toContain('header');
});

it('rejects rows with non-numeric coordinates', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude,crisis_slug\nBad Coords,market,not-a-number,-0.19,accra-flood-2026\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(0)
        ->and($result->skipped)->toBe(1)
        ->and($result->errors[0]['reason'])->toContain('Latitude is not a number');
});

it('rejects rows with out-of-range coordinates', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude,crisis_slug\nBad,market,200,-0.19,accra-flood-2026\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->errors[0]['reason'])->toContain('out of range');
});

it('rejects rows with invalid type', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude,crisis_slug\nBad,not-a-type,5.55,-0.19,accra-flood-2026\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->errors[0]['reason'])->toContain('Invalid type');
});

it('rejects rows pointing at a non-existent crisis_slug', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv("name,type,latitude,longitude,crisis_slug\nGhost,market,5.55,-0.19,unknown-slug-xyz\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(0)
        ->and($result->errors[0]['reason'])->toContain("'unknown-slug-xyz' not found");
});

it('mixes successful and failed rows correctly', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = writeCsv(
        "name,type,latitude,longitude,crisis_slug\n".
        "Good Row,market,5.55,-0.19,accra-flood-2026\n".
        "Bad Type,not-a-type,5.55,-0.19,accra-flood-2026\n".
        "Another Good,hospital,5.55,-0.19,accra-flood-2026\n"
    );

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(2)
        ->and($result->skipped)->toBe(1)
        ->and($result->errors)->toHaveCount(1)
        ->and($result->errors[0]['row'])->toBe(3);
});

it('strips a UTF-8 BOM from the header row (Excel-export safe)', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $bom = chr(0xEF).chr(0xBB).chr(0xBF);
    $csv = writeCsv($bom."name,type,latitude,longitude,crisis_slug\nFoo,market,5.55,-0.19,accra-flood-2026\n");

    $result = (new LandmarkBulkImportService(UndpUser::factory()->create()->id))->import($csv);

    expect($result->imported)->toBe(1);
});
