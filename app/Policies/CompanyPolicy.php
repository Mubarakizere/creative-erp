<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Determine whether the user can view any companies.
     *
     * Permission: company.view
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('company.view');
    }

    /**
     * Determine whether the user can view the company.
     *
     * Permission: company.view
     * Multi-tenant: User must belong to the company or have global access.
     */
    public function view(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.view')) {
            return false;
        }

        // Users can only view their own company unless they are Super Admin (handled by Gate::before)
        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create companies.
     *
     * Permission: company.create
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('company.create');
    }

    /**
     * Determine whether the user can update the company.
     *
     * Permission: company.update
     */
    public function update(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.update')) {
            return false;
        }

        // Company-scoped users can only update their own company
        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the company.
     *
     * Permission: company.delete
     */
    public function delete(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.delete')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can restore the company.
     *
     * Permission: company.restore
     */
    public function restore(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.restore')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can activate the company.
     *
     * Permission: company.activate
     */
    public function activate(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.activate')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can deactivate the company.
     *
     * Permission: company.deactivate
     */
    public function deactivate(User $user, Company $company): bool
    {
        if (!$user->hasPermissionTo('company.deactivate')) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $company->id) {
            return false;
        }

        return true;
    }
}
