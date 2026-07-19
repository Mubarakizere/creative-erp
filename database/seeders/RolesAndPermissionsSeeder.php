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
            'company.restore', 'company.activate', 'company.deactivate',
            
            // Branch
            'branch.view', 'branch.create', 'branch.update', 'branch.delete',
            'branch.restore', 'branch.activate', 'branch.deactivate',
            
            // Department
            'department.view', 'department.create', 'department.update', 'department.delete',
            'department.restore', 'department.activate', 'department.deactivate',
            
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
            
            // Project Teams
            'project-team.view', 'project-team.create', 'project-team.update', 'project-team.delete',
            'project-team.restore', 'project-team.assign', 'project-team.remove', 
            'project-team.activate', 'project-team.deactivate', 'project-team.export', 'project-team.import',
            
            // Tasks
            'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks',
            'restore-tasks', 'assign-tasks',
            
            // Milestones
            'view-milestones', 'create-milestones', 'edit-milestones', 'delete-milestones',
            'restore-milestones',
            
            // Documents
            'document.view', 'document.create', 'document.upload', 'document.update', 'document.delete',
            'document.restore', 'document.download', 'document.replace',
            
            // Document Categories
            'document-category.view', 'document-category.create', 'document-category.update', 'document-category.delete',
            
            // Comments & Discussions
            'comment.view', 'comment.create', 'comment.update', 'comment.delete',
            'comment.pin', 'comment.internal',
            
            // Inventory (Future)
            'inventory.view', 'inventory.create', 'inventory.update', 'inventory.delete',
            
            // Report (Future)
            'report.view', 'report.create', 'report.update', 'report.delete', 'report.export',
            
            'comments.restore',
            'comments.pin',
            'comments.reply',

            // Meetings
            'meeting.view',
            'meeting.create',
            'meeting.update',
            'meeting.delete',
            'meeting.restore',
            'meeting.invite',
            'meeting.cancel',
            
            // Calendar
            'calendar.view',
            
            // Workflows
            'workflow.view', 'workflow.create', 'workflow.update', 'workflow.delete',
            
            // Approvals
            'approval.view', 'approval.approve', 'approval.reject', 'approval.return', 'approval.cancel', 'approval.submit',
            
            // Time Tracking
            'time.view',
            'time.create',
            'time.update',
            'time.delete',
            'time.restore',
            'time.export',
            'time.approve',
            
            // Settings
            'settings.manage',

            // Notifications
            'notification.view', 'notification.manage', 'notification.delete',
            'notification.send', 'notification.announcement', 'notification.system',
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

        // Assign specific permissions to roles
        $companyAdmin = Role::where('name', 'Company Admin')->first();
        if ($companyAdmin) {
            // Give all permissions EXCEPT global creation/deletion of companies, roles, permissions, settings
            $allPermissions = Permission::all()->pluck('name')->toArray();
            $excludedPermissions = [
                'company.create', 'company.delete', 'company.restore', 
                'role.create', 'role.delete', 
                'permission.create', 'permission.update', 'permission.delete',
                'settings.manage'
            ];
            
            $companyAdminPermissions = array_diff($allPermissions, $excludedPermissions);
            $companyAdmin->syncPermissions($companyAdminPermissions);
        }

        $projectManager = Role::where('name', 'Project Manager')->first();
        if ($projectManager) {
            $projectManager->syncPermissions([
                'project.view', 'project.create', 'project.update',
                'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks',
                'view-milestones', 'create-milestones', 'edit-milestones',
                'document.view', 'document.create', 'document.update',
                'comment.view', 'comment.create', 'comment.update', 'comments.reply',
                'meeting.view', 'meeting.create', 'meeting.update', 'meeting.invite', 'meeting.cancel',
                'calendar.view',
                'time.view', 'time.create', 'time.update', 'time.delete', 'time.export', 'time.approve',
                'approval.view', 'approval.submit', 'approval.approve', 'approval.reject', 'approval.return', 'approval.cancel',
                'notification.view', 'notification.manage', 'notification.delete', 'notification.send'
            ]);
        }

        $employee = Role::where('name', 'Employee')->first();
        if ($employee) {
            $employee->syncPermissions([
                'project.view',
                'view-tasks', 'edit-tasks',
                'document.view', 'document.create',
                'comment.view', 'comment.create', 'comments.reply',
                'meeting.view', 'meeting.create', 'meeting.invite',
                'calendar.view',
                'time.view', 'time.create', 'time.update',
                'approval.view', 'approval.submit',
                'notification.view', 'notification.manage'
            ]);
        }
    }
}
