<?php

namespace App\Policies;

use App\Models\AssetDisposal;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetDisposalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.dispose');
    }

    public function view(User $user, AssetDisposal $disposal)
    {
        return $user->hasPermissionTo('asset.dispose') && $user->company_id === $disposal->asset->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.dispose');
    }
    
    public function approve(User $user, AssetDisposal $disposal)
    {
        return $user->hasPermissionTo('asset.dispose') && 
               $user->company_id === $disposal->asset->company_id && 
               $user->id !== $disposal->requested_by;
    }
}
