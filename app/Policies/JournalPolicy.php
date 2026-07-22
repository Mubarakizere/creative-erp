<?php

namespace App\Policies;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JournalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('journal.view');
    }

    public function view(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('journal.view') && $user->company_id === $journal->company_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('journal.create');
    }

    public function update(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('journal.update') && $user->company_id === $journal->company_id && in_array($journal->status, ['Draft', 'Pending Approval']);
    }

    public function delete(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('journal.delete') && $user->company_id === $journal->company_id && $journal->status === 'Draft';
    }

    public function post(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('journal.post') && $user->company_id === $journal->company_id;
    }

    public function reverse(User $user, Journal $journal): bool
    {
        return $user->hasPermissionTo('journal.reverse') && $user->company_id === $journal->company_id;
    }
}
