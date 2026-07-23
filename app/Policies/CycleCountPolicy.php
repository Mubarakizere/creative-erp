<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseCycleCount;
use Illuminate\Auth\Access\Response;

class CycleCountPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.count');
    }

    public function view(User $user, WarehouseCycleCount $warehouseCycleCount): bool
    {
        return $user->company_id === $warehouseCycleCount->company_id && $user->hasPermissionTo('warehouse.count');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.count');
    }

    public function update(User $user, WarehouseCycleCount $warehouseCycleCount): bool
    {
        return $user->company_id === $warehouseCycleCount->company_id && $user->hasPermissionTo('warehouse.count');
    }

    public function delete(User $user, WarehouseCycleCount $warehouseCycleCount): bool
    {
        return $user->company_id === $warehouseCycleCount->company_id && $user->hasPermissionTo('warehouse.count');
    }

    public function restore(User $user, WarehouseCycleCount $warehouseCycleCount): bool
    {
        return false;
    }

    public function forceDelete(User $user, WarehouseCycleCount $warehouseCycleCount): bool
    {
        return false;
    }
}
