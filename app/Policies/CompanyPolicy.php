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
        // TODO: Check permission 'company.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can view the company.
     *
     * Permission: company.view
     */
    public function view(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.view' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can create companies.
     *
     * Permission: company.create
     */
    public function create(User $user): bool
    {
        // TODO: Check permission 'company.create' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can update the company.
     *
     * Permission: company.update
     */
    public function update(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.update' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can delete the company.
     *
     * Permission: company.delete
     */
    public function delete(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.delete' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can restore the company.
     *
     * Permission: company.restore
     */
    public function restore(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.restore' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can activate the company.
     *
     * Permission: company.activate
     */
    public function activate(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.activate' when Roles/Permissions module is built
        return true;
    }

    /**
     * Determine whether the user can deactivate the company.
     *
     * Permission: company.deactivate
     */
    public function deactivate(User $user, Company $company): bool
    {
        // TODO: Check permission 'company.deactivate' when Roles/Permissions module is built
        return true;
    }
}
