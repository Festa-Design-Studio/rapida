<?php

use App\Models\Crisis;
use App\Models\Landmark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('records an activity row when a Landmark is created', function () {
    $crisis = Crisis::factory()->create();
    $landmark = Landmark::factory()->create([
        'crisis_id' => $crisis->id,
        'name' => 'Independence Square',
    ]);

    $activity = Activity::where('subject_type', Landmark::class)
        ->where('subject_id', $landmark->id)
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('landmark')
        ->and($activity->event)->toBe('created')
        ->and($activity->attribute_changes['attributes']['name'])->toBe('Independence Square');
});

it('records the changed attributes when a Landmark is updated', function () {
    $landmark = Landmark::factory()->create(['name' => 'Original']);
    Activity::query()->delete();

    $landmark->update(['name' => 'Renamed']);

    $activity = Activity::where('subject_type', Landmark::class)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity->attribute_changes['attributes']['name'])->toBe('Renamed')
        ->and($activity->attribute_changes['old']['name'])->toBe('Original');
});

it('records a deletion event when a Landmark is removed', function () {
    $landmark = Landmark::factory()->create();
    $id = $landmark->id;
    Activity::query()->delete();

    $landmark->delete();

    $activity = Activity::where('subject_type', Landmark::class)
        ->where('subject_id', $id)
        ->where('event', 'deleted')
        ->first();

    expect($activity)->not->toBeNull();
});
