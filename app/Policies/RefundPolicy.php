<?php

namespace App\Policies;

use App\Models\Refund;
use App\Models\User;

class RefundPolicy
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
        return $user->hasPermissionTo('refund.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, Refund $refund): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $refund->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('refund.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, Refund $refund): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $refund->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, Refund $refund): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $refund->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.delete') || $user->hasPermissionTo('finance.delete');
    }
}
