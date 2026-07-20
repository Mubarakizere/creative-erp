<?php

namespace App\Policies;

use App\Models\PaymentTerm;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentTermPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function view(User $user, PaymentTerm $paymentTerm)
    {
        return $user->can('quotation.manage') && $user->company_id === $paymentTerm->company_id;
    }

    public function create(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function update(User $user, PaymentTerm $paymentTerm)
    {
        return $user->can('quotation.manage') && $user->company_id === $paymentTerm->company_id;
    }

    public function delete(User $user, PaymentTerm $paymentTerm)
    {
        return $user->can('quotation.manage') && $user->company_id === $paymentTerm->company_id;
    }
}
