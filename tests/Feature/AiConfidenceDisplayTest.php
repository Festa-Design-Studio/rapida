<?php

use App\Jobs\ExportReportsCsv;
use App\Jobs\ExportReportsGeoJson;
use App\Models\Crisis;
use App\Models\DamageReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('shows AI confidence on the report detail page', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'ai_confidence' => 0.92,
        'ai_suggested_level' => 'complete',
    ]);

    $response = $this->get(route('report-detail', $report));

    $response->assertSuccessful();
    $response->assertSee('92%');
    $response->assertSee(__('rapida.ai_confidence_label'));
    $response->assertSee(__('rapida.ai_confidence_high'));
});

it('hides AI confidence row when ai_confidence is null', function () {
    $crisis = Crisis::factory()->create(['status' => 'active']);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'ai_confidence' => null,
    ]);

    $response = $this->get(route('report-detail', $report));

    $response->assertSuccessful();
    $response->assertDontSee(__('rapida.ai_confidence_label'));
});

it('maps confidence tiers correctly', function (float $confidence, string $expectedTier) {
    $tier = $confidence > 0.85 ? 'high' : ($confidence >= 0.60 ? 'medium' : 'low');
    expect($tier)->toBe($expectedTier);
})->with([
    'high confidence' => [0.92, 'high'],
    'medium confidence' => [0.75, 'medium'],
    'low confidence' => [0.45, 'low'],
    'boundary high' => [0.86, 'high'],
    'boundary medium-low' => [0.60, 'medium'],
    'boundary high-medium' => [0.85, 'medium'],
]);

it('includes ai_confidence and ai_suggested_level in CSV export', function () {
    Storage::fake();

    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'ai_confidence' => 0.88,
        'ai_suggested_level' => 'partial',
    ]);

    $job = new ExportReportsCsv($crisis->id);
    $filename = $job->handle();

    $csv = Storage::get($filename);

    expect($csv)->toContain('ai_confidence,ai_suggested_level');
    expect($csv)->toContain('0.88');
    expect($csv)->toContain('partial');
});

it('includes ai_confidence and ai_suggested_level in GeoJSON export', function () {
    Storage::fake();

    $crisis = Crisis::factory()->create(['status' => 'active']);
    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'latitude' => 5.55,
        'longitude' => -0.20,
        'ai_confidence' => 0.72,
        'ai_suggested_level' => 'minimal',
    ]);

    $job = new ExportReportsGeoJson($crisis->id);
    $filename = $job->handle();

    $geojson = json_decode(Storage::get($filename), true);

    $properties = $geojson['features'][0]['properties'];
    expect($properties)->toHaveKey('ai_confidence');
    expect($properties)->toHaveKey('ai_suggested_level');
    expect((float) $properties['ai_confidence'])->toBe(0.72);
    expect($properties['ai_suggested_level'])->toBe('minimal');
});

it('has all AI confidence translation keys in en, fr, and ar', function (string $locale) {
    $keys = [
        'rapida.ai_confidence_label',
        'rapida.ai_confidence_high',
        'rapida.ai_confidence_medium',
        'rapida.ai_confidence_low',
        'rapida.ai_suggestion_with_confidence',
    ];

    foreach ($keys as $key) {
        $translated = __($key, [], $locale);
        expect($translated)->not->toBe($key, "Missing translation for {$key} in {$locale}");
    }
})->with(['en', 'fr', 'ar']);
