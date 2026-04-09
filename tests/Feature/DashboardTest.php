<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\UndpUser;
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
