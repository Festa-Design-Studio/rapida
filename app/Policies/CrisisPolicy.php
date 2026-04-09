<?php

namespace App\Policies;

use App\Models\Crisis;
use App\Models\UndpUser;

class CrisisPolicy
{
    public function viewAny(UndpUser $user): bool
    {
        return true; // All UNDP staff can view crises
    }

    public function create(UndpUser $user): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin']);
    }

    public function update(UndpUser $user, Crisis $crisis): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin']);
    }

    public function delete(UndpUser $user, Crisis $crisis): bool
    {
        return $user->role->value === 'superadmin';
    }
}
