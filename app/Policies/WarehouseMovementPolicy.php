<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseMovement;
use Illuminate\Auth\Access\Response;

class WarehouseMovementPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('warehouse.view') || $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, WarehouseMovement $warehouseMovement): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseMovement->company_id && $user->company_id !== $warehouseMovement->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('warehouse.view') || $user->hasPermissionTo('inventory.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('warehouse.create') || $user->hasPermissionTo('inventory.create');
    }

    public function update(User $user, WarehouseMovement $warehouseMovement): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseMovement->company_id && $user->company_id !== $warehouseMovement->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('warehouse.update') || $user->hasPermissionTo('inventory.update');
    }

    public function delete(User $user, WarehouseMovement $warehouseMovement): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseMovement->company_id && $user->company_id !== $warehouseMovement->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('warehouse.delete') || $user->hasPermissionTo('inventory.delete');
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
