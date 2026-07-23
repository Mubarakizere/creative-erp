<?php

namespace App\Policies;

use App\Models\SupplierQuotation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupplierQuotationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('quotation.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupplierQuotation $supplierQuotation): bool
    {
        return $user->hasPermissionTo('quotation.view') && $user->company_id === $supplierQuotation->company_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('quotation.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupplierQuotation $supplierQuotation): bool
    {
        return $user->hasPermissionTo('quotation.update') && $user->company_id === $supplierQuotation->company_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupplierQuotation $supplierQuotation): bool
    {
        return $user->hasPermissionTo('quotation.delete') && $user->company_id === $supplierQuotation->company_id;
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
