<?php

use App\Models\Account;
use App\Models\Crisis;
use App\Models\DamageReport;
use App\Services\AnalyticsQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('counts reports in the same H3 cell, ignoring other cells', function () {
    $crisis = Crisis::factory()->create();

    DamageReport::factory()->count(3)->create([
        'crisis_id' => $crisis->id,
        'h3_cell_id' => 'cell-here',
    ]);
    DamageReport::factory()->count(5)->create([
        'crisis_id' => $crisis->id,
        'h3_cell_id' => 'cell-there',
    ]);

    expect(app(AnalyticsQueryService::class)->reportsInH3Cell((string) $crisis->id, 'cell-here'))->toBe(3);
    expect(app(AnalyticsQueryService::class)->reportsInH3Cell((string) $crisis->id, 'cell-there'))->toBe(5);
    expect(app(AnalyticsQueryService::class)->reportsInH3Cell((string) $crisis->id, 'unknown-cell'))->toBe(0);
});

it('returns 0 when h3_cell_id is null', function () {
    $crisis = Crisis::factory()->create();
    DamageReport::factory()->count(2)->create(['crisis_id' => $crisis->id]);

    expect(app(AnalyticsQueryService::class)->reportsInH3Cell((string) $crisis->id, null))->toBe(0);
});

it('counts reports by account scoped to a crisis', function () {
    $crisis = Crisis::factory()->create();
    $other = Crisis::factory()->create();
    $account = Account::factory()->create();

    DamageReport::factory()->count(4)->create([
        'crisis_id' => $crisis->id,
        'account_id' => $account->id,
    ]);
    DamageReport::factory()->count(2)->create([
        'crisis_id' => $other->id,
        'account_id' => $account->id,
    ]);
    DamageReport::factory()->count(7)->create([
        'crisis_id' => $crisis->id,
        // Anonymous reports — should not count toward the account's tally
    ]);

    expect(app(AnalyticsQueryService::class)->reportsByAccount((string) $crisis->id, (string) $account->id))->toBe(4);
});

it('returns 0 reportsByAccount when accountId is null (anonymous reporter)', function () {
    $crisis = Crisis::factory()->create();
    DamageReport::factory()->count(3)->create(['crisis_id' => $crisis->id]);

    expect(app(AnalyticsQueryService::class)->reportsByAccount((string) $crisis->id, null))->toBe(0);
});
