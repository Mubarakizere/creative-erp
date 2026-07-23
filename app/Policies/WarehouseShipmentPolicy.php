<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseShipment;
use Illuminate\Auth\Access\Response;

class WarehouseShipmentPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.ship');
    }

    public function view(User $user, WarehouseShipment $warehouseShipment): bool
    {
        return $user->company_id === $warehouseShipment->company_id && $user->hasPermissionTo('warehouse.ship');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.ship');
    }

    public function update(User $user, WarehouseShipment $warehouseShipment): bool
    {
        return $user->company_id === $warehouseShipment->company_id && $user->hasPermissionTo('warehouse.ship');
    }

    public function delete(User $user, WarehouseShipment $warehouseShipment): bool
    {
        return $user->company_id === $warehouseShipment->company_id && $user->hasPermissionTo('warehouse.ship');
    }

    public function restore(User $user, WarehouseShipment $warehouseShipment): bool
    {
        return false;
    }

    public function forceDelete(User $user, WarehouseShipment $warehouseShipment): bool
    {
        return false;
    }
}
