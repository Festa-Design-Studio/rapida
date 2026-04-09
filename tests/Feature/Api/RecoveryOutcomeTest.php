<?php

use App\Events\RecoveryOutcomeCreated;
use App\Models\Crisis;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

it('returns outcomes for a crisis', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    RecoveryOutcome::factory()->count(3)->create(['crisis_id' => $crisis->id]);

    $response = $this->getJson("/api/v1/crises/{$crisis->slug}/outcomes");

    $response->assertSuccessful();
    expect($response->json())->toHaveCount(3);
});

it('filters outcomes by h3_cell', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $targetCell = 'abc123def456789';

    RecoveryOutcome::factory()->create([
        'crisis_id' => $crisis->id,
        'h3_cell_id' => $targetCell,
    ]);
    RecoveryOutcome::factory()->create([
        'crisis_id' => $crisis->id,
        'h3_cell_id' => 'other_cell_id_999',
    ]);

    $response = $this->getJson("/api/v1/crises/{$crisis->slug}/outcomes?h3_cell={$targetCell}");

    $response->assertSuccessful();
    expect($response->json())->toHaveCount(1);
    expect($response->json()[0]['h3_cell_id'])->toBe($targetCell);
});

it('creates an outcome with undp auth', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $user = UndpUser::factory()->create();

    Event::fake();

    $response = $this->actingAs($user, 'undp')
        ->postJson("/api/v1/crises/{$crisis->slug}/outcomes", [
            'h3_cell_id' => 'abc123def456789',
            'message' => 'Rubble cleared, road reopened.',
        ]);

    $response->assertCreated();
    expect(RecoveryOutcome::count())->toBe(1);

    $outcome = RecoveryOutcome::first();

    expect($outcome->crisis_id)->toBe($crisis->id);
    expect($outcome->h3_cell_id)->toBe('abc123def456789');
    expect($outcome->message)->toBe('Rubble cleared, road reopened.');
    expect($outcome->triggered_by)->toBe($user->id);
});

it('returns 401 for unauthenticated outcome creation', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    $response = $this->postJson("/api/v1/crises/{$crisis->slug}/outcomes", [
        'h3_cell_id' => 'abc123def456789',
        'message' => 'Rubble cleared.',
    ]);

    $response->assertUnauthorized();
    expect(RecoveryOutcome::count())->toBe(0);
});

it('dispatches RecoveryOutcomeCreated event on creation', function () {
    Event::fake([RecoveryOutcomeCreated::class]);

    $crisis = Crisis::factory()->create(['status' => 'active']);
    $user = UndpUser::factory()->create();

    $this->actingAs($user, 'undp')
        ->postJson("/api/v1/crises/{$crisis->slug}/outcomes", [
            'h3_cell_id' => 'abc123def456789',
            'message' => 'Power restored to the district.',
        ])
        ->assertCreated();

    Event::assertDispatched(RecoveryOutcomeCreated::class, function ($event) {
        return $event->outcome->message === 'Power restored to the district.';
    });
});
