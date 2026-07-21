<?php

namespace App\Policies;

use App\Models\CreditNote;
use App\Models\User;

class CreditNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('credit_note.view');
    }

    public function view(User $user, CreditNote $creditNote): bool
    {
        if ($user->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('credit_note.create');
    }

    public function update(User $user, CreditNote $creditNote): bool
    {
        if ($user->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.update');
    }

    public function delete(User $user, CreditNote $creditNote): bool
    {
        if ($user->company_id && $user->company_id !== $creditNote->company_id) {
            return false;
        }

        return $user->hasPermissionTo('credit_note.delete');
    }
}
