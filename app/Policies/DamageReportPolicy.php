<?php

namespace App\Policies;

use App\Models\DamageReport;
use App\Models\UndpUser;

class DamageReportPolicy
{
    public function view(?UndpUser $user, DamageReport $report): bool
    {
        return true; // Reports are public
    }

    public function flag(UndpUser $user, DamageReport $report): bool
    {
        return in_array($user->role->value, ['analyst', 'operator', 'superadmin', 'field_coordinator']);
    }

    public function verify(UndpUser $user, DamageReport $report): bool
    {
        return in_array($user->role->value, ['analyst', 'operator', 'superadmin', 'field_coordinator']);
    }

    public function delete(UndpUser $user, DamageReport $report): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin']);
    }
}
