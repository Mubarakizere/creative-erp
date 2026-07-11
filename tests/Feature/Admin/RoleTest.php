<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure roles and permissions are reset
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::create(['name' => 'role.view']);
        Permission::create(['name' => 'role.create']);
        Permission::create(['name' => 'role.update']);
        Permission::create(['name' => 'role.delete']);

        // Create Super Admin
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $this->superAdmin = User::factory()->create(['status' => 'active']);
        $this->superAdmin->assignRole($superAdminRole);

        // Create Regular User
        $this->regularUser = User::factory()->create(['status' => 'active']);
    }

    public function test_super_admin_can_view_roles_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.roles.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.roles.index');
    }

    public function test_regular_user_cannot_view_roles_list(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.roles.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_create_role(): void
    {
        $roleData = [
            'name' => 'Test Role',
            'guard_name' => 'web',
            'permissions' => ['role.view']
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.roles.store'), $roleData);
        
        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Test Role']);
        
        $role = Role::where('name', 'Test Role')->first();
        $this->assertTrue($role->hasPermissionTo('role.view'));
    }

    public function test_super_admin_can_update_role(): void
    {
        $role = Role::create(['name' => 'Old Name']);
        
        $roleData = [
            'name' => 'New Name',
            'guard_name' => 'web',
            'permissions' => ['role.create']
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('admin.roles.update', $role), $roleData);
        
        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'New Name']);
        $this->assertTrue($role->fresh()->hasPermissionTo('role.create'));
    }

    public function test_super_admin_can_delete_role(): void
    {
        $role = Role::create(['name' => 'Role to Delete']);
        
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.roles.destroy', $role));
        
        $response->assertRedirect(route('admin.roles.index'));
        $this->assertDatabaseMissing('roles', ['name' => 'Role to Delete']);
    }

    public function test_super_admin_cannot_delete_super_admin_role(): void
    {
        $role = Role::where('name', 'Super Admin')->first();
        
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.roles.destroy', $role));
        
        $response->assertStatus(403);
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
    }
}
