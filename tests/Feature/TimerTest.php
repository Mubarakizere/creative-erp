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

class TimerTest extends TestCase
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
        $role->givePermissionTo(['time.view', 'time.create', 'time.update', 'time.delete']);
        $this->user->assignRole($role);
    }

    public function test_user_can_start_timer()
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.time-tracking.timer.start'), [
                'project_id' => $this->project->id,
                'description' => 'Working on new feature',
                'billable' => 1,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('time_entries', [
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'status' => 'running',
        ]);
    }

    public function test_user_cannot_start_multiple_timers()
    {
        // Start first timer
        TimeEntry::create([
            'user_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'project_id' => $this->project->id,
            'start_time' => Carbon::now(),
            'status' => 'running'
        ]);

        // Attempt to start second timer
        $response = $this->actingAs($this->user)
            ->post(route('admin.time-tracking.timer.start'), [
                'project_id' => $this->project->id,
            ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, TimeEntry::where('user_id', $this->user->id)->where('status', 'running')->count());
    }

    public function test_user_can_stop_timer()
    {
        $timer = TimeEntry::create([
            'user_id' => $this->user->id,
            'company_id' => $this->user->company_id,
            'project_id' => $this->project->id,
            'start_time' => Carbon::now()->subMinutes(30),
            'status' => 'running'
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.time-tracking.timer.stop', $timer));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('time_entries', [
            'id' => $timer->id,
            'status' => 'completed',
        ]);
    }
}
