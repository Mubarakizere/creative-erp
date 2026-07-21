<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('payment.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('payment.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.update');
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.delete');
    }
}
