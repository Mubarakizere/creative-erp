<?php
namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseInvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }
    public function view(User $user, PurchaseInvoice $pi) { return $user->hasPermissionTo('procurement.view') && $user->company_id === $pi->company_id; }
    public function create(User $user) { return $user->hasPermissionTo('procurement.create'); }
}