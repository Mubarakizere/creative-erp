<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('invoice.view');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('invoice.create');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if (in_array($invoice->status, ['Paid', 'Cancelled', 'Voided'])) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.update');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if (in_array($invoice->status, ['Paid', 'Partially Paid'])) {
            return false;
        }

        if ($user->company_id && $user->company_id !== $invoice->company_id) {
            return false;
        }

        return $user->hasPermissionTo('invoice.delete');
    }
}
