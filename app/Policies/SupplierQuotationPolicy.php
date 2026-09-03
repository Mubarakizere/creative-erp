<?php

namespace App\Policies;

use App\Models\SupplierQuotation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierQuotationPolicy
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
        return $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('quotation.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupplierQuotation $supplierQuotation): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplierQuotation->company_id && $user->company_id !== $supplierQuotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('quotation.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('procurement.create') || $user->hasPermissionTo('quotation.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupplierQuotation $supplierQuotation): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplierQuotation->company_id && $user->company_id !== $supplierQuotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.update') || $user->hasPermissionTo('quotation.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupplierQuotation $supplierQuotation): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplierQuotation->company_id && $user->company_id !== $supplierQuotation->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.delete') || $user->hasPermissionTo('quotation.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupplierQuotation $supplierQuotation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupplierQuotation $supplierQuotation): bool
    {
        return false;
    }
}
