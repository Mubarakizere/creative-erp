<?php

namespace App\Policies;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('warehouse.view');
    }

    public function view(User $user, Warehouse $warehouse)
    {
        if ($user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('warehouse.create');
    }

    public function update(User $user, Warehouse $warehouse)
    {
        if ($user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.update');
    }

    public function delete(User $user, Warehouse $warehouse)
    {
        if ($user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.delete');
    }
}
