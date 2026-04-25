<?php

use App\Events\ReportSubmitted;
use App\Jobs\ClassifyDamageWithAI;
use App\Jobs\TranslateDescription;
use App\Listeners\DispatchReportProcessing;
use App\Models\Account;
use App\Models\Badge;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

uses(RefreshDatabase::class);

it('null-forces device_fingerprint_id in conflict crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => true,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'partial',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'conflict',
        'device_fingerprint_id' => 'abc-fingerprint-123',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->device_fingerprint_id)->toBeNull();
});

it('forces reporter_tier to anonymous in conflict crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => true,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'complete',
        'infrastructure_type' => 'commercial',
        'crisis_type' => 'conflict',
        'device_fingerprint_id' => 'should-be-nulled',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->reporter_tier)->toBe('anonymous');
});

it('preserves device_fingerprint_id in normal crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => false,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'minimal',
        'infrastructure_type' => 'residential',
        'crisis_type' => 'flood',
        'device_fingerprint_id' => 'abc-fingerprint-456',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->device_fingerprint_id)->toBe('abc-fingerprint-456');
});

it('resolves device reporter_tier when fingerprint provided in normal crisis', function () {
    $crisis = Crisis::factory()->create([
        'status' => 'active',
        'conflict_context' => false,
    ]);

    $this->postJson('/api/v1/reports', [
        'crisis_slug' => $crisis->slug,
        'damage_level' => 'partial',
        'infrastructure_type' => 'government',
        'crisis_type' => 'earthquake',
        'device_fingerprint_id' => 'abc-fingerprint-789',
        'submitted_at' => now()->toIso8601String(),
    ])->assertCreated();

    $report = DamageReport::first();

    expect($report->reporter_tier)->toBe('device');
});

it('does not award badges to logged-in accounts in conflict crises', function () {
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => true]);
    $account = Account::factory()->create();

    DamageReport::factory()->count(5)->create([
        'crisis_id' => $crisis->id,
        'account_id' => $account->id,
    ]);

    $latest = DamageReport::where('account_id', $account->id)->latest()->first();
    $awarded = app(BadgeService::class)->checkAndAwardBadges($latest);

    expect($awarded)->toBe([])
        ->and(Badge::where('account_id', $account->id)->count())->toBe(0);
});

it('awards badges normally to logged-in accounts in non-conflict crises', function () {
    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);
    $account = Account::factory()->create();

    DamageReport::factory()->count(5)->create([
        'crisis_id' => $crisis->id,
        'account_id' => $account->id,
    ]);

    $latest = DamageReport::where('account_id', $account->id)->latest()->first();
    $awarded = app(BadgeService::class)->checkAndAwardBadges($latest);

    expect($awarded)->toContain('first_report')
        ->and(Badge::where('account_id', $account->id)->count())->toBeGreaterThan(0);
});

it('skips ClassifyDamageWithAI dispatch in conflict crises', function () {
    Bus::fake([ClassifyDamageWithAI::class, TranslateDescription::class]);

    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => true]);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'photo_url' => 'photos/test.jpg',
        'description' => 'Damaged hospital wing',
    ]);

    app(DispatchReportProcessing::class)->handle(new ReportSubmitted($report));

    Bus::assertNotDispatched(ClassifyDamageWithAI::class);
    Bus::assertNotDispatched(TranslateDescription::class);
});

it('dispatches ClassifyDamageWithAI normally in non-conflict crises', function () {
    Bus::fake([ClassifyDamageWithAI::class]);

    $crisis = Crisis::factory()->create(['status' => 'active', 'conflict_context' => false]);
    $report = DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'photo_url' => 'photos/test.jpg',
    ]);

    app(DispatchReportProcessing::class)->handle(new ReportSubmitted($report));

    Bus::assertDispatched(ClassifyDamageWithAI::class);
});
