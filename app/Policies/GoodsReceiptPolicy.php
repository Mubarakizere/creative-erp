<?php
namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, GoodsReceipt $gr) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $gr->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('goods_receipt.create'); }
}