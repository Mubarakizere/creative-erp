<?php
namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
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

    public function viewAny(User $user) { return $user->hasPermissionTo('supplier.view') || $user->hasPermissionTo('procurement.view'); }

    public function view(User $user, Supplier $supplier)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplier->company_id && $user->company_id !== $supplier->company_id) {
            return false;
        }

        return $user->hasPermissionTo('supplier.view') || $user->hasPermissionTo('procurement.view');
    }

    public function create(User $user) { return $user->hasPermissionTo('supplier.create') || $user->hasPermissionTo('procurement.create'); }

    public function update(User $user, Supplier $supplier)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplier->company_id && $user->company_id !== $supplier->company_id) {
            return false;
        }

        return $user->hasPermissionTo('supplier.update') || $user->hasPermissionTo('procurement.update');
    }

    public function delete(User $user, Supplier $supplier)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $supplier->company_id && $user->company_id !== $supplier->company_id) {
            return false;
        }

        return $user->hasPermissionTo('supplier.delete') || $user->hasPermissionTo('procurement.delete');
    }
}