<?php

use App\Enums\UndpUserRole;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;

uses(RefreshDatabase::class);

it('records an activity row when a UndpUser is created', function () {
    $user = UndpUser::factory()->create([
        'name' => 'Audit Operator',
        'email' => 'audit@undp.test',
        'role' => UndpUserRole::Operator,
    ]);

    $activity = Activity::where('subject_type', UndpUser::class)
        ->where('subject_id', $user->id)
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull()
        ->and($activity->log_name)->toBe('undp_user')
        ->and($activity->event)->toBe('created')
        ->and($activity->attribute_changes['attributes']['email'])->toBe('audit@undp.test');
});

it('NEVER logs the password attribute (security-critical)', function () {
    $user = UndpUser::factory()->create();
    Activity::query()->delete();

    $user->update(['password' => 'a-new-bcrypt-input']);

    $activities = Activity::where('subject_type', UndpUser::class)->get();

    // Baseline: password updates may or may not produce an activity row
    // (depends on whether other logOnly fields also dirty), but no row
    // may EVER carry the password value in attribute_changes.
    expect($activities->count())->toBeGreaterThanOrEqual(0);

    foreach ($activities as $activity) {
        $changes = $activity->attribute_changes ?? [];
        $attributes = $changes['attributes'] ?? [];
        $old = $changes['old'] ?? [];

        expect($attributes)->not->toHaveKey('password')
            ->and($old)->not->toHaveKey('password');
    }
});

it('logs role and is_active changes for staff promotion / suspension flows', function () {
    $user = UndpUser::factory()->create([
        'role' => UndpUserRole::Analyst,
        'is_active' => true,
    ]);
    Activity::query()->delete();

    $user->update(['role' => UndpUserRole::Operator, 'is_active' => false]);

    $activity = Activity::where('subject_type', UndpUser::class)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    expect($activity->attribute_changes['attributes']['role'])->toBe(UndpUserRole::Operator->value)
        ->and($activity->attribute_changes['attributes']['is_active'])->toBeFalse();
});
