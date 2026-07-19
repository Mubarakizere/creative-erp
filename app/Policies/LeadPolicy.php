<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete');
    }

    public function restore(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function convert(User $user, Lead $lead): bool
    {
        if ($user->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        // Cannot convert already converted leads
        if ($lead->status === 'Converted') {
            return false;
        }

        return $user->hasPermissionTo('crm.convert');
    }
}
