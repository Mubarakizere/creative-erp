<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolePermissionsMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_supervisor_role_has_exact_required_permissions()
    {
        $supervisor = Role::where('name', 'Supervisor')->first();
        $this->assertNotNull($supervisor);

        $expectedPermissions = [
            'project.view',
            'document.view',
            'document.download',
            'project_task.view',
            'project_task.update',
            'milestone.view',
            'time.view',
            'report.view',
        ];

        foreach ($expectedPermissions as $perm) {
            $this->assertTrue($supervisor->hasPermissionTo($perm), "Supervisor should have permission: {$perm}");
        }
    }

    public function test_engineer_and_site_engineer_roles_have_exact_required_permissions()
    {
        $roles = ['Engineer', 'Site Engineer'];

        $expectedPermissions = [
            'calendar.view',
            'comment.create',
            'comment.view',
            'document.view',
            'document.download',
            'document.create',
            'document.upload',
            'goods_receipt.view',
            'material_request.create',
            'material_request.view',
            'material_request.submit',
            'meeting.view',
            'milestone.view',
            'notification.announcement',
            'notification.view',
        ];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $this->assertNotNull($role, "Role {$roleName} should exist");

            foreach ($expectedPermissions as $perm) {
                $this->assertTrue($role->hasPermissionTo($perm), "{$roleName} should have permission: {$perm}");
            }
        }
    }

    public function test_client_role_has_exact_required_permissions()
    {
        $client = Role::where('name', 'Client')->first();
        $this->assertNotNull($client);

        $expectedPermissions = [
            'calendar.view',
            'activity.view',
            'document.view',
            'document.download',
            'milestone.view',
            'notification.view',
            'notification.announcement',
            'project_task.view',
            'meeting.view',
            'report.view',
        ];

        foreach ($expectedPermissions as $perm) {
            $this->assertTrue($client->hasPermissionTo($perm), "Client should have permission: {$perm}");
        }
    }
}
