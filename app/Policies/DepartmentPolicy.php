<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    /**
     * Determine whether the user can view any departments.
     *
     * Permission: department.view
     */
    public function viewAny(User $user): bool
    {
        // TODO: Check permission 'department.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can view the department.
     *
     * Permission: department.view
     */
    public function view(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can create departments.
     *
     * Permission: department.create
     */
    public function create(User $user): bool
    {
        // TODO: Check permission 'department.create' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can update the department.
     *
     * Permission: department.update
     */
    public function update(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.update' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can delete the department.
     *
     * Permission: department.delete
     */
    public function delete(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.delete' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can restore the department.
     *
     * Permission: department.restore
     */
    public function restore(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.restore' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can activate the department.
     *
     * Permission: department.activate
     */
    public function activate(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.activate' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can deactivate the department.
     *
     * Permission: department.deactivate
     */
    public function deactivate(User $user, Department $department): bool
    {
        // TODO: Check permission 'department.deactivate' when Roles/Permissions module is built
        return true;
    }
}
