<?php

namespace App\Policies;

use App\Models\QuotationTemplate;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationTemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function view(User $user, QuotationTemplate $quotationTemplate)
    {
        return $user->can('quotation.manage') && $user->company_id === $quotationTemplate->company_id;
    }

    public function create(User $user)
    {
        return clone $user->can('quotation.manage');
    }

    public function update(User $user, QuotationTemplate $quotationTemplate)
    {
        return $user->can('quotation.manage') && $user->company_id === $quotationTemplate->company_id;
    }

    public function delete(User $user, QuotationTemplate $quotationTemplate)
    {
        return $user->can('quotation.manage') && $user->company_id === $quotationTemplate->company_id;
    }
}
