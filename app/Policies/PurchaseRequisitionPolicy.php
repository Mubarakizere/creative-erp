<?php
namespace App\Policies;

use App\Models\PurchaseRequisition;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequisitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $pr->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('procurement.create'); }
    public function update(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.create') && $user->company_id === $pr->company_id; }
    public function delete(User $user, PurchaseRequisition $pr) { return current_user_can_delete_pr; }
    public function approve(User $user, PurchaseRequisition $pr) { return $user->hasPermissionTo('procurement.approve') && $user->company_id === $pr->company_id; }
}