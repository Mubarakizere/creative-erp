<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Permissions
        $permissions = [
            // Company
            'company.view', 'company.create', 'company.update', 'company.delete',
            
            // Branch
            'branch.view', 'branch.create', 'branch.update', 'branch.delete',
            
            // Department
            'department.view', 'department.create', 'department.update', 'department.delete',
            
            // Role
            'role.view', 'role.create', 'role.update', 'role.delete',
            
            // Permission
            'permission.view', 'permission.create', 'permission.update', 'permission.delete',
            
            // User (Future)
            'user.view', 'user.create', 'user.update', 'user.delete',
            'user.restore', 'user.activate', 'user.deactivate', 'user.reset-password',
            
            // Client
            'client.view', 'client.create', 'client.update', 'client.delete',
            'client.restore', 'client.activate', 'client.deactivate', 'client.export', 'client.import',
            
            // Project
            'project.view', 'project.create', 'project.update', 'project.delete',
            'project.restore', 'project.archive', 'project.close', 'project.reopen',
            'project.export', 'project.import', 'project.assign-manager', 'project.change-status',
            'project.view-budget', 'project.edit-budget',
            
            // Inventory (Future)
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
            
            // Report (Future)
            'report.view', 'report.create', 'report.update', 'report.delete',
            
            // Settings
            'settings.manage',
        ];

        // 2. Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Define Default Roles
        $roles = [
            'Super Admin',
            'Company Admin',
            'Project Manager',
            'HR Manager',
            'Finance Manager',
            'Procurement Officer',
            'Warehouse Manager',
            'Engineer',
            'Employee',
            'Client',
        ];

        // 4. Create Roles and Assign Permissions
        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Super Admin gets all permissions
            if ($roleName === 'Super Admin') {
                $role->syncPermissions(Permission::all());
            }
        }
    }
}
