<?php

namespace App\Policies;

use App\Models\DocumentationCategory;
use App\Models\User;

class DocumentationCategoryPolicy
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
        return $user->hasPermissionTo('documentation.view') || $user->hasPermissionTo('document.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentationCategory $category): bool
    {
        return $user->hasPermissionTo('documentation.view') || $user->hasPermissionTo('document.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('documentation.manage') || $user->hasPermissionTo('documentation.create') || $user->hasPermissionTo('document.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentationCategory $category): bool
    {
        return $user->hasPermissionTo('documentation.manage') || $user->hasPermissionTo('documentation.update') || $user->hasPermissionTo('document.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentationCategory $category): bool
    {
        return $user->hasPermissionTo('documentation.manage') || $user->hasPermissionTo('documentation.delete') || $user->hasPermissionTo('document.delete');
    }
}
