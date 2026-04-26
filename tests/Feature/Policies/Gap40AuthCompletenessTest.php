<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use App\Models\Landmark;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;

uses(RefreshDatabase::class);

/**
 * Gap-40: end-to-end policy enforcement for Landmark, RecoveryOutcome,
 * UndpUser. Each test asserts the role matrix the policy documents.
 */
it('LandmarkPolicy: only operator/superadmin can create or delete landmarks', function () {
    $crisis = Crisis::factory()->create();
    $landmark = Landmark::factory()->create(['crisis_id' => $crisis->id]);

    foreach (['field_coordinator', 'analyst'] as $role) {
        $user = UndpUser::factory()->create(['role' => $role]);
        expect(Gate::forUser($user)->allows('create', Landmark::class))->toBeFalse("$role should not create");
        expect(Gate::forUser($user)->allows('delete', $landmark))->toBeFalse("$role should not delete");
    }

    foreach (['operator', 'superadmin'] as $role) {
        $user = UndpUser::factory()->create(['role' => $role]);
        expect(Gate::forUser($user)->allows('create', Landmark::class))->toBeTrue("$role should create");
        expect(Gate::forUser($user)->allows('delete', $landmark))->toBeTrue("$role should delete");
    }
});

it('RecoveryOutcomePolicy: only analyst+ can create; only operator+ can update; only superadmin can delete', function () {
    $crisis = Crisis::factory()->create();
    $outcome = RecoveryOutcome::factory()->create(['crisis_id' => $crisis->id]);

    $field = UndpUser::factory()->create(['role' => 'field_coordinator']);
    expect(Gate::forUser($field)->allows('create', RecoveryOutcome::class))->toBeFalse();

    $analyst = UndpUser::factory()->create(['role' => 'analyst']);
    expect(Gate::forUser($analyst)->allows('create', RecoveryOutcome::class))->toBeTrue();
    expect(Gate::forUser($analyst)->allows('update', $outcome))->toBeFalse();
    expect(Gate::forUser($analyst)->allows('delete', $outcome))->toBeFalse();

    $operator = UndpUser::factory()->create(['role' => 'operator']);
    expect(Gate::forUser($operator)->allows('update', $outcome))->toBeTrue();
    expect(Gate::forUser($operator)->allows('delete', $outcome))->toBeFalse();

    $superadmin = UndpUser::factory()->create(['role' => 'superadmin']);
    expect(Gate::forUser($superadmin)->allows('delete', $outcome))->toBeTrue();
});

it('UndpUserPolicy: operators manage analyst+field_coordinator; superadmins manage non-superadmins; nobody self-deletes', function () {
    $field = UndpUser::factory()->create(['role' => 'field_coordinator']);
    $analyst = UndpUser::factory()->create(['role' => 'analyst']);
    $operator = UndpUser::factory()->create(['role' => 'operator']);
    $operator2 = UndpUser::factory()->create(['role' => 'operator']);
    $superadmin = UndpUser::factory()->create(['role' => 'superadmin']);

    // Field coordinator + analyst cannot manage anyone
    expect(Gate::forUser($field)->allows('viewAny', UndpUser::class))->toBeFalse();
    expect(Gate::forUser($analyst)->allows('viewAny', UndpUser::class))->toBeFalse();

    // Operator can manage field_coordinator + analyst
    expect(Gate::forUser($operator)->allows('update', $field))->toBeTrue();
    expect(Gate::forUser($operator)->allows('update', $analyst))->toBeTrue();
    // Operator cannot manage other operators or superadmins
    expect(Gate::forUser($operator)->allows('update', $operator2))->toBeFalse();
    expect(Gate::forUser($operator)->allows('update', $superadmin))->toBeFalse();

    // Superadmin can manage non-superadmins
    expect(Gate::forUser($superadmin)->allows('update', $field))->toBeTrue();
    expect(Gate::forUser($superadmin)->allows('update', $analyst))->toBeTrue();
    expect(Gate::forUser($superadmin)->allows('update', $operator))->toBeTrue();

    // Nobody can self-delete
    expect(Gate::forUser($superadmin)->allows('delete', $superadmin))->toBeFalse();
    expect(Gate::forUser($operator)->allows('delete', $operator))->toBeFalse();
});

it('all five policies are registered with the Gate facade', function () {
    expect(Gate::getPolicyFor(DamageReport::class))->not->toBeNull();
    expect(Gate::getPolicyFor(Crisis::class))->not->toBeNull();
    expect(Gate::getPolicyFor(Landmark::class))->not->toBeNull();
    expect(Gate::getPolicyFor(RecoveryOutcome::class))->not->toBeNull();
    expect(Gate::getPolicyFor(UndpUser::class))->not->toBeNull();
});
