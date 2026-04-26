<?php

use App\Enums\UndpUserRole;
use App\Models\Crisis;
use App\Models\Landmark;
use App\Models\UndpUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->operator = UndpUser::factory()->create(['role' => UndpUserRole::Operator]);
    $this->actingAs($this->operator, 'undp');
});

it('imports a CSV via the Livewire bulkImport action', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);

    $csv = "name,type,latitude,longitude,crisis_slug\n".
           "Makola,market,5.5560,-0.1969,accra-flood-2026\n".
           "Korle Bu,hospital,5.55,-0.205,accra-flood-2026\n";
    $file = UploadedFile::fake()->createWithContent('landmarks.csv', $csv);

    Livewire::test('admin.landmark-manager')
        ->set('csvFile', $file)
        ->call('bulkImport');

    expect(Landmark::count())->toBe(2);
});

it('reports row-level errors back through the importResult DTO', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);

    $csv = "name,type,latitude,longitude,crisis_slug\n".
           "Good,market,5.55,-0.19,accra-flood-2026\n".
           "Bad,not-a-type,5.55,-0.19,accra-flood-2026\n";
    $file = UploadedFile::fake()->createWithContent('landmarks.csv', $csv);

    $component = Livewire::test('admin.landmark-manager')
        ->set('csvFile', $file)
        ->call('bulkImport');

    expect($component->get('importResult')['imported'])->toBe(1)
        ->and($component->get('importResult')['skipped'])->toBe(1)
        ->and(Landmark::count())->toBe(1);
});

it('attributes imported landmarks to the authenticated operator', function () {
    Crisis::factory()->create(['slug' => 'accra-flood-2026']);
    $csv = "name,type,latitude,longitude,crisis_slug\nFoo,market,5.55,-0.19,accra-flood-2026\n";
    $file = UploadedFile::fake()->createWithContent('landmarks.csv', $csv);

    Livewire::test('admin.landmark-manager')
        ->set('csvFile', $file)
        ->call('bulkImport');

    expect(Landmark::first()->added_by)->toBe($this->operator->id);
});
