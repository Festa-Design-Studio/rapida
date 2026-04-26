<?php

use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Wizard photo + fingerprint submission contract
|--------------------------------------------------------------------------
|
| Locks two regression-prone bugs:
|   1. The photo file must reach submit() — earlier the nested
|      step-photo component owned its own $photo property, dispatched
|      it to the parent via event, and Livewire serialised the file
|      object to a string at the boundary. Reports got placeholder.jpg.
|   2. The device_fingerprint_id must be read from the cookie, not from
|      a never-populated request input. Earlier reports persisted with
|      device_fingerprint_id = NULL so /my-reports never re-found them.
|
*/

it('persists the uploaded photo (not the placeholder) when submit is called', function () {
    Storage::fake('public');
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $photo = UploadedFile::fake()->image('damage.jpg', 800, 600);

    Livewire::test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('photo', $photo)
        ->set('latitude', 5.55)
        ->set('longitude', -0.20)
        ->set('damageLevel', 'partial')
        ->set('infrastructureTypes', ['commercial'])
        ->set('crisisType', 'flood')
        ->call('submit')
        ->assertSet('currentStep', 7)
        ->assertSet('submitError', null);

    $report = DamageReport::latest('submitted_at')->first();
    expect($report)->not->toBeNull()
        ->and($report->photo_url)->not->toContain('placeholder')
        ->and($report->photo_url)->toStartWith('photos/');
});

it('persists the device_fingerprint_id from the cookie on submit', function () {
    Storage::fake('public');
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);
    $photo = UploadedFile::fake()->image('damage.jpg');
    $fingerprint = 'browser-cookie-uuid-xyz';

    Livewire::withCookies(['rapida_device_fingerprint' => $fingerprint])
        ->test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('photo', $photo)
        ->set('latitude', 5.55)
        ->set('longitude', -0.20)
        ->set('damageLevel', 'partial')
        ->set('infrastructureTypes', ['commercial'])
        ->set('crisisType', 'flood')
        ->call('submit');

    expect(DamageReport::latest('submitted_at')->first()->device_fingerprint_id)
        ->toBe($fingerprint);
});

it('does not persist a fingerprint in conflict mode even with the cookie set', function () {
    Storage::fake('public');
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => true]);
    $photo = UploadedFile::fake()->image('damage.jpg');

    Livewire::withCookies(['rapida_device_fingerprint' => 'should-be-stripped'])
        ->test('wizard.wizard-shell', ['crisis' => $crisis])
        ->set('photo', $photo)
        ->set('latitude', 5.55)
        ->set('longitude', -0.20)
        ->set('damageLevel', 'partial')
        ->set('infrastructureTypes', ['commercial'])
        ->set('crisisType', 'flood')
        ->call('submit');

    expect(DamageReport::latest('submitted_at')->first()->device_fingerprint_id)->toBeNull();
});
