<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;

class OrganizationMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'companies' => Company::count(),
            'branches' => Branch::count(),
            'departments' => Department::count(),
        ];
    }

    public function widgets(): array
    {
        return [];
    }

    public function reports(): array
    {
        return [
            // Company Summary data could go here
        ];
    }
}
