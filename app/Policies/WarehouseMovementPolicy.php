<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseMovement;
use Illuminate\Auth\Access\Response;

class WarehouseMovementPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage');
    }

    public function view(User $user, WarehouseMovement $warehouseMovement): bool
    {
        return $user->company_id === $warehouseMovement->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage');
    }

    public function update(User $user, WarehouseMovement $warehouseMovement): bool
    {
        return $user->company_id === $warehouseMovement->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function delete(User $user, WarehouseMovement $warehouseMovement): bool
    {
        return $user->company_id === $warehouseMovement->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function restore(User $user, WarehouseMovement $warehouseMovement): bool
    {
        return false;
    }

    public function forceDelete(User $user, WarehouseMovement $warehouseMovement): bool
    {
        return false;
    }
}
