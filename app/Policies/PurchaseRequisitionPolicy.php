<?php
namespace App\Policies;

use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequisitionPolicy
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

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('purchase_requisition.view'); }

    public function view(User $user, PurchaseRequisition $pr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pr->company_id && $user->company_id !== $pr->company_id) {
            return false;
        }

        return $user->id === $pr->requested_by || $user->hasPermissionTo('procurement.view') || $user->hasPermissionTo('purchase_requisition.view');
    }

    public function create(User $user) { return $user->hasPermissionTo('procurement.create') || $user->hasPermissionTo('purchase_requisition.create'); }

    public function update(User $user, PurchaseRequisition $pr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pr->company_id && $user->company_id !== $pr->company_id) {
            return false;
        }

        return $user->id === $pr->requested_by || $user->hasPermissionTo('procurement.update') || $user->hasPermissionTo('purchase_requisition.update');
    }

    public function delete(User $user, PurchaseRequisition $pr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pr->company_id && $user->company_id !== $pr->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.delete') || $user->hasPermissionTo('purchase_requisition.delete');
    }

    public function approve(User $user, PurchaseRequisition $pr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pr->company_id && $user->company_id !== $pr->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.approve') || $user->hasPermissionTo('purchase_requisition.approve');
    }
}