<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+2 months');
        $end = (clone $start)->modify('+' . $this->faker->numberBetween(30, 180) . ' minutes');

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph,
            'company_id' => Company::factory(),
            'branch_id' => Branch::factory(),
            'project_id' => $this->faker->boolean(70) ? Project::factory() : null,
            'meeting_type' => $this->faker->randomElement(array_keys(Meeting::getMeetingTypes())),
            'status' => $this->faker->randomElement(array_keys(Meeting::getStatuses())),
            'location' => $this->faker->boolean(60) ? $this->faker->company . ' Conference Room' : null,
            'meeting_link' => $this->faker->boolean(40) ? 'https://meet.google.com/' . $this->faker->lexify('???-????-???') : null,
            'start_at' => $start,
            'end_at' => $end,
            'timezone' => $this->faker->timezone,
            'created_by' => User::factory(),
            'updated_by' => function (array $attributes) {
                return $attributes['created_by'];
            },
        ];
    }
}
