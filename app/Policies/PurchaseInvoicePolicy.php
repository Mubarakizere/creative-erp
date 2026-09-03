<?php
namespace App\Policies;

use App\Models\PurchaseInvoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseInvoicePolicy
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

    public function viewAny(User $user) { return $user->hasPermissionTo('procurement.view'); }

    public function view(User $user, PurchaseInvoice $pi)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pi->company_id && $user->company_id !== $pi->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.view');
    }

    public function create(User $user) { return $user->hasPermissionTo('procurement.create'); }

    public function update(User $user, PurchaseInvoice $pi)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pi->company_id && $user->company_id !== $pi->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.update');
    }

    public function delete(User $user, PurchaseInvoice $pi)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $pi->company_id && $user->company_id !== $pi->company_id) {
            return false;
        }

        return $user->hasPermissionTo('procurement.delete');
    }
}