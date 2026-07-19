<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Client;

class ClientMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        return [
            'clients' => $this->applyFilters(Client::query(), $filters)->count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        return [
            'latestClients' => $this->applyFilters(Client::query(), $filters)->with('company')->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            // Client Summary data could go here
        ];
    }
}
