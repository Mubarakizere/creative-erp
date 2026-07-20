<?php

namespace App\Policies;

use App\Models\Tax;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function view(User $user, Tax $tax)
    {
        return $user->can('quotation.manage') && $user->company_id === $tax->company_id;
    }

    public function create(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function update(User $user, Tax $tax)
    {
        return $user->can('quotation.manage') && $user->company_id === $tax->company_id;
    }

    public function delete(User $user, Tax $tax)
    {
        return $user->can('quotation.manage') && $user->company_id === $tax->company_id;
    }
}
