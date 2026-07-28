<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.view');
    }

    public function view(User $user, Asset $asset)
    {
        return $user->hasPermissionTo('asset.view') && $user->company_id === $asset->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.create');
    }

    public function update(User $user, Asset $asset)
    {
        return $user->hasPermissionTo('asset.update') && $user->company_id === $asset->company_id;
    }

    public function delete(User $user, Asset $asset)
    {
        return $user->hasPermissionTo('asset.delete') && $user->company_id === $asset->company_id;
    }

    public function assign(User $user, Asset $asset)
    {
        return $user->hasPermissionTo('asset.assign') && $user->company_id === $asset->company_id;
    }

    public function depreciate(User $user, Asset $asset)
    {
        return $user->hasPermissionTo('asset.depreciate') && $user->company_id === $asset->company_id;
    }
}
