<?php

use App\Enums\UndpUserRole;
use App\Models\Crisis;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = UndpUser::factory()->create(['role' => UndpUserRole::Operator]);
    $this->actingAs($this->operator, 'undp');
});

it('persists every expanded field when an operator creates a crisis', function () {
    Livewire::test('admin.crisis-manager')
        ->set('name', 'Test Flood 2026')
        ->set('slug', 'test-flood-2026')
        ->set('defaultLanguage', 'fr')
        ->set('availableLanguages', ['en', 'fr', 'ar'])
        ->set('status', 'active')
        ->set('crisisTypeDefault', 'flood')
        ->set('h3Resolution', 8)
        ->set('dataRetentionDays', 180)
        ->set('multiPhotoEnabled', true)
        ->set('multiPhotoMax', 7)
        ->set('dangerZonesEnabled', true)
        ->call('createCrisis');

    $crisis = Crisis::where('slug', 'test-flood-2026')->first();

    expect($crisis)->not->toBeNull()
        ->and($crisis->default_language)->toBe('fr')
        ->and($crisis->available_languages)->toBe(['en', 'fr', 'ar'])
        ->and($crisis->crisis_type_default)->toBe('flood')
        ->and($crisis->h3_resolution)->toBe(8)
        ->and($crisis->data_retention_days)->toBe(180)
        ->and($crisis->multi_photo_enabled)->toBeTrue()
        ->and($crisis->multi_photo_max)->toBe(7)
        ->and($crisis->danger_zones_enabled)->toBeTrue()
        ->and($crisis->conflict_context)->toBeFalse();
});

it('opens the conflict_context confirmation modal on click and gates the persisted value', function () {
    $component = Livewire::test('admin.crisis-manager');

    $component->call('requestConflictContextEnable')
        ->assertSet('confirmConflictContext', true)
        ->assertSet('conflictContext', false);

    $component->call('confirmConflictContextChange')
        ->assertSet('confirmConflictContext', false)
        ->assertSet('conflictContext', true);
});

it('cancels the conflict_context modal without enabling the flag', function () {
    Livewire::test('admin.crisis-manager')
        ->call('requestConflictContextEnable')
        ->call('cancelConflictContext')
        ->assertSet('conflictContext', false)
        ->assertSet('confirmConflictContext', false);
});

it('round-trips every expanded field through editCrisis()', function () {
    $crisis = Crisis::factory()->create([
        'name' => 'Original',
        'slug' => 'original-slug',
        'default_language' => 'ar',
        'available_languages' => ['en', 'ar'],
        'status' => 'active',
        'conflict_context' => true,
        'crisis_type_default' => 'earthquake',
        'h3_resolution' => 8,
        'data_retention_days' => 730,
        'multi_photo_enabled' => true,
        'multi_photo_max' => 4,
        'danger_zones_enabled' => true,
    ]);

    Livewire::test('admin.crisis-manager')
        ->call('editCrisis', $crisis->id)
        ->assertSet('name', 'Original')
        ->assertSet('defaultLanguage', 'ar')
        ->assertSet('availableLanguages', ['en', 'ar'])
        ->assertSet('conflictContext', true)
        ->assertSet('crisisTypeDefault', 'earthquake')
        ->assertSet('h3Resolution', 8)
        ->assertSet('dataRetentionDays', 730)
        ->assertSet('multiPhotoEnabled', true)
        ->assertSet('multiPhotoMax', 4)
        ->assertSet('dangerZonesEnabled', true);
});

it('rejects an empty available_languages list', function () {
    Livewire::test('admin.crisis-manager')
        ->set('name', 'Test')
        ->set('slug', 'test')
        ->set('availableLanguages', [])
        ->call('createCrisis')
        ->assertHasErrors('availableLanguages');
});

it('rejects an out-of-range multi_photo_max', function () {
    Livewire::test('admin.crisis-manager')
        ->set('name', 'Test')
        ->set('slug', 'test')
        ->set('multiPhotoMax', 99)
        ->call('createCrisis')
        ->assertHasErrors('multiPhotoMax');
});
