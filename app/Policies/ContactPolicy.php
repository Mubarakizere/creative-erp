<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Contact $contact): bool
    {
        if ($user->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Contact $contact): bool
    {
        if ($user->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Contact $contact): bool
    {
        if ($user->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete');
    }

    public function restore(User $user, Contact $contact): bool
    {
        if ($user->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        if ($user->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
