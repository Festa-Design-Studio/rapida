<?php

use App\Models\Crisis;
use App\Models\Landmark;
use App\Models\RecoveryOutcome;
use App\Models\UndpUser;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

/*
|--------------------------------------------------------------------------
| Audit-log coverage (gap-37)
|--------------------------------------------------------------------------
|
| The four operator-side models must all carry the LogsActivity trait so
| every staff mutation produces an audit_log row. Adding a fifth
| operator-side model in future without an audit trail would silently
| undermine compliance — this test makes the omission a CI failure.
|
*/

it('all operator-side models carry the LogsActivity trait', function (string $modelClass) {
    expect(class_uses_recursive($modelClass))->toContain(LogsActivity::class);
})->with([
    Crisis::class,
    Landmark::class,
    RecoveryOutcome::class,
    UndpUser::class,
]);

it('every LogsActivity model implements getActivitylogOptions()', function (string $modelClass) {
    $reflection = new ReflectionClass($modelClass);
    expect($reflection->hasMethod('getActivitylogOptions'))->toBeTrue();
})->with([
    Crisis::class,
    Landmark::class,
    RecoveryOutcome::class,
    UndpUser::class,
]);
