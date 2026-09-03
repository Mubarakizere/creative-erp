<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseReturn;
use Illuminate\Auth\Access\Response;

class WarehouseReturnPolicy
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
        return $user->hasPermissionTo('warehouse.return') || $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, WarehouseReturn $warehouseReturn): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseReturn->company_id && $user->company_id !== $warehouseReturn->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.return') || $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('inventory.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.return') || $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('inventory.create');
    }

    public function update(User $user, WarehouseReturn $warehouseReturn): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseReturn->company_id && $user->company_id !== $warehouseReturn->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.return') || $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('inventory.update');
    }

    public function delete(User $user, WarehouseReturn $warehouseReturn): bool
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouseReturn->company_id && $user->company_id !== $warehouseReturn->company_id) {
            return false;
        }

        return $user->hasPermissionTo('warehouse.return') || $user->hasPermissionTo('warehouse.manage') || $user->hasPermissionTo('inventory.delete');
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
