<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $durationMinutes = $this->faker->numberBetween(15, 240); // 15 mins to 4 hours
        $endTime = (clone $startTime)->modify("+{$durationMinutes} minutes");
        
        return [
            'uuid' => Str::uuid(),
            'company_id' => Company::factory(),
            'branch_id' => Branch::factory(),
            'project_id' => Project::factory(),
            'task_id' => null, // Typically set in seeder based on project
            'user_id' => User::factory(),
            'description' => $this->faker->sentence(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
            'billable' => $this->faker->boolean(80), // 80% billable
            'hourly_rate' => $this->faker->randomFloat(2, 20, 150),
            'status' => 'completed',
        ];
    }
    
    public function running()
    {
        return $this->state(function (array $attributes) {
            $startTime = now()->subMinutes(rand(10, 120));
            return [
                'end_time' => null,
                'duration_minutes' => 0,
                'status' => 'running',
                'start_time' => $startTime,
            ];
        });
    }
    
    public function paused()
    {
        return $this->state(function (array $attributes) {
            return [
                'end_time' => null,
                'status' => 'paused',
            ];
        });
    }
}
