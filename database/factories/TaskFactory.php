<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $dueDate = (clone $startDate)->modify('+' . rand(1, 14) . ' days');

        return [
            'uuid' => $this->faker->uuid(),
            'company_id' => Company::factory(),
            'project_id' => Project::factory(),
            'parent_id' => null,
            'assigned_to' => User::factory(),
            'task_code' => 'TSK-' . $this->faker->unique()->numberBetween(1000, 9999),
            'name' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'status' => $this->faker->randomElement(['Pending', 'In Progress', 'Waiting Review', 'Completed', 'Cancelled']),
            'progress' => $this->faker->numberBetween(0, 100),
            'start_date' => $startDate->format('Y-m-d'),
            'due_date' => $dueDate->format('Y-m-d'),
            'completed_at' => null,
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }
}
