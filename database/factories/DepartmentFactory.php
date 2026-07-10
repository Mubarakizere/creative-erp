<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Department::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'Human Resources', 'Finance', 'Procurement', 'Engineering',
            'Electrical', 'Mechanical', 'Architecture', 'Administration',
            'Warehouse', 'IT', 'Operations', 'Quality Assurance',
            'Health & Safety', 'Legal', 'Sales & Marketing',
        ];

        return [
            'uuid' => (string) Str::uuid(),
            'company_id' => Company::factory(),
            'branch_id' => Branch::factory(),
            'name' => fake()->unique()->randomElement($departments) . ' ' . fake()->numerify('##'),
            'code' => fake()->unique()->bothify('DEPT-###??'),
            'manager_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'description' => fake()->optional()->sentence(),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the department is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
