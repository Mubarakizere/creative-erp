<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Milestone>
 */
class MilestoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Pending', 'In Progress', 'Completed', 'On Hold']);
        $progress = $status === 'Completed' ? 100 : $this->faker->numberBetween(0, 99);
        $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
        $dueDate = $this->faker->dateTimeBetween('now', '+3 months');

        return [
            'uuid' => (string) Str::uuid(),
            'company_id' => Company::factory(),
            'project_id' => Project::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'status' => $status,
            'progress' => $progress,
            'start_date' => $startDate,
            'due_date' => $dueDate,
            'completed_at' => $status === 'Completed' ? $this->faker->dateTimeBetween($startDate, 'now') : null,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
