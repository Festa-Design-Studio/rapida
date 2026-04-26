<?php

namespace App\Policies;

use App\Models\UndpUser;

/**
 * Gap-40: user-manager Livewire component lets operators create/deactivate/
 * delete UNDP staff. Without an explicit policy, that authority sits on the
 * route middleware alone. Belt-and-suspenders: the policy guards each action
 * regardless of how the component is reached.
 *
 * Field coordinators and analysts cannot manage other users.
 */
class UndpUserPolicy
{
    public function viewAny(UndpUser $user): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }

    public function create(UndpUser $user): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }

    public function update(UndpUser $user, UndpUser $target): bool
    {
        // Operators can manage analysts and field-coordinators, not other operators.
        // Superadmins can manage anyone except other superadmins (single source of
        // truth — superadmin role assignment is via tinker only).
        if ($user->role->value === 'superadmin') {
            return $target->role->value !== 'superadmin' || $user->id === $target->id;
        }
        if ($user->role->value === 'operator') {
            return in_array($target->role->value, ['field_coordinator', 'analyst'], true);
        }

        return false;
    }

    public function delete(UndpUser $user, UndpUser $target): bool
    {
        // Same constraint as update: superadmin deletes anyone non-superadmin;
        // operator deletes only field coordinators and analysts. Self-delete
        // is forbidden to avoid locking out the only superadmin.
        if ($user->id === $target->id) {
            return false;
        }

        return $this->update($user, $target);
    }
}
