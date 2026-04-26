<?php

use App\Enums\UndpUserRole;
use App\Models\Crisis;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('records an activity row when a Crisis is created', function () {
    $crisis = Crisis::factory()->create(['name' => 'Audit Test Crisis']);

    $activity = Activity::where('subject_type', Crisis::class)
        ->where('subject_id', $crisis->id)
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('crisis')
        ->and($activity->event)->toBe('created')
        ->and($activity->attribute_changes['attributes']['name'])->toBe('Audit Test Crisis');
});

it('records the changed attributes when a Crisis is updated', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    Activity::query()->delete();

    $crisis->update(['conflict_context' => true, 'name' => 'Updated Name']);

    $activity = Activity::where('subject_type', Crisis::class)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->attribute_changes['attributes']['conflict_context'])->toBeTrue()
        ->and($activity->attribute_changes['old']['conflict_context'])->toBeFalse()
        ->and($activity->attribute_changes['attributes']['name'])->toBe('Updated Name');
});

it('attributes the change to the authenticated UndpUser causer', function () {
    $operator = UndpUser::factory()->create(['role' => UndpUserRole::Operator]);
    $this->actingAs($operator, 'undp');

    $crisis = Crisis::factory()->create();

    $activity = Activity::where('subject_type', Crisis::class)
        ->where('subject_id', $crisis->id)
        ->latest('id')
        ->first();

    expect($activity->causer_type)->toBe(UndpUser::class)
        ->and($activity->causer_id)->toBe($operator->id);
});

it('does not include unrelated attributes in attribute_changes (logOnly scope)', function () {
    $crisis = Crisis::factory()->create();
    Activity::query()->delete();

    // qr_code_url is not in the logOnly list — even when it changes, the
    // value must not appear in the audit row's attribute_changes payload
    // (otherwise the audit log leaks attributes the operator didn't ask
    // to track).
    $crisis->update(['qr_code_url' => 'https://example.com/qr.png']);

    $activity = Activity::where('event', 'updated')->latest('id')->first();
    $attributes = $activity?->attribute_changes['attributes'] ?? [];

    expect($attributes)->not->toHaveKey('qr_code_url');
});
