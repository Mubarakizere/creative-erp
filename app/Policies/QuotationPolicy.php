<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationPolicy
{
    use HandlesAuthorization;

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

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('quotation.view') || $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Quotation $quotation)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $quotation->company_id && $user->company_id !== $quotation->company_id) {
            return false;
        }

        return $user->id === $quotation->user_id || $user->id === $quotation->created_by || $user->hasPermissionTo('quotation.view') || $user->hasPermissionTo('crm.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('quotation.create') || $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Quotation $quotation)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $quotation->company_id && $user->company_id !== $quotation->company_id) {
            return false;
        }

        return $user->id === $quotation->user_id || $user->id === $quotation->created_by || $user->hasPermissionTo('quotation.update') || $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Quotation $quotation)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $quotation->company_id && $user->company_id !== $quotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('quotation.delete') || $user->hasPermissionTo('crm.delete');
    }

    public function approve(User $user, Quotation $quotation)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $quotation->company_id && $user->company_id !== $quotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('quotation.approve') || $user->hasPermissionTo('crm.approve');
    }

    public function export(User $user, Quotation $quotation)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $quotation->company_id && $user->company_id !== $quotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('quotation.export') || $user->hasPermissionTo('quotation.view') || $user->hasPermissionTo('crm.view');
    }
}
