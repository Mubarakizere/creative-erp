<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
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

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('settings.view') || $user->hasPermissionTo('settings.manage');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?Setting $setting = null): bool
    {
        return $user->hasPermissionTo('settings.view') || $user->hasPermissionTo('settings.manage');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('settings.update') || $user->hasPermissionTo('settings.manage');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ?Setting $setting = null): bool
    {
        return $user->hasPermissionTo('settings.update') || $user->hasPermissionTo('settings.manage');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ?Setting $setting = null): bool
    {
        return $user->hasPermissionTo('settings.delete') || $user->hasPermissionTo('settings.manage');
    }
}
