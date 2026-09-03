<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
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

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('invoice.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $invoice->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('invoice.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if (in_array($invoice->status, ['Paid', 'Cancelled', 'Voided'])) {
            return false;
        }

        if ($user->company_id && $invoice->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if (in_array($invoice->status, ['Paid', 'Partially Paid'])) {
            return false;
        }

        if ($user->company_id && $invoice->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.delete') || $user->hasPermissionTo('finance.delete');
    }
}
