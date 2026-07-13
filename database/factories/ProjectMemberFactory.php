<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectMember>
 */
class ProjectMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'project_role' => $this->faker->randomElement(['Site Engineer', 'Architect', 'Civil Engineer', 'Technician']),
            'allocation_percentage' => $this->faker->numberBetween(10, 100),
            'hourly_rate' => $this->faker->optional()->randomFloat(2, 20, 100),
            'joined_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(['Active', 'Active', 'Active', 'Inactive']),
        ];
    }
}
