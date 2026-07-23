<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, Inventory $inventory)
    {
        if ($user->company_id !== $inventory->company_id) return false;
        return $user->hasPermissionTo('inventory.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('inventory.create');
    }

    public function update(User $user, Inventory $inventory)
    {
        if ($user->company_id !== $inventory->company_id) return false;
        return $user->hasPermissionTo('inventory.update');
    }

    public function delete(User $user, Inventory $inventory)
    {
        if ($user->company_id !== $inventory->company_id) return false;
        return $user->hasPermissionTo('inventory.delete');
    }
}
