<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Task;
use App\Models\TimeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TimesheetAndTimeTrackingFixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_timesheet_page_loads_for_user_with_time_view()
    {
        $company = Company::factory()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('Supervisor');

        $project = Project::factory()->create(['company_id' => $company->id]);
        ProjectMember::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'department_id' => $department->id,
            'project_role' => 'Supervisor',
            'status' => 'Active',
            'joined_at' => now(),
        ]);

        TimeEntry::create([
            'company_id' => $company->id,
            'user_id' => $user->id,
            'project_id' => $project->id,
            'start_time' => now()->subHours(2),
            'end_time' => now(),
            'duration_minutes' => 120,
            'status' => 'completed',
            'billable' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.time-tracking.timesheet'));
        $response->assertStatus(200);
        $response->assertSee('My Timesheet');
    }

    public function test_productivity_reports_page_accessible_for_supervisor_and_client()
    {
        $company = Company::factory()->create();
        
        $supervisor = User::factory()->create(['company_id' => $company->id]);
        $supervisor->assignRole('Supervisor');

        $client = User::factory()->create(['company_id' => $company->id]);
        $client->assignRole('Client');

        $responseSupervisor = $this->actingAs($supervisor)->get(route('admin.time-tracking.reports'));
        $responseSupervisor->assertStatus(200);

        $responseClient = $this->actingAs($client)->get(route('admin.time-tracking.reports'));
        $responseClient->assertStatus(200);
    }

    public function test_user_can_only_log_time_for_assigned_projects()
    {
        $company = Company::factory()->create();
        $department = Department::factory()->create(['company_id' => $company->id]);
        
        $user = User::factory()->create(['company_id' => $company->id]);
        $user->assignRole('Engineer');

        $assignedProject = Project::factory()->create(['company_id' => $company->id]);
        $unassignedProject = Project::factory()->create(['company_id' => $company->id]);

        ProjectMember::create([
            'project_id' => $assignedProject->id,
            'user_id' => $user->id,
            'department_id' => $department->id,
            'project_role' => 'Engineer',
            'status' => 'Active',
            'joined_at' => now(),
        ]);

        // Logging time on assigned project succeeds
        $responseValid = $this->actingAs($user)->post(route('admin.time-tracking.store'), [
            'project_id' => $assignedProject->id,
            'start_time' => now()->subHours(3)->toDateTimeString(),
            'end_time' => now()->subHour()->toDateTimeString(),
            'description' => 'Site inspection',
            'billable' => 1,
        ]);
        $responseValid->assertSessionHas('success');

        // Logging time on unassigned project fails
        $responseInvalid = $this->actingAs($user)->post(route('admin.time-tracking.store'), [
            'project_id' => $unassignedProject->id,
            'start_time' => now()->subHours(3)->toDateTimeString(),
            'end_time' => now()->subHour()->toDateTimeString(),
            'description' => 'Unauthorized entry',
            'billable' => 1,
        ]);
        $responseInvalid->assertSessionHas('error');
    }
}
