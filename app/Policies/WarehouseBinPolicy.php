<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WarehouseBin;
use Illuminate\Auth\Access\Response;

class WarehouseBinPolicy
{
    use \Illuminate\Auth\Access\HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.bin');
    }

    public function view(User $user, WarehouseBin $warehouseBin): bool
    {
        return $user->company_id === $warehouseBin->company_id && $user->hasPermissionTo('warehouse.bin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('warehouse.bin');
    }

    public function update(User $user, WarehouseBin $warehouseBin): bool
    {
        return $user->company_id === $warehouseBin->company_id && $user->hasPermissionTo('warehouse.bin');
    }

    public function delete(User $user, WarehouseBin $warehouseBin): bool
    {
        return $user->company_id === $warehouseBin->company_id && $user->hasPermissionTo('warehouse.bin');
    }

    public function restore(User $user, WarehouseBin $warehouseBin): bool
    {
        return $user->company_id === $warehouseBin->company_id && $user->hasPermissionTo('warehouse.bin');
    }

    public function forceDelete(User $user, WarehouseBin $warehouseBin): bool
    {
        return false;
    }
}
