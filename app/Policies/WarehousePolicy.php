<?php

namespace App\Policies;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy
{
    use HandlesAuthorization;

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

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('warehouse.view') || $user->hasPermissionTo('inventory.view');
    }

    public function view(User $user, Warehouse $warehouse)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouse->company_id && $user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.view') || $user->hasPermissionTo('inventory.view');
    }

    public function create(User $user)
    {
        return $user->hasPermissionTo('warehouse.create') || $user->hasPermissionTo('inventory.create');
    }

    public function update(User $user, Warehouse $warehouse)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouse->company_id && $user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.update') || $user->hasPermissionTo('inventory.update');
    }

    public function delete(User $user, Warehouse $warehouse)
    {
        if ($user->hasRole('Super Admin') || $user->hasRole('CEO')) {
            return true;
        }

        if ($user->company_id && $warehouse->company_id && $user->company_id !== $warehouse->company_id) return false;
        return $user->hasPermissionTo('warehouse.delete') || $user->hasPermissionTo('inventory.delete');
    }
}
