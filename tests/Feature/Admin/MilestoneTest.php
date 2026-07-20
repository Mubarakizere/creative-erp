<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Project;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MilestoneTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Company $company;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->company = Company::factory()->create();
        
        $this->admin->company_id = $this->company->id;
        $this->admin->save();

        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        $this->admin->assignRole($role);

        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    public function test_admin_can_view_milestones_index()
    {
        Milestone::factory(3)->create(['company_id' => $this->company->id, 'project_id' => $this->project->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.milestones.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.milestones.index');
        $response->assertViewHas('milestones');
    }

    public function test_admin_can_create_milestone()
    {
        $data = [
            'project_id' => $this->project->id,
            'name' => 'Phase 1 MVP',
            'priority' => 'High',
            'status' => 'Pending',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.milestones.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('milestones', [
            'name' => 'Phase 1 MVP',
            'project_id' => $this->project->id,
            'company_id' => $this->company->id,
        ]);
    }

    public function test_milestone_progress_auto_calculates_on_task_assignment()
    {
        $milestone = Milestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'status' => 'Pending',
            'progress' => 0
        ]);

        $task1 = Task::factory()->create(['project_id' => $this->project->id, 'status' => 'Completed']);
        $task2 = Task::factory()->create(['project_id' => $this->project->id, 'status' => 'Pending']);

        $response = $this->actingAs($this->admin)->post(route('admin.milestones.assign-tasks', $milestone), [
            'task_ids' => [$task1->id, $task2->id]
        ]);

        $response->assertRedirect();
        
        $milestone->refresh();
        
        // 1 out of 2 tasks is completed
        $this->assertEquals(50, $milestone->progress);
        $this->assertEquals('In Progress', $milestone->status);
    }

    public function test_milestone_completes_when_all_tasks_completed()
    {
        $milestone = Milestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
        ]);

        $task = Task::factory()->create(['project_id' => $this->project->id, 'status' => 'Pending']);
        
        $this->actingAs($this->admin)->post(route('admin.milestones.assign-tasks', $milestone), [
            'task_ids' => [$task->id]
        ]);

        $milestone->refresh();
        $this->assertEquals(0, $milestone->progress);

        // Update task to completed, which should trigger TaskService to recalculate milestone
        // Since we are unit testing the service here, we can just call it or simulate the controller.
        
        app(\App\Services\TaskService::class)->updateTask($task, ['status' => 'Completed', 'progress' => 100]);

        $milestone->refresh();
        $this->assertEquals(100, $milestone->progress);
        $this->assertEquals('Completed', $milestone->status);
        $this->assertNotNull($milestone->completed_at);
    }

    public function test_task_cannot_be_assigned_to_multiple_active_milestones()
    {
        $milestone1 = Milestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'status' => 'In Progress'
        ]);

        $milestone2 = Milestone::factory()->create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'status' => 'Pending'
        ]);

        $task = Task::factory()->create([
            'project_id' => $this->project->id,
            'status' => 'Pending'
        ]);

        // Assign to first milestone
        $this->actingAs($this->admin)->post(route('admin.milestones.assign-tasks', $milestone1), [
            'task_ids' => [$task->id]
        ])->assertSessionHas('success');

        // Try to assign to second milestone
        $response = $this->actingAs($this->admin)->post(route('admin.milestones.assign-tasks', $milestone2), [
            'task_ids' => [$task->id]
        ]);
        
        $response->assertSessionHas('error');
    }
}
