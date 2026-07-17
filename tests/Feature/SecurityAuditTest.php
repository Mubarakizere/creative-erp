<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_access_pending_for_users_without_role()
    {
        $user = clone User::factory()->create(['status' => 'active']);
        // No role assigned

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect(route('admin.access-pending'));
    }

    public function test_super_admin_has_global_access()
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('Super Admin');

        // Testing access to a random endpoint
        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_company_admin_is_scoped_to_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $admin = User::factory()->create([
            'status' => 'active',
            'company_id' => $company1->id
        ]);
        $admin->assignRole('Company Admin');

        // Can view own company
        $this->assertTrue($admin->can('view', $company1));
        
        // Cannot view other company
        $this->assertFalse($admin->can('view', $company2));
    }

    public function test_employee_has_default_permissions()
    {
        $employee = User::factory()->create(['status' => 'active']);
        $employee->assignRole('Employee');

        $this->assertTrue($employee->can('calendar.view'));
        $this->assertTrue($employee->can('meeting.view'));
        
        // Cannot view administrative modules
        $this->assertFalse($employee->can('role.view'));
        $this->assertFalse($employee->can('company.create'));
    }
}
