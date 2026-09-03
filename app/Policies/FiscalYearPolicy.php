<?php

namespace App\Policies;

use App\Models\FiscalYear;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FiscalYearPolicy
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

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('fiscal.manage') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, FiscalYear $year): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $year->company_id && $user->company_id !== $year->company_id) {
            return false;
        }

        return $user->hasPermissionTo('fiscal.manage') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('fiscal.manage') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, FiscalYear $year): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $year->company_id && $user->company_id !== $year->company_id) {
            return false;
        }

        return $user->hasPermissionTo('fiscal.manage') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, FiscalYear $year): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $year->company_id && $user->company_id !== $year->company_id) {
            return false;
        }

        return $user->hasPermissionTo('fiscal.manage') || $user->hasPermissionTo('finance.delete');
    }
}
