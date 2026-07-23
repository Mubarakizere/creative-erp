<?php
namespace App\Policies;

use App\Models\SupplierPayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPaymentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('supplier_payment.view'); }
    public function view(User $user, SupplierPayment $sp) { return $user->hasPermissionTo('supplier_payment.view') && $user->company_id === $sp->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('supplier_payment.create'); }
}