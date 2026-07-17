<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'type' => 'App\Notifications\AppNotification',
            'notifiable_type' => 'App\Models\User',
            'notifiable_id' => 1,
            'data' => [
                'title' => $this->faker->sentence,
                'message' => $this->faker->paragraph,
                'category' => 'system',
                'priority' => 'Normal'
            ],
            'read_at' => null,
            'category' => 'system',
            'priority' => 'Normal',
            'company_id' => null,
            'branch_id' => null,
            'created_by' => null,
        ];
    }
}
