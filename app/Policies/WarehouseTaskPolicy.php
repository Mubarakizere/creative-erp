<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseTask;
use Illuminate\Auth\Access\Response;

class WarehouseTaskPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage');
    }

    public function view(User $user, WarehouseTask $warehouseTask): bool
    {
        return $user->company_id === $warehouseTask->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage');
    }

    public function update(User $user, WarehouseTask $warehouseTask): bool
    {
        return $user->company_id === $warehouseTask->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function delete(User $user, WarehouseTask $warehouseTask): bool
    {
        return $user->company_id === $warehouseTask->company_id && $user->hasPermissionTo('warehouse.manage');
    }

    public function restore(User $user, WarehouseTask $warehouseTask): bool
    {
        return false;
    }

    public function forceDelete(User $user, WarehouseTask $warehouseTask): bool
    {
        return false;
    }
}
