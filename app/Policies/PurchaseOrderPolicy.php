<?php
namespace App\Policies;

use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('purchase_order.view'); }
    public function view(User $user, PurchaseOrder $po) { return $user->hasPermissionTo('purchase_order.view') && $user->company_id === $po->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('purchase_order.create'); }
    public function approve(User $user, PurchaseOrder $po) { return $user->hasPermissionTo('purchase_order.approve') && $user->company_id === $po->company_id; }
}