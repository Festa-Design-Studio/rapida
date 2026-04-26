<?php

namespace App\Policies;

use App\Models\Landmark;
use App\Models\UndpUser;

/**
 * Gap-40: previously the landmark admin Livewire component relied solely on
 * the EnsureIsOperator route middleware. If a future PR ever exposes the
 * component on a different route or via a sub-page that forgets the
 * middleware, the gate disappears. Per-action policy + explicit authorize()
 * calls in the component make the gate route-independent.
 */
class LandmarkPolicy
{
    public function viewAny(UndpUser $user): bool
    {
        return true; // All UNDP staff can view landmarks (read-only)
    }

    public function create(UndpUser $user): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }

    public function update(UndpUser $user, Landmark $landmark): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }

    public function delete(UndpUser $user, Landmark $landmark): bool
    {
        return in_array($user->role->value, ['operator', 'superadmin'], true);
    }
}
