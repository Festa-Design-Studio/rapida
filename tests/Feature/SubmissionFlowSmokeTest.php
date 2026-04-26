<?php

use App\Enums\DamageLevel;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\UndpUser;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * End-to-end smoke check that the seeders produce a usable Accra demo
 * crisis and that both the public reporter view and the authenticated
 * analyst view render against it. Run before recording the pitch video
 * (gap-03) and before any deploy verification (gap-04) — if this fails,
 * the demo is not ready.
 */
it('seeds the Accra crisis with a realistic report volume', function () {
    $this->seed(DatabaseSeeder::class);

    $crisis = Crisis::where('slug', 'accra-flood-2026')->first();
    expect($crisis)->not->toBeNull();

    $reportCount = DamageReport::where('crisis_id', $crisis->id)->count();
    expect($reportCount)->toBeGreaterThanOrEqual(50)
        ->and($reportCount)->toBeLessThanOrEqual(120);
});

it('renders the public Accra crisis page after seeding', function () {
    $this->seed(DatabaseSeeder::class);

    $response = $this->get('/crisis/accra-flood-2026');

    $response->assertOk();
});

it('renders the analyst dashboard for the seeded evaluator after seeding', function () {
    $this->seed(DatabaseSeeder::class);

    // Any seeded analyst-role staff user works as a stand-in until the
    // dedicated UNDP evaluator account ships in gap-30.
    $analyst = UndpUser::where('role', 'analyst')->first();
    expect($analyst)->not->toBeNull();

    $response = $this->actingAs($analyst, 'undp')->get('/dashboard/analyst');

    $response->assertOk();
});

it('produces a damage-level distribution close to the proposal mix', function () {
    $this->seed(DatabaseSeeder::class);

    $crisis = Crisis::where('slug', 'accra-flood-2026')->first();
    $reports = DamageReport::where('crisis_id', $crisis->id)->get();

    $byLevel = $reports->groupBy(fn ($r) => $r->damage_level instanceof DamageLevel
        ? $r->damage_level->value
        : $r->damage_level)->map->count();

    // Permissive bounds — the proposal calls for 40/35/25 minimal/partial/complete
    // and the seeder uses fixed counts (32/28/20 = 80 total), so each bucket
    // should be present and roughly proportional.
    expect($byLevel['minimal'] ?? 0)->toBeGreaterThan(20)
        ->and($byLevel['partial'] ?? 0)->toBeGreaterThan(15)
        ->and($byLevel['complete'] ?? 0)->toBeGreaterThan(10);
});
