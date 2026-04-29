<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('returns 200 for the map home route', function () {
    $this->get('/')->assertOk();
});

it('renders the full-width Submit a Report section below the map', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee(__('rapida.submit_a_report'));
    $response->assertSee(__('rapida.report_damage'));
    // The submit anchor wrapper carries w-full on its button so the CTA
    // stretches edge-to-edge in the section.
    $response->assertSee('w-full', escape: false);
});

it('does not float the Report Damage button over the map', function () {
    $response = $this->get('/');

    $response->assertOk();
    // The legacy floating-button positioning has been retired. Ensure the
    // CTA is no longer overlaid on the map.
    $response->assertDontSee('left-1/2 -translate-x-1/2', escape: false);
    $response->assertDontSee('bottom-36', escape: false);
});

it('shows the photo URL only on cards verified by an analyst', function () {
    $crisis = Crisis::factory()->create();

    $verifiedReport = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'photo_url' => 'photos/verified-report.jpg',
    ]);

    Verification::create([
        'report_id' => $verifiedReport->id,
        'status' => 'verified',
        'verified_at' => now(),
    ]);

    $pendingReport = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'photo_url' => 'photos/pending-report.jpg',
    ]);

    Verification::create([
        'report_id' => $pendingReport->id,
        'status' => 'pending',
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('photos/verified-report.jpg');
    $response->assertDontSee('photos/pending-report.jpg');
});

it('hides photos for reports that have no verification record at all', function () {
    $crisis = Crisis::factory()->create();

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'photo_url' => 'photos/no-verification.jpg',
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertDontSee('photos/no-verification.jpg');
});

it('eager-loads the verification relation to avoid N+1', function () {
    $crisis = Crisis::factory()->create();

    DamageReport::factory()->count(5)->create(['crisis_id' => $crisis->id])
        ->each(fn ($r) => Verification::create([
            'report_id' => $r->id,
            'status' => 'pending',
        ]));

    $queries = collect();
    DB::listen(fn ($q) => $queries->push($q->sql));

    $this->get('/')->assertOk();

    // The verification table should be hit exactly once (eager load via
    // a single `where in (...)` lookup), not once per report.
    $verificationQueryCount = $queries
        ->filter(fn ($sql) => str_contains($sql, 'verifications'))
        ->count();

    expect($verificationQueryCount)->toBe(1);
});

it('renders the global footer with both attributions', function () {
    $response = $this->get('/');

    $response->assertOk();
    $response->assertSee('role="contentinfo"', false);
    $response->assertSee('UNDP Crisis Mapping Tool');
    $response->assertSee('Interface designed by Festa.');
    $response->assertSee('https://festa.design/');
});
