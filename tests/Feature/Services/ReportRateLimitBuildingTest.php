<?php

use App\DataTransferObjects\SubmitReportData;
use App\Exceptions\ReportRateLimitedException;
use App\Models\Account;
use App\Models\Building;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\ReportSubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('rejects a second report for the same building+account within 24 hours (gap-52)', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'b1']);
    $account = Account::factory()->create();

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'account_id' => $account->id,
        'submitted_at' => now()->subHours(2),
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        accountId: (string) $account->id,
        buildingFootprintId: (string) $building->id,
        submittedVia: 'web',
    );

    expect(fn () => app(ReportSubmissionService::class)->submit($data))
        ->toThrow(ReportRateLimitedException::class);
});

it('allows a second report for the same building+account after 24 hours', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'b2']);
    $account = Account::factory()->create();

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'account_id' => $account->id,
        'submitted_at' => now()->subDays(2),
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        accountId: (string) $account->id,
        buildingFootprintId: (string) $building->id,
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);
    expect($report)->toBeInstanceOf(DamageReport::class);
});

it('allows truly anonymous reporters to submit twice for the same building (no identifier to scope to)', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => false]);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'b3']);

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'account_id' => null,
        'device_fingerprint_id' => null,
        'submitted_at' => now()->subHours(1),
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        buildingFootprintId: (string) $building->id,
        submittedVia: 'web',
    );

    $report = app(ReportSubmissionService::class)->submit($data);
    expect($report)->toBeInstanceOf(DamageReport::class);
});

it('does not enforce the rule in conflict mode (privacy gate disables identification)', function () {
    $crisis = Crisis::factory()->create(['conflict_context' => true]);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'b4']);
    $account = Account::factory()->create();

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'account_id' => $account->id,
        'submitted_at' => now()->subHours(2),
    ]);

    $data = new SubmitReportData(
        crisis: $crisis,
        damageLevel: 'partial',
        infrastructureType: 'commercial',
        crisisType: 'flood',
        accountId: (string) $account->id,
        buildingFootprintId: (string) $building->id,
        submittedVia: 'web',
    );

    // Should not throw — conflict mode forces anonymous tier and disables this rule.
    $report = app(ReportSubmissionService::class)->submit($data);
    expect($report)->toBeInstanceOf(DamageReport::class);
});

it('returns 429 with localised message via the API controller (gap-52)', function () {
    $crisis = Crisis::factory()->create(['slug' => 'rl-building-test', 'status' => 'active', 'conflict_context' => false]);
    $building = Building::create(['crisis_id' => $crisis->id, 'ms_building_id' => 'b5']);

    DamageReport::factory()->create([
        'crisis_id' => $crisis->id,
        'building_footprint_id' => $building->id,
        'device_fingerprint_id' => 'fp-xyz-123',
        'submitted_at' => now()->subHours(3),
    ]);

    $response = $this->postJson('/api/v1/reports', [
        'crisis_slug' => 'rl-building-test',
        'damage_level' => 'partial',
        'infrastructure_type' => 'commercial',
        'crisis_type' => 'flood',
        'building_footprint_id' => $building->id,
        'device_fingerprint_id' => 'fp-xyz-123',
        'submitted_at' => now()->toIso8601String(),
    ]);

    $response->assertStatus(429);
    $response->assertJson(['reason' => 'building_rate_limit']);
});
