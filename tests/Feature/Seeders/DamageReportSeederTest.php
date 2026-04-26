<?php

use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use Database\Seeders\DamageReportSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| DamageReportSeeder — demo data shape contract
|--------------------------------------------------------------------------
|
| Locks the demo data contract so the UNDP demo URL is reproducibly
| populated:
|   - 78 reports against the Accra crisis
|   - exactly 3 demo personas with stable fingerprints (gap-32 walk-through)
|   - bulk anonymous reports populate the heatmap but are NOT findable
|     via /my-reports for any cookie value
|   - idempotent: re-running produces the same shape
|
*/

function seedAccraDemo(): Crisis
{
    $crisis = Crisis::factory()->create(['slug' => 'accra-flood-2026', 'status' => 'active']);
    Building::factory()->count(5)->create(['crisis_id' => $crisis->id]);

    return $crisis;
}

it('seeds the documented persona reports against the Accra crisis', function () {
    seedAccraDemo();

    (new DamageReportSeeder)->run();

    foreach ([
        DamageReportSeeder::PERSONA_RESIDENT,
        DamageReportSeeder::PERSONA_SHOPKEEPER,
        DamageReportSeeder::PERSONA_VOLUNTEER,
    ] as $persona) {
        expect(DamageReport::where('device_fingerprint_id', $persona)->count())
            ->toBe(6, "Persona {$persona} should have 6 reports");
    }
});

it('produces 78 reports total with the documented anonymous/persona split', function () {
    seedAccraDemo();

    (new DamageReportSeeder)->run();

    expect(DamageReport::count())->toBe(78)
        ->and(DamageReport::whereNotNull('device_fingerprint_id')->count())->toBe(18)
        ->and(DamageReport::whereNull('device_fingerprint_id')->count())->toBe(60);
});

it('is idempotent — re-running produces the same row count', function () {
    seedAccraDemo();

    (new DamageReportSeeder)->run();
    $firstRun = DamageReport::count();

    (new DamageReportSeeder)->run();
    expect(DamageReport::count())->toBe($firstRun);
});

it('does nothing when no Accra crisis exists', function () {
    (new DamageReportSeeder)->run();

    expect(DamageReport::count())->toBe(0);
});

it('produces realistic Accra coordinates (within Greater Accra bounding box)', function () {
    seedAccraDemo();

    (new DamageReportSeeder)->run();

    $outOfBounds = DamageReport::where(function ($q) {
        $q->whereNotBetween('latitude', [5.4, 5.7])
            ->orWhereNotBetween('longitude', [-0.3, -0.1]);
    })->count();

    expect($outOfBounds)->toBe(0);
});
