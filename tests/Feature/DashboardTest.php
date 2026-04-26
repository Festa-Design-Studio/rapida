<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\UndpUser;
use Database\Seeders\UndpUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects unauthenticated users from dashboard', function () {
    $this->get('/dashboard/analyst')->assertRedirect('/login');
});

it('shows analyst dashboard for authenticated UNDP user', function () {
    $user = UndpUser::factory()->create(['role' => 'analyst']);
    $this->actingAs($user, 'undp')
        ->get('/dashboard/analyst')
        ->assertOk();
});

it('shows field dashboard for authenticated UNDP user', function () {
    $user = UndpUser::factory()->create(['role' => 'field_coordinator']);
    $this->actingAs($user, 'undp')
        ->get('/dashboard/field')
        ->assertOk();
});

it('exports CSV for authenticated user', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create(['crisis_id' => $crisis->id]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/csv');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('exports GeoJSON for authenticated user', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.56,
        'longitude' => -0.20,
    ]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $response = $this->actingAs($user, 'undp')
        ->get('/dashboard/export/geojson');

    $response->assertOk();
});

it('flags a report', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create(['crisis_id' => $crisis->id, 'is_flagged' => false]);
    $user = UndpUser::factory()->create(['role' => 'analyst']);

    $this->actingAs($user, 'undp')
        ->post("/dashboard/reports/{$report->id}/flag")
        ->assertRedirect();

    expect($report->fresh()->is_flagged)->toBeTrue();
});

it('seeds the UNDP evaluator account with the analyst role', function () {
    // gap-30: docs/submission/evaluator-access.md hands these credentials to
    // UNDP InnoCentive reviewers. If this test fails, the access sheet is
    // out of sync with the seeder and the demo will reject the reviewer's
    // login. Lock the contract here.
    $this->seed(UndpUserSeeder::class);

    $evaluator = UndpUser::where('email', 'evaluator@undp.org')->first();

    expect($evaluator)->not->toBeNull()
        ->and((string) $evaluator->role->value)->toBe('analyst')
        ->and((bool) $evaluator->is_active)->toBeTrue();
});

it('lets the seeded UNDP evaluator log in and reach /dashboard/analyst', function () {
    $this->seed(UndpUserSeeder::class);

    $response = $this->post('/login', [
        'email' => 'evaluator@undp.org',
        'password' => 'rapida-demo-2026',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticatedAs(UndpUser::where('email', 'evaluator@undp.org')->first(), 'undp');

    $this->get('/dashboard/analyst')->assertOk();
});
