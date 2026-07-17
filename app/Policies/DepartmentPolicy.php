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
        return $user->hasPermissionTo('department.view');
    }

    /**
     * Determine whether the user can view the department.
     *
     * Permission: department.view
     * Multi-tenant: User must belong to the same company.
     */
    public function view(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.view')) {
            return false;
        }

        // Company-scoped isolation via branch
        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create departments.
     *
     * Permission: department.create
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('department.create');
    }

    /**
     * Determine whether the user can update the department.
     *
     * Permission: department.update
     */
    public function update(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.update')) {
            return false;
        }

        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the department.
     *
     * Permission: department.delete
     */
    public function delete(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.delete')) {
            return false;
        }

        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the department.
     *
     * Permission: department.restore
     */
    public function restore(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.restore')) {
            return false;
        }

        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can activate the department.
     *
     * Permission: department.activate
     */
    public function activate(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.activate')) {
            return false;
        }

        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can deactivate the department.
     *
     * Permission: department.deactivate
     */
    public function deactivate(User $user, Department $department): bool
    {
        if (!$user->hasPermissionTo('department.deactivate')) {
            return false;
        }

        if ($user->company_id && $department->branch && $department->branch->company_id !== $user->company_id) {
            return false;
        }

        return true;
    }
}
