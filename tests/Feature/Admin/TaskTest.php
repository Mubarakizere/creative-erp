<?php

namespace Tests\Feature\Admin;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\ProjectMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Project $project;
    protected User $projectManager;
    protected User $teamMember;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Permissions
        $permissions = ['view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks', 'restore-tasks', 'assign-tasks'];
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'System Administrator']);
        $adminRole->givePermissionTo($permissions);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Project Manager']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Employee']);

        // Create Company
        $company = \App\Models\Company::factory()->create();

        // Create Admin
        $this->admin = User::factory()->create(['status' => 'active', 'company_id' => $company->id]);
        $this->admin->assignRole('System Administrator');
        
        // Create other users
        $this->projectManager = User::factory()->create(['status' => 'active', 'company_id' => $company->id]);
        $this->projectManager->assignRole('Project Manager');
        
        $this->teamMember = User::factory()->create(['status' => 'active', 'company_id' => $company->id]);
        $this->teamMember->assignRole('Employee');

        // Create Project
        $this->project = Project::factory()->create([
            'company_id' => $company->id,
            'project_manager_id' => $this->projectManager->id,
            'status' => 'In Progress'
        ]);

        // Add team members
        ProjectMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->projectManager->id,
            'project_role' => 'Project Manager',
            'status' => 'Active'
        ]);

        ProjectMember::factory()->create([
            'project_id' => $this->project->id,
            'user_id' => $this->teamMember->id,
            'project_role' => 'Developer',
            'status' => 'Active'
        ]);
    }

    public function test_admin_can_view_tasks_index(): void
    {
        Task::factory()->count(3)->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.tasks.index');
        $response->assertViewHas('tasks');
    }

    public function test_admin_can_view_task_creation_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.projects.tasks.create', ['project_id' => $this->project->id]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.tasks.create');
    }

    public function test_admin_can_create_task(): void
    {
        $taskData = [
            'project_id' => $this->project->id,
            'task_code' => 'TSK-001',
            'name' => 'Initial Setup',
            'description' => 'Setup project repository',
            'assigned_to' => $this->teamMember->id,
            'priority' => 'High',
            'status' => 'Pending',
            'progress' => 0,
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(5)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.projects.tasks.store'), $taskData);

        $response->assertRedirect(route('admin.projects.tasks.index', ['project_id' => $this->project->id]));
        $this->assertDatabaseHas('tasks', [
            'project_id' => $this->project->id,
            'task_code' => 'TSK-001',
            'name' => 'Initial Setup',
            'assigned_to' => $this->teamMember->id,
        ]);
    }

    public function test_admin_can_view_task_edit_form(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.projects.tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.tasks.edit');
        $response->assertViewHas('task');
    }

    public function test_admin_can_update_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id,
            'status' => 'Pending',
            'progress' => 0
        ]);

        $updateData = [
            'project_id' => $this->project->id,
            'task_code' => $task->task_code,
            'name' => 'Updated Task Name',
            'assigned_to' => $this->teamMember->id,
            'priority' => 'Critical',
            'status' => 'In Progress',
            'progress' => 50,
            'start_date' => $task->start_date->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.projects.tasks.update', $task), $updateData);

        $response->assertRedirect(route('admin.projects.tasks.show', $task));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Updated Task Name',
            'status' => 'In Progress',
            'progress' => 50,
        ]);
    }

    public function test_admin_can_soft_delete_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.projects.tasks.destroy', $task));

        $response->assertRedirect(route('admin.projects.tasks.index'));
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_admin_can_restore_task(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id
        ]);
        $task->delete();

        $response = $this->actingAs($this->admin)->patch(route('admin.projects.tasks.restore', $task->id));

        $response->assertRedirect();
        $this->assertNotSoftDeleted('tasks', ['id' => $task->id]);
    }

    public function test_task_status_completed_sets_progress_to_100(): void
    {
        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id,
            'status' => 'In Progress',
            'progress' => 50
        ]);

        $updateData = [
            'project_id' => $this->project->id,
            'task_code' => $task->task_code,
            'name' => $task->name,
            'priority' => $task->priority,
            'status' => 'Completed',
            'progress' => 50, // Should be overridden by service
            'start_date' => $task->start_date->format('Y-m-d'),
        ];

        $this->actingAs($this->admin)->put(route('admin.projects.tasks.update', $task), $updateData);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'Completed',
            'progress' => 100,
        ]);
        
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_circular_dependency_is_prevented(): void
    {
        $taskA = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id
        ]);
        $taskB = Task::factory()->create([
            'project_id' => $this->project->id,
            'company_id' => $this->project->company_id,
            'parent_id' => $taskA->id
        ]);

        $updateData = [
            'project_id' => $this->project->id,
            'task_code' => $taskA->task_code,
            'name' => $taskA->name,
            'parent_id' => $taskB->id, // A depends on B, which depends on A
            'priority' => $taskA->priority,
            'status' => $taskA->status,
            'progress' => $taskA->progress,
            'start_date' => $taskA->start_date->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.projects.tasks.update', $taskA), $updateData);

        $response->assertSessionHas('error');
    }
}
