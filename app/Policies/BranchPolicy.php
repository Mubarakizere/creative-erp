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
        // TODO: Check permission 'branch.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can view the branch.
     *
     * Permission: branch.view
     */
    public function view(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can create branches.
     *
     * Permission: branch.create
     */
    public function create(User $user): bool
    {
        // TODO: Check permission 'branch.create' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can update the branch.
     *
     * Permission: branch.update
     */
    public function update(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.update' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can delete the branch.
     *
     * Permission: branch.delete
     */
    public function delete(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.delete' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can restore the branch.
     *
     * Permission: branch.restore
     */
    public function restore(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.restore' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can activate the branch.
     *
     * Permission: branch.activate
     */
    public function activate(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.activate' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can deactivate the branch.
     *
     * Permission: branch.deactivate
     */
    public function deactivate(User $user, Branch $branch): bool
    {
        // TODO: Check permission 'branch.deactivate' when Roles/Permissions module is built
        return true;
    }
}
