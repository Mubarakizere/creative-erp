<?php
namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
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

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('purchase_order.view'); }

    public function view(User $user, PurchaseOrder $po)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $po->company_id && $user->company_id !== $po->company_id) {
            return false;
        }

        return $user->id === $po->created_by || $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('purchase_order.view');
    }

    public function create(User $user) { return $user->hasPermissionTo('procurement.create') || $user->hasPermissionTo('purchase_order.create'); }

    public function update(User $user, PurchaseOrder $po)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $po->company_id && $user->company_id !== $po->company_id) {
            return false;
        }

        return $user->id === $po->created_by || $user->hasPermissionTo('procurement.update') || $user->hasPermissionTo('purchase_order.update');
    }

    public function delete(User $user, PurchaseOrder $po)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $po->company_id && $user->company_id !== $po->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.delete') || $user->hasPermissionTo('purchase_order.delete');
    }

    public function approve(User $user, PurchaseOrder $po)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $po->company_id && $user->company_id !== $po->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.approve') || $user->hasPermissionTo('purchase_order.approve');
    }
}