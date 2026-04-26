<?php

use App\Jobs\ExportReportsPdf;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('honors the locale passed at dispatch time, not the worker default (gap-39)', function () {
    $crisis = Crisis::factory()->create(['status' => 'active', 'name' => 'Locale Test Crisis']);
    DamageReport::factory()->count(2)->create(['crisis_id' => $crisis->id]);

    // Simulate a queue-worker context: framework locale starts at en, then the
    // job arrives with locale=fr captured at dispatch. Without the gap-39 fix,
    // the rendered PDF would use en regardless. With the fix, the job re-sets
    // the locale before any __() resolves.
    app()->setLocale('en');

    $filename = (new ExportReportsPdf(
        crisisId: $crisis->id,
        damageFilter: null,
        locale: 'fr',
    ))->handle();

    expect(app()->getLocale())->toBe('fr');
    expect(Storage::exists($filename))->toBeTrue();

    Storage::delete($filename);
});

it('falls back to current app locale when no locale is passed (back-compat)', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(2)->create(['crisis_id' => $crisis->id]);

    app()->setLocale('en');

    $filename = (new ExportReportsPdf(
        crisisId: $crisis->id,
    ))->handle();

    // No locale was passed -> the job leaves the worker's locale alone.
    expect(app()->getLocale())->toBe('en');
    expect(Storage::exists($filename))->toBeTrue();

    Storage::delete($filename);
});
