<?php

namespace App\Policies;

use App\Models\AssetCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('asset.manage');
    }

    public function view(User $user, AssetCategory $category)
    {
        return $user->hasPermissionTo('asset.manage') && $user->company_id === $category->company_id;
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('asset.manage');
    }

    public function update(User $user, AssetCategory $category)
    {
        return $user->hasPermissionTo('asset.manage') && $user->company_id === $category->company_id;
    }

    public function delete(User $user, AssetCategory $category)
    {
        return $user->hasPermissionTo('asset.manage') && $user->company_id === $category->company_id;
    }
}
