<?php

namespace App\Policies;

use App\Models\AssetTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetTransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.transfer');
    }

    public function view(User $user, AssetTransfer $transfer)
    {
        return $user->hasPermissionTo('asset.transfer') && $user->company_id === $transfer->asset->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.transfer');
    }
    
    public function approve(User $user, AssetTransfer $transfer)
    {
        // Must have permission and cannot approve own request unless super admin
        return $user->hasPermissionTo('asset.transfer') && 
               $user->company_id === $transfer->asset->company_id && 
               $user->id !== $transfer->requested_by;
    }
}
