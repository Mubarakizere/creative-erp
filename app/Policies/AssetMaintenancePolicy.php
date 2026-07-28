<?php

namespace App\Policies;

use App\Models\AssetMaintenance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetMaintenancePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.maintenance');
    }

    public function view(User $user, AssetMaintenance $maintenance)
    {
        return $user->hasPermissionTo('asset.maintenance') && $user->company_id === $maintenance->asset->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.maintenance');
    }
}
