<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{
    /**
     * Get paginated permissions with search and filters.
     */
    public function getPaginatedPermissions(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Permission::query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['module'])) {
            $query->where('name', 'like', $filters['module'] . '.%');
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all permissions grouped by module.
     */
    public function getAllPermissionsGroupedByModule(): \Illuminate\Support\Collection
    {
        $permissions = Permission::all();

        return $permissions->groupBy(function ($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'general';
        });
    }

    /**
     * Create a new permission.
     */
    public function createPermission(array $data): Permission
    {
        return Permission::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);
    }

    /**
     * Update an existing permission.
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        return $permission;
    }

    /**
     * Delete a permission.
     */
    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }
}
