<?php

namespace App\Policies;

use App\Models\Opportunity;
use App\Models\User;

class OpportunityPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('opportunity.view');
    }

    public function view(User $user, Opportunity $opportunity): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $opportunity->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->id === $opportunity->assigned_to || $user->id === $opportunity->owner_id || $user->id === $opportunity->created_by || $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('opportunity.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create') || $user->hasPermissionTo('opportunity.create');
    }

    public function update(User $user, Opportunity $opportunity): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $opportunity->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->id === $opportunity->assigned_to || $user->id === $opportunity->owner_id || $user->id === $opportunity->created_by || $user->hasPermissionTo('crm.update') || $user->hasPermissionTo('opportunity.update');
    }

    public function delete(User $user, Opportunity $opportunity): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $opportunity->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete') || $user->hasPermissionTo('opportunity.delete');
    }

    public function restore(User $user, Opportunity $opportunity): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $opportunity->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Opportunity $opportunity): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $opportunity->company_id && $user->company_id !== $opportunity->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
