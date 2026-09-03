<?php

namespace App\Policies;

use App\Models\WebsiteSetting;
use App\Models\User;

class WebsiteSettingPolicy
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
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('settings.view') || $user->hasPermissionTo('website.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ?WebsiteSetting $websiteSetting = null): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('settings.view') || $user->hasPermissionTo('website.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('settings.update') || $user->hasPermissionTo('website.update');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ?WebsiteSetting $websiteSetting = null): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('settings.update') || $user->hasPermissionTo('website.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ?WebsiteSetting $websiteSetting = null): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('settings.update') || $user->hasPermissionTo('website.delete');
    }
}
