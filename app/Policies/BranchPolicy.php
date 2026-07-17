<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;

class BranchPolicy
{
    /**
     * Determine whether the user can view any branches.
     *
     * Permission: branch.view
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('branch.view');
    }

    /**
     * Determine whether the user can view the branch.
     *
     * Permission: branch.view
     * Multi-tenant: User must belong to the same company as the branch.
     */
    public function view(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.view')) {
            return false;
        }

        // Company-scoped isolation
        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create branches.
     *
     * Permission: branch.create
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('branch.create');
    }

    /**
     * Determine whether the user can update the branch.
     *
     * Permission: branch.update
     */
    public function update(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.update')) {
            return false;
        }

        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the branch.
     *
     * Permission: branch.delete
     */
    public function delete(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.delete')) {
            return false;
        }

        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the branch.
     *
     * Permission: branch.restore
     */
    public function restore(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.restore')) {
            return false;
        }

        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can activate the branch.
     *
     * Permission: branch.activate
     */
    public function activate(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.activate')) {
            return false;
        }

        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can deactivate the branch.
     *
     * Permission: branch.deactivate
     */
    public function deactivate(User $user, Branch $branch): bool
    {
        if (!$user->hasPermissionTo('branch.deactivate')) {
            return false;
        }

        if ($user->company_id && $branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }
}
