<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BudgetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('budget.view');
    }

    public function view(User $user, Budget $budget): bool
    {
        return $user->can('budget.view') && $user->company_id === $budget->company_id;
    }

    public function create(User $user): bool
    {
        return $user->can('budget.manage');
    }

    public function update(User $user, Budget $budget): bool
    {
        return $user->can('budget.manage') && $user->company_id === $budget->company_id;
    }

    public function delete(User $user, Budget $budget): bool
    {
        return $user->can('budget.manage') && $user->company_id === $budget->company_id;
    }

    public function restore(User $user, Budget $budget): bool
    {
        return $user->can('budget.manage') && $user->company_id === $budget->company_id;
    }

    public function forceDelete(User $user, Budget $budget): bool
    {
        return false; // Typically don't allow force delete for financial records
    }
}
