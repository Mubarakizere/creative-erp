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
            'customerProfitability' => $this->customerProfitability($filters)
        ];
    }

    public function customerProfitability(array $filters = []): array
    {
        // Customer Profitability: Sum of Invoices
        $companyId = $filters['company_id'] ?? (auth()->user() ? auth()->user()->company_id : null);
        if (!$companyId) return [];

        $clients = Client::where('company_id', $companyId)->get();
        
        $profitability = [];
        foreach ($clients as $client) {
            $revenue = \App\Models\Invoice::where('client_id', $client->id)
                ->where('status', '!=', 'Cancelled')
                ->where('status', '!=', 'Voided')
                ->sum('total_amount');

            if ($revenue > 0) {
                $profitability[] = [
                    'id' => $client->id,
                    'name' => $client->display_name,
                    'revenue' => (float) $revenue,
                    'expenses' => 0,
                    'net_profit' => (float) $revenue
                ];
            }
        }

        return collect($profitability)->sortByDesc('net_profit')->values()->toArray();
    }
}
