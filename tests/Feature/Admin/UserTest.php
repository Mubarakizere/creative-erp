<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Company $company;
    protected Branch $branch;
    protected Department $department;
    protected Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base data
        $this->company = Company::create(['name' => 'Test Company', 'email' => 'company@test.com', 'code' => 'TC', 'status' => 'active']);
        $this->branch = Branch::create(['company_id' => $this->company->id, 'email' => 'branch@test.com', 'name' => 'Main Branch', 'code' => 'MB', 'status' => 'active']);
        $this->department = Department::create(['branch_id' => $this->branch->id, 'company_id' => $this->company->id, 'name' => 'IT', 'code' => 'IT', 'status' => 'active']);

        // Setup roles & permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $this->employeeRole = Role::firstOrCreate(['name' => 'Employee']);

        $permissions = [
            'user.view', 'user.create', 'user.update', 'user.delete',
            'user.restore', 'user.activate', 'user.deactivate', 'user.reset-password'
        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
            $superAdminRole->givePermissionTo($perm);
        }

        // Setup admin user
        $this->admin = User::factory()->create([
            'status' => 'active'
        ]);
        $this->admin->assignRole('Super Admin');
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Users');
    }

    public function test_admin_can_create_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.users.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'roles' => ['Employee'],
            'status' => 'active'
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('Employee'));
    }

    public function test_admin_can_update_user(): void
    {
        $user = User::factory()->create([
            'company_id' => $this->company->id,
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'status' => 'active'
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), [
            'first_name' => 'Jane Updated',
            'last_name' => 'Smith',
            'email' => 'jane.updated@example.com',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'department_id' => $this->department->id,
            'roles' => ['Employee'],
            'status' => 'inactive'
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Jane Updated',
            'email' => 'jane.updated@example.com',
            'status' => 'inactive'
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $user));
        $response->assertRedirect();

        $this->assertSoftDeleted($user);
    }

    public function test_admin_can_reset_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password')
        ]);
        $oldPassword = $user->password;

        $response = $this->actingAs($this->admin)->post(route('admin.users.reset-password', $user));
        $response->assertRedirect();

        $user->refresh();
        $this->assertNotEquals($oldPassword, $user->password);
    }
}
