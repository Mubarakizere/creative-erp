<?php

namespace App\Policies;

use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('role.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('role.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('role.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        // Example logic: Super Admin role shouldn't be edited by someone who isn't a super admin
        if ($role->name === 'Super Admin' && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->can('role.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Protect critical roles
        if (in_array($role->name, ['Super Admin', 'Company Admin'])) {
            return false;
        }

        return $user->can('role.delete');
    }
}
