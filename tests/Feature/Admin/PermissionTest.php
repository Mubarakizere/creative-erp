<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::firstOrCreate(['name' => 'permission.view']);
        Permission::firstOrCreate(['name' => 'permission.create']);
        Permission::firstOrCreate(['name' => 'permission.update']);
        Permission::firstOrCreate(['name' => 'permission.delete']);

        // Create Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create(['status' => 'active']);
        $this->superAdmin->assignRole($superAdminRole);

        // Create Regular User
        $this->regularUser = User::factory()->create(['status' => 'active']);
    }

    public function test_super_admin_can_view_permissions_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.permissions.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.permissions.index');
    }

    public function test_regular_user_cannot_view_permissions_list(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.permissions.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_permission(): void
    {
        $permissionData = [
            'name' => 'test.permission',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.permissions.store'), $permissionData);
        
        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'test.permission']);
    }

    public function test_super_admin_can_update_permission(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'old.permission']);
        
        $permissionData = [
            'name' => 'new.permission',
            'guard_name' => 'web'
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('admin.permissions.update', $permission), $permissionData);
        
        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseHas('permissions', ['name' => 'new.permission']);
    }

    public function test_super_admin_can_delete_permission(): void
    {
        $permission = Permission::firstOrCreate(['name' => 'delete.me']);
        
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.permissions.destroy', $permission));
        
        $response->assertRedirect(route('admin.permissions.index'));
        $this->assertDatabaseMissing('permissions', ['name' => 'delete.me']);
    }
}
