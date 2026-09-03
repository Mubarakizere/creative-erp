<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;

class ContactPolicy
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
        return $user->hasPermissionTo('crm.view');
    }

    public function view(User $user, Contact $contact): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $contact->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->id === $contact->owner_id || $user->id === $contact->created_by || $user->hasPermissionTo('crm.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('crm.create');
    }

    public function update(User $user, Contact $contact): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $contact->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->id === $contact->owner_id || $user->id === $contact->created_by || $user->hasPermissionTo('crm.update');
    }

    public function delete(User $user, Contact $contact): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $contact->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.delete');
    }

    public function restore(User $user, Contact $contact): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $contact->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }

    public function forceDelete(User $user, Contact $contact): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $contact->company_id && $user->company_id !== $contact->company_id) {
            return false;
        }

        return $user->hasPermissionTo('crm.manage');
    }
}
