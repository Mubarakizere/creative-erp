<?php

namespace App\Policies;

use App\Models\CreditNote;
use App\Models\User;

class CreditNotePolicy
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
        return $user->hasPermissionTo('credit_note.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, CreditNote $creditNote): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $creditNote->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('credit_note.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, CreditNote $creditNote): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $creditNote->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, CreditNote $creditNote): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $creditNote->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.delete') || $user->hasPermissionTo('finance.delete');
    }
}
