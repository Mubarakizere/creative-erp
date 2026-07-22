<?php

namespace App\Policies;

use App\Models\GeneralLedger;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GeneralLedgerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('ledger.view');
    }

    public function view(User $user, GeneralLedger $ledger): bool
    {
        return $user->hasPermissionTo('ledger.view') && (int) $user->company_id === (int) $ledger->company_id;
    }
}
