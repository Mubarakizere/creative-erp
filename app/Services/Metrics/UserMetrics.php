<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\User;

class UserMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'users' => User::count(),
        ];
    }

    public function widgets(): array
    {
        return [];
    }

    public function reports(): array
    {
        return [
            // User Summary data
        ];
    }
}
