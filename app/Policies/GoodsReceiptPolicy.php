<?php
namespace App\Policies;

use App\Models\GoodsReceipt;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GoodsReceiptPolicy
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

    public function viewAny(User $user) { return $user->hasPermissionTo('goods_receipt.view') || $user->hasPermissionTo('procurement.view'); }

    public function view(User $user, GoodsReceipt $gr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $gr->company_id && $user->company_id !== $gr->company_id) {
            return false;
        }

        return $user->hasPermissionTo('goods_receipt.view') || $user->hasPermissionTo('procurement.view');
    }

    public function create(User $user) { return $user->hasPermissionTo('goods_receipt.create') || $user->hasPermissionTo('procurement.create'); }

    public function update(User $user, GoodsReceipt $gr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $gr->company_id && $user->company_id !== $gr->company_id) {
            return false;
        }

        return $user->hasPermissionTo('goods_receipt.update') || $user->hasPermissionTo('procurement.update');
    }

    public function delete(User $user, GoodsReceipt $gr)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $gr->company_id && $user->company_id !== $gr->company_id) {
            return false;
        }

        return $user->hasPermissionTo('goods_receipt.delete') || $user->hasPermissionTo('procurement.delete');
    }
}