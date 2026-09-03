<?php

namespace App\Policies;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JournalPolicy
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
        return $user->hasPermissionTo('journal.view') || $user->hasPermissionTo('finance.view');
    }

    public function view(User $user, Journal $journal): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $journal->company_id && $user->company_id !== $journal->company_id) {
            return false;
        }

        return $user->hasPermissionTo('journal.view') || $user->hasPermissionTo('finance.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('journal.create') || $user->hasPermissionTo('finance.create');
    }

    public function update(User $user, Journal $journal): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $journal->company_id && $user->company_id !== $journal->company_id) {
            return false;
        }

        return in_array($journal->status, ['Draft', 'Pending Approval']) && ($user->hasPermissionTo('journal.update') || $user->hasPermissionTo('finance.update'));
    }

    public function delete(User $user, Journal $journal): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $journal->company_id && $user->company_id !== $journal->company_id) {
            return false;
        }

        return $journal->status === 'Draft' && ($user->hasPermissionTo('journal.delete') || $user->hasPermissionTo('finance.delete'));
    }

    public function post(User $user, Journal $journal): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $journal->company_id && $user->company_id !== $journal->company_id) {
            return false;
        }

        return $user->hasPermissionTo('journal.post') || $user->hasPermissionTo('finance.update');
    }

    public function reverse(User $user, Journal $journal): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $journal->company_id && $user->company_id !== $journal->company_id) {
            return false;
        }

        return $user->hasPermissionTo('journal.reverse') || $user->hasPermissionTo('finance.update');
    }
}
