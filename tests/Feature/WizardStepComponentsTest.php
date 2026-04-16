<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders step-photo with crisis and conflict mode props', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-photo', ['crisis' => $crisis, 'conflictMode' => false])
        ->assertSeeHtml(__('wizard.step_1_title'))
        ->assertSet('conflictMode', false);
});

it('renders step-location with crisis context', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-location', ['crisis' => $crisis, 'conflictMode' => false])
        ->assertSeeHtml(__('wizard.step_2_title'));
});

it('step-location handles landmark selection', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-location', ['crisis' => $crisis, 'conflictMode' => false])
        ->call('selectLandmark', 'lm-1', 5.565, -0.178, 'Osu Castle')
        ->assertSet('latitude', 5.565)
        ->assertSet('longitude', -0.178)
        ->assertSet('landmarkText', 'Osu Castle')
        ->assertSet('locationMethod', 'landmark_picker');
});

it('renders step-damage with damage level radio buttons', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-damage', [
        'crisis' => $crisis,
        'conflictMode' => false,
        'hasPhoto' => false,
    ])
        ->assertSeeHtml(__('wizard.step_3_title'))
        ->assertSeeHtml('value="minimal"')
        ->assertSeeHtml('value="partial"')
        ->assertSeeHtml('value="complete"');
});

it('renders step-infrastructure with fieldsets', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-infrastructure', ['crisis' => $crisis, 'conflictMode' => false])
        ->assertSeeHtml(__('wizard.step_4_title'))
        ->assertSeeHtml(__('wizard.infra_type_label'));
});

it('renders step-modular with crisis modules', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => ['electricity', 'health', 'pressing_needs'],
    ]);

    Livewire::test('wizard.step-modular', ['crisis' => $crisis, 'conflictMode' => false])
        ->assertSeeHtml(__('wizard.step_5_title'))
        ->assertSeeHtml(__('wizard.module_electricity'));
});

it('renders step-review with collected step data', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-review', [
        'crisis' => $crisis,
        'conflictMode' => false,
        'stepData' => [
            'damageLevel' => 'partial',
            'latitude' => 5.565,
            'longitude' => -0.178,
            'infrastructureTypes' => ['community'],
            'crisisType' => 'flood',
        ],
    ])
        ->assertSeeHtml(__('wizard.step_6_title'))
        ->assertSeeHtml('Partial')
        ->assertSeeHtml('Flood')
        ->assertSeeHtml('5.5650, -0.1780');
});

it('step-review dispatches report-submitted on submit', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => [],
    ]);

    Livewire::test('wizard.step-review', [
        'crisis' => $crisis,
        'conflictMode' => false,
        'stepData' => [
            'damageLevel' => 'partial',
            'crisisType' => 'flood',
            'infrastructureTypes' => ['community'],
        ],
    ])
        ->call('submit')
        ->assertDispatched('report-submitted');

    expect(DamageReport::count())->toBe(1);
});

it('renders step-confirmation with report details', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);

    Livewire::test('wizard.step-confirmation', [
        'crisis' => $crisis,
        'reportId' => 'test-report-id',
        'communityReportCount' => 42,
        'damageLevel' => 'partial',
    ])
        ->assertSeeHtml(__('wizard.btn_submit_another'));
});

it('wizard shell uses ConflictModeService to compute conflict mode', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => true,
        'active_modules' => [],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->assertSet('conflictMode', true);
});

it('wizard shell uses ReportSubmissionService for submit', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'active_modules' => [],
    ]);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('damageLevel', 'complete')
        ->set('latitude', 5.565)
        ->set('longitude', -0.178)
        ->set('crisisType', 'earthquake')
        ->set('infrastructureTypes', ['government'])
        ->call('submit')
        ->assertSet('currentStep', 7)
        ->assertNotSet('reportId', null);

    $report = DamageReport::first();
    expect($report)->not->toBeNull()
        ->and($report->damage_level->value)->toBe('complete')
        ->and($report->crisis_type)->toBe('earthquake')
        ->and($report->infrastructure_type)->toBe('government');
});
