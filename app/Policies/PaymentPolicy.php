<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
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
        return $user->hasPermissionTo('payment.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $payment->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('payment.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $payment->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.update') || $user->hasPermissionTo('finance.update');
    }

    public function delete(User $user, Payment $payment): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $payment->company_id && $user->company_id !== $payment->company_id) {
            return false;
        }

        return $user->hasPermissionTo('payment.delete') || $user->hasPermissionTo('finance.delete');
    }
}
