<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Company;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimeTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $company->id]);
        $this->project = Project::factory()->create(['company_id' => $company->id]);
        
        $role = Role::create(['name' => 'Admin']);
        // Time tracking permissions
        $role->givePermissionTo(['time.view', 'time.create', 'time.update', 'time.delete']);
        $this->user->assignRole($role);
    }

    public function test_user_can_view_time_tracking_index()
    {
        $this->actingAs($this->user)
            ->get(route('admin.time-tracking.index'))
            ->assertStatus(200)
            ->assertViewIs('admin.time-tracking.index');
    }

    public function test_user_can_create_time_entry_manually()
    {
        $startTime = Carbon::now()->subHours(2);
        $endTime = Carbon::now();

        $response = $this->actingAs($this->user)
            ->post(route('admin.time-tracking.store'), [
                'project_id' => $this->project->id,
                'start_time' => $startTime->toDateTimeString(),
                'end_time' => $endTime->toDateTimeString(),
                'description' => 'Manual entry test',
                'billable' => 1,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('time_entries', [
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'description' => 'Manual entry test',
            'duration_minutes' => 120,
            'status' => 'completed',
        ]);
    }
}
