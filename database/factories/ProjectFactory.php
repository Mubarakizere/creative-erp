<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $company = Company::inRandomOrder()->first() ?? Company::factory()->create();
        $branch = Branch::where('company_id', $company->id)->inRandomOrder()->first() ?? Branch::factory()->create(['company_id' => $company->id]);
        $client = Client::where('company_id', $company->id)->inRandomOrder()->first() ?? Client::factory()->create(['company_id' => $company->id, 'branch_id' => $branch->id]);
        $manager = User::where('company_id', $company->id)->inRandomOrder()->first() ?? User::factory()->create(['company_id' => $company->id]);

        return [
            'uuid' => (string) Str::uuid(),
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'client_id' => $client->id,
            'project_manager_id' => $manager->id,
            'project_code' => 'PRJ-' . strtoupper($this->faker->unique()->lexify('????-####')),
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'category' => $this->faker->randomElement(['Construction', 'IT', 'Consulting', 'Design']),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Critical']),
            'status' => $this->faker->randomElement(['Planning', 'Pending', 'In Progress', 'On Hold', 'Completed', 'Cancelled', 'Closed']),
            'progress' => $this->faker->numberBetween(0, 100),
            'estimated_budget' => $this->faker->randomFloat(2, 10000, 500000),
            'actual_budget' => $this->faker->randomFloat(2, 9000, 550000),
            'estimated_cost' => $this->faker->randomFloat(2, 8000, 480000),
            'actual_cost' => $this->faker->randomFloat(2, 7000, 500000),
            'currency' => 'RWF',
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'planned_end_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'contract_number' => 'CNT-' . $this->faker->unique()->numerify('#####'),
            'location' => $this->faker->city(),
            'created_by' => $manager->id,
            'updated_by' => $manager->id,
        ];
    }
}
