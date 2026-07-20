<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return clone $user->can('quotation.view');
    }

    public function view(User $user, Quotation $quotation)
    {
        return $user->can('quotation.view') && $user->company_id === $quotation->company_id;
    }

    public function create(User $user)
    {
        return clone $user->can('quotation.create');
    }

    public function update(User $user, Quotation $quotation)
    {
        return $user->can('quotation.update') && $user->company_id === $quotation->company_id;
    }

    public function delete(User $user, Quotation $quotation)
    {
        return $user->can('quotation.delete') && $user->company_id === $quotation->company_id;
    }

    public function approve(User $user, Quotation $quotation)
    {
        return $user->can('quotation.approve') && $user->company_id === $quotation->company_id;
    }

    public function export(User $user, Quotation $quotation)
    {
        return $user->can('quotation.export') && $user->company_id === $quotation->company_id;
    }
}
