<?php

namespace App\Policies;

use App\Models\Refund;
use App\Models\User;

class RefundPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('refund.view');
    }

    public function view(User $user, Refund $refund): bool
    {
        if ($user->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('refund.create');
    }

    public function update(User $user, Refund $refund): bool
    {
        if ($user->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.update');
    }

    public function delete(User $user, Refund $refund): bool
    {
        if ($user->company_id && $user->company_id !== $refund->company_id) {
            return false;
        }

        return $user->hasPermissionTo('refund.delete');
    }
}
