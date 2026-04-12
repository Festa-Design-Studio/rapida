<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('submits a damage report via the wizard component', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => ['electricity', 'health', 'pressing_needs'],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('damageLevel', 'partial')
        ->set('latitude', 5.565)
        ->set('longitude', -0.178)
        ->set('locationMethod', 'landmark_picker')
        ->set('landmarkText', 'Osu Castle')
        ->set('infrastructureTypes', ['community'])
        ->set('crisisType', 'flood')
        ->call('submit')
        ->assertSet('currentStep', 7)
        ->assertNotSet('reportId', null)
        ->assertSet('submitError', null);

    expect(DamageReport::count())->toBe(1);

    $report = DamageReport::first();
    expect($report->damage_level->value)->toBe('partial')
        ->and($report->crisis_type)->toBe('flood')
        ->and($report->infrastructure_type)->toBe('community')
        ->and($report->latitude)->toBe(5.565)
        ->and($report->longitude)->toBe(-0.178)
        ->and($report->landmark_text)->toBe('Osu Castle');
});

it('renders the submit button with wire:key on step 6', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => [],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('currentStep', 6)
        ->assertSeeHtml('wire:key="btn-submit"')
        ->assertSeeHtml('wire:click="submit"');
});

it('renders the continue button with wire:key on steps before 6', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => [],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('currentStep', 3)
        ->assertSeeHtml('wire:key="btn-next"')
        ->assertSeeHtml('wire:click="nextStep"');
});

it('navigates through all wizard steps', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => [],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->assertSet('currentStep', 1)
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        ->call('nextStep')
        ->assertSet('currentStep', 3)
        ->call('nextStep')
        ->assertSet('currentStep', 4)
        ->call('nextStep')
        ->assertSet('currentStep', 5)
        ->call('nextStep')
        ->assertSet('currentStep', 6)
        ->call('prevStep')
        ->assertSet('currentStep', 5);
});
