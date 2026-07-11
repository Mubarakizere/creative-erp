<?php

namespace App\Services;

use Spatie\Permission\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class RoleService
{
    /**
     * Get paginated roles with search and filters.
     */
    public function getPaginatedRoles(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Role::query()->with('permissions');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['guard_name'])) {
            $query->where('guard_name', $filters['guard_name']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all roles.
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Create a new role.
     */
    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * Update an existing role.
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'name' => $data['name'],
            'guard_name' => $data['guard_name'] ?? 'web',
        ]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }

    /**
     * Delete a role.
     */
    public function deleteRole(Role $role): bool
    {
        return $role->delete();
    }
}
