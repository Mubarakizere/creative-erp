<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Client;
use App\Models\Task;
use App\Models\Milestone;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectAccessVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_view_all_projects()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $superAdmin = User::factory()->create(['company_id' => $company1->id]);
        $superAdmin->assignRole('Super Admin');

        $project1 = Project::factory()->create(['company_id' => $company1->id, 'name' => 'Project Alpha']);
        $project2 = Project::factory()->create(['company_id' => $company2->id, 'name' => 'Project Beta']);

        $response = $this->actingAs($superAdmin)->get(route('admin.projects.index'));
        $response->assertStatus(200);
        $response->assertSee('Project Alpha');
        $response->assertSee('Project Beta');

        $this->assertTrue($superAdmin->can('view', $project1));
        $this->assertTrue($superAdmin->can('view', $project2));
    }

    public function test_ceo_can_view_all_company_projects()
    {
        $company = Company::factory()->create();
        $ceo = User::factory()->create(['company_id' => $company->id]);
        $ceo->assignRole('CEO');

        $project1 = Project::factory()->create(['company_id' => $company->id, 'name' => 'Project Alpha']);
        $project2 = Project::factory()->create(['company_id' => $company->id, 'name' => 'Project Beta']);

        $response = $this->actingAs($ceo)->get(route('admin.projects.index'));
        $response->assertStatus(200);
        $response->assertSee('Project Alpha');
        $response->assertSee('Project Beta');

        $this->assertTrue($ceo->can('view', $project1));
        $this->assertTrue($ceo->can('view', $project2));
    }

    public function test_user_can_only_view_assigned_projects_and_is_forbidden_from_unassigned_projects()
    {
        $company = Company::factory()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        
        $manager = User::factory()->create(['company_id' => $company->id]);
        $supervisor = User::factory()->create(['company_id' => $company->id]);
        $supervisor->assignRole('Supervisor');

        $assignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'project_manager_id' => $manager->id,
            'name' => 'Assigned Tower'
        ]);
        $unassignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'project_manager_id' => $manager->id,
            'name' => 'Unassigned Bridge'
        ]);

        // Assign user to project via ProjectMember
        ProjectMember::create([
            'project_id' => $assignedProject->id,
            'user_id' => $supervisor->id,
            'department_id' => $department->id,
            'project_role' => 'Supervisor',
            'status' => 'Active',
            'joined_at' => now(),
        ]);

        $response = $this->actingAs($supervisor)->get(route('admin.projects.index'));
        $response->assertStatus(200);
        $response->assertSee('Assigned Tower');
        $response->assertDontSee('Unassigned Bridge');

        // Check policy directly
        $this->assertTrue($supervisor->can('view', $assignedProject));
        $this->assertFalse($supervisor->can('view', $unassignedProject));

        // Attempting to view unassigned project directly results in 403 Forbidden
        $showResponse = $this->actingAs($supervisor)->get(route('admin.projects.show', $unassignedProject));
        $showResponse->assertStatus(403);
    }

    public function test_engineer_can_access_assigned_project_tasks_and_not_unassigned()
    {
        $company = Company::factory()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        $manager = User::factory()->create(['company_id' => $company->id]);

        $engineer = User::factory()->create(['company_id' => $company->id]);
        $engineer->assignRole('Engineer');

        $assignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'project_manager_id' => $manager->id,
            'name' => 'Project Assigned'
        ]);
        $unassignedProject = Project::factory()->create([
            'company_id' => $company->id,
            'project_manager_id' => $manager->id,
            'name' => 'Project Unassigned'
        ]);

        ProjectMember::create([
            'project_id' => $assignedProject->id,
            'user_id' => $engineer->id,
            'department_id' => $department->id,
            'project_role' => 'Engineer',
            'status' => 'Active',
            'joined_at' => now(),
        ]);

        $taskAssigned = Task::create([
            'company_id' => $company->id,
            'project_id' => $assignedProject->id,
            'name' => 'Install Foundation',
            'task_code' => 'TSK-001',
            'status' => 'In Progress',
            'start_date' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $taskUnassigned = Task::create([
            'company_id' => $company->id,
            'project_id' => $unassignedProject->id,
            'name' => 'Secret Task',
            'task_code' => 'TSK-002',
            'status' => 'In Progress',
            'start_date' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $this->assertTrue($engineer->can('view', $taskAssigned));
        $this->assertFalse($engineer->can('view', $taskUnassigned));

        $showTaskResponse = $this->actingAs($engineer)->get(route('admin.projects.tasks.show', $taskUnassigned));
        $showTaskResponse->assertStatus(403);
    }

    public function test_client_can_access_project_associated_via_email()
    {
        $company = Company::factory()->create();
        $manager = User::factory()->create(['company_id' => $company->id]);

        $clientUser = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'client@acme.com',
        ]);
        $clientUser->assignRole('Client');

        $clientRecord = Client::factory()->create([
            'company_id' => $company->id,
            'email' => 'client@acme.com',
        ]);

        $clientProject = Project::factory()->create([
            'company_id' => $company->id,
            'client_id' => $clientRecord->id,
            'project_manager_id' => $manager->id,
            'name' => 'Acme Headquarters',
        ]);

        $otherClient = Client::factory()->create([
            'company_id' => $company->id,
            'email' => 'other@corp.com',
        ]);

        $otherProject = Project::factory()->create([
            'company_id' => $company->id,
            'client_id' => $otherClient->id,
            'project_manager_id' => $manager->id,
            'name' => 'Other Corp HQ',
        ]);

        $this->assertTrue($clientUser->can('view', $clientProject));
        $this->assertFalse($clientUser->can('view', $otherProject));

        $response = $this->actingAs($clientUser)->get(route('admin.projects.index'));
        $response->assertStatus(200);
        $response->assertSee('Acme Headquarters');
        $response->assertDontSee('Other Corp HQ');
    }
}
