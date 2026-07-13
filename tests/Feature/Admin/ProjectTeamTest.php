<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProjectTeamTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base models needed for the tests
        $company = Company::factory()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        
        $this->admin = User::factory()->create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'status' => 'active'
        ]);

        // Create permissions and role
        $permissions = [
            'project.viewAny', 'project.view', 'project.create', 'project.update', 'project.delete',
            'project-team.view', 'project-team.create', 'project-team.update', 'project-team.delete',
            'project-team.assign', 'project-team.remove', 'project-team.activate', 'project-team.deactivate'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::create(['name' => 'Super Admin']);
        $role->givePermissionTo(Permission::all());
        $this->admin->assignRole('Super Admin');
    }

    public function test_admin_can_view_project_teams_index()
    {
        $this->actingAs($this->admin);

        ProjectMember::factory()->count(3)->create();

        $response = $this->get(route('admin.projects.team.index'));

        $response->assertStatus(200);
        $response->assertViewHas('members');
    }

    public function test_admin_can_assign_member_to_project()
    {
        $this->actingAs($this->admin);

        $project = Project::factory()->create();
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $data = [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'department_id' => $department->id,
            'project_role' => 'Project Manager',
            'allocation_percentage' => 100,
            'joined_at' => now()->format('Y-m-d'),
            'status' => 'Active',
        ];

        $response = $this->post(route('admin.projects.team.store'), $data);

        $response->assertRedirect(route('admin.projects.show', $project));
        $this->assertDatabaseHas('project_members', [
            'project_id' => $project->id,
            'user_id' => $user->id,
            'project_role' => 'Project Manager'
        ]);
        
        // Assert PM is updated on project
        $project->refresh();
        $this->assertEquals($user->id, $project->project_manager_id);
    }
}
