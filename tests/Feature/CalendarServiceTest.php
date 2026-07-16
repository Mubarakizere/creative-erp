<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Task;
use App\Services\CalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CalendarService $service;
    protected Company $company;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->service = app(CalendarService::class);
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_calendar_service_aggregates_events()
    {
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Create a meeting
        Meeting::factory()->create([
            'company_id' => $this->company->id,
            'start_at' => now()->startOfMonth()->addDays(2)->setHour(10),
            'end_at' => now()->startOfMonth()->addDays(2)->setHour(11),
        ])->attendees()->attach($this->user->id, ['attendance_status' => 'accepted']);

        // Create a task
        Task::factory()->create([
            'company_id' => $this->company->id,
            'assigned_to' => $this->user->id,
            'due_date' => now()->startOfMonth()->addDays(5),
            'status' => 'Pending',
        ]);

        $events = $this->service->getEvents($start, $end, $this->user->id, $this->company->id);

        // We expect at least 2 events (meeting and task) in the collection
        $this->assertGreaterThanOrEqual(2, $events->count());

        $types = $events->pluck('type')->unique()->toArray();
        $this->assertContains('meeting', $types);
        $this->assertContains('task', $types);
    }

    public function test_todays_schedule_returns_correct_events()
    {
        // Today meeting
        Meeting::factory()->create([
            'company_id' => $this->company->id,
            'start_at' => now()->setHour(14),
            'end_at' => now()->setHour(15),
        ])->attendees()->attach($this->user->id, ['attendance_status' => 'accepted']);

        // Tomorrow meeting (should not be included)
        Meeting::factory()->create([
            'company_id' => $this->company->id,
            'start_at' => now()->addDay()->setHour(14),
            'end_at' => now()->addDay()->setHour(15),
        ])->attendees()->attach($this->user->id, ['attendance_status' => 'accepted']);

        $schedule = $this->service->getTodaysSchedule($this->user->id, $this->company->id);

        $this->assertCount(1, $schedule);
        $this->assertEquals('meeting', $schedule->first()->type);
    }
}
