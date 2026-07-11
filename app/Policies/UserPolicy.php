<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('user.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('user.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Only super admin can delete a super admin
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('user.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('user.restore');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can activate the model.
     */
    public function activate(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('user.activate');
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return false;
        }

        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('user.deactivate');
    }

    /**
     * Determine whether the user can reset the password for the model.
     */
    public function resetPassword(User $user, User $model): bool
    {
        if ($model->hasRole('Super Admin') && !$user->hasRole('Super Admin')) {
            return false;
        }

        return $user->hasPermissionTo('user.reset-password');
    }
}
