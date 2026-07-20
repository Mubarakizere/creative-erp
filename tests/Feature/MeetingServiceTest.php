<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Meeting;
use App\Services\MeetingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MeetingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected MeetingService $service;
    protected Company $company;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->service = app(MeetingService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_can_create_meeting()
    {
        $this->actingAs($this->user);

        $data = [
            'title' => 'Project Kickoff',
            'company_id' => $this->company->id,
            'branch_id' => \App\Models\Branch::factory()->create(['company_id' => $this->company->id])->id,
            'meeting_type' => 'project',
            'start_at' => now()->addDay()->toDateTimeString(),
            'end_at' => now()->addDay()->addHour()->toDateTimeString(),
            'timezone' => 'UTC',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ];

        $attendees = User::factory(2)->create(['company_id' => $this->company->id])->pluck('id')->toArray();

        $meeting = $this->service->createMeeting($data, $attendees);

        $this->assertDatabaseHas('meetings', [
            'id' => $meeting->id,
            'title' => 'Project Kickoff',
        ]);

        $this->assertCount(3, $meeting->attendees); // 2 + organizer
        $this->assertTrue($meeting->isOrganizer($this->user));
    }

    public function test_can_detect_scheduling_conflicts()
    {
        $start = now()->addDays(2)->setHour(10)->setMinute(0);
        $end = now()->addDays(2)->setHour(11)->setMinute(0);
        $existing = Meeting::factory()->create([
            'start_at' => $start,
            'end_at' => $end,
            'status' => 'scheduled',
        ]);
        $existing->attendees()->attach($this->user->id, ['attendance_status' => 'accepted']);

        // Check conflict for same time
        $conflicts = $this->service->detectConflictsForAttendees(
            $start->toDateTimeString(),
            $end->toDateTimeString(),
            [$this->user->id]
        );

        $this->assertCount(1, $conflicts);
        $this->assertEquals($this->user->id, $conflicts[0]['user']->id);
        $this->assertCount(1, $conflicts[0]['conflicts']);
    }

    public function test_can_cancel_meeting()
    {
        $this->actingAs($this->user);

        $meeting = Meeting::factory()->create(['status' => 'scheduled']);
        
        $this->service->cancelMeeting($meeting);

        $this->assertEquals('cancelled', $meeting->fresh()->status);
    }
}
