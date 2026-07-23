<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseReturn;
use Illuminate\Auth\Access\Response;

class WarehouseReturnPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.return');
    }

    public function view(User $user, WarehouseReturn $warehouseReturn): bool
    {
        return $user->company_id === $warehouseReturn->company_id && $user->hasPermissionTo('warehouse.return');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.return');
    }

    public function update(User $user, WarehouseReturn $warehouseReturn): bool
    {
        return $user->company_id === $warehouseReturn->company_id && $user->hasPermissionTo('warehouse.return');
    }

    public function delete(User $user, WarehouseReturn $warehouseReturn): bool
    {
        return $user->company_id === $warehouseReturn->company_id && $user->hasPermissionTo('warehouse.return');
    }

    public function restore(User $user, WarehouseReturn $warehouseReturn): bool
    {
        return false;
    }

    public function forceDelete(User $user, WarehouseReturn $warehouseReturn): bool
    {
        return false;
    }
}
