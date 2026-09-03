<?php

namespace App\Policies;

use App\Models\Pipeline;
use App\Models\User;

class PipelinePolicy
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
        return $user->hasPermissionTo('crm.pipeline') || $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Pipeline $pipeline): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pipeline->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.pipeline') || $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.manage') || $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Pipeline $pipeline): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pipeline->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage') || $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Pipeline $pipeline): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pipeline->company_id && $user->company_id !== $pipeline->company_id) {
            return false;
        }
        
        if ($pipeline->is_default) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage') || $user->hasPermissionTo('crm.delete');
    }
}
