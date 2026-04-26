<?php

use App\Models\Crisis;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('records an activity row when a RecoveryOutcome is created', function () {
    $crisis = Crisis::factory()->create();
    $operator = UndpUser::factory()->create();
    $outcome = RecoveryOutcome::create([
        'crisis_id' => $crisis->id,
        'h3_cell_id' => '882a10c969fffff',
        'message' => 'Plateau District: 14 homes added to shelter program.',
        'triggered_by' => $operator->id,
        'triggered_at' => now(),
    ]);

    $activity = Activity::where('subject_type', RecoveryOutcome::class)
        ->where('subject_id', $outcome->id)
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('recovery_outcome')
        ->and($activity->event)->toBe('created')
        ->and($activity->attribute_changes['attributes']['h3_cell_id'])->toBe('882a10c969fffff');
});

it('records the changed message when a RecoveryOutcome is updated', function () {
    $outcome = RecoveryOutcome::create([
        'crisis_id' => Crisis::factory()->create()->id,
        'h3_cell_id' => '882a10c969fffff',
        'message' => 'Original message',
        'triggered_by' => UndpUser::factory()->create()->id,
        'triggered_at' => now(),
    ]);
    Activity::query()->delete();

    $outcome->update(['message' => 'Updated message']);

    $activity = Activity::where('subject_type', RecoveryOutcome::class)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity->attribute_changes['attributes']['message'])->toBe('Updated message')
        ->and($activity->attribute_changes['old']['message'])->toBe('Original message');
});
