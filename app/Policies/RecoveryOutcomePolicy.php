<?php

namespace App\Policies;

use App\Models\RecoveryOutcome;
use App\Models\UndpUser;

/**
 * Gap-40: RecoveryOutcomeController only required auth:undp before this PR,
 * so any UNDP user (including a field coordinator) could broadcast a recovery
 * outcome. PRD-implied requirement: only operators and analysts. Field
 * coordinators receive outcomes but do not author them.
 */
class RecoveryOutcomePolicy
{
    public function viewAny(UndpUser $user): bool
    {
        return true; // All UNDP staff and reporters can view outcomes
    }

    public function create(UndpUser $user): bool
    {
        return in_array($user->role->value, ['analyst', 'operator', 'superadmin'], true);
    }

    public function update(UndpUser $user, RecoveryOutcome $outcome): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }

    public function delete(UndpUser $user, RecoveryOutcome $outcome): bool
    {
        return $user->role->value === 'superadmin';
    }
}
