<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
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
        return $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('lead.view');
    }

    public function view(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->id === $lead->assigned_to || $user->id === $lead->created_by || $user->hasPermissionTo('crm.view') || $user->hasPermissionTo('lead.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create') || $user->hasPermissionTo('lead.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->id === $lead->assigned_to || $user->id === $lead->created_by || $user->hasPermissionTo('crm.update') || $user->hasPermissionTo('lead.update');
    }

    public function delete(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete') || $user->hasPermissionTo('lead.delete');
    }

    public function restore(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function convert(User $user, Lead $lead): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $lead->company_id && $user->company_id !== $lead->company_id) {
            return false;
        }

        // Cannot convert already converted leads
        if ($lead->status === 'Converted') {
            return false;
        }

        return $user->hasPermissionTo('crm.convert') || $user->hasPermissionTo('crm.update') || $user->hasPermissionTo('lead.update');
    }
}
