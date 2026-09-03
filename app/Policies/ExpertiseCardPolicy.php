<?php

namespace App\Policies;

use App\Models\ExpertiseCard;
use App\Models\User;

class ExpertiseCardPolicy
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
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('website.view') || $user->hasPermissionTo('expertise.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExpertiseCard $expertiseCard): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('website.view') || $user->hasPermissionTo('expertise.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('website.create') || $user->hasPermissionTo('expertise.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExpertiseCard $expertiseCard): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('website.update') || $user->hasPermissionTo('expertise.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExpertiseCard $expertiseCard): bool
    {
        return $user->hasPermissionTo('website.manage') || $user->hasPermissionTo('website.delete') || $user->hasPermissionTo('expertise.delete');
    }
}
