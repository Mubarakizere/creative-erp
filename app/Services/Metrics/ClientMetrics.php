<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Client;

class ClientMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'clients' => Client::count(),
        ];
    }

    public function widgets(): array
    {
        return [
            'latestClients' => Client::with('company')->latest()->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [
            // Client Summary data could go here
        ];
    }
}
