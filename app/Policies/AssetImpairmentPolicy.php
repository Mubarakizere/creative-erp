<?php

namespace App\Policies;

use App\Models\AssetImpairment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetImpairmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.impair');
    }

    public function view(User $user, AssetImpairment $impairment)
    {
        return $user->hasPermissionTo('asset.impair') && $user->company_id === $impairment->asset->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.impair');
    }
    
    public function approve(User $user, AssetImpairment $impairment)
    {
        return $user->hasPermissionTo('asset.impair') && 
               $user->company_id === $impairment->asset->company_id && 
               $user->id !== $impairment->requested_by;
    }
}
