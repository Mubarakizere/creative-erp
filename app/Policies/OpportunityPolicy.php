<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Opportunity $opportunity): bool
    {
        if ($user->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Opportunity $opportunity): bool
    {
        if ($user->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        if ($user->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete');
    }

    public function restore(User $user, Opportunity $opportunity): bool
    {
        if ($user->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Opportunity $opportunity): bool
    {
        if ($user->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
