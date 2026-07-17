<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Department;

class OrganizationMetrics implements MetricProvider
{
    protected ?string $companyId;

    public function __construct()
    {
        $this->companyId = auth()->user()?->company_id;
    }

    public function cards(): array
    {
        $companyQuery = Company::query();
        if ($this->companyId) {
            $companyQuery->where('id', $this->companyId);
        }

        $branchQuery = Branch::query();
        if ($this->companyId) {
            $branchQuery->where('company_id', $this->companyId);
        }

        $deptQuery = Department::query();
        if ($this->companyId) {
            $deptQuery->whereHas('branch', function($q) {
                $q->where('company_id', $this->companyId);
            });
        }

        return [
            'companies' => auth()->user()?->can('company.view') ? $companyQuery->count() : 0,
            'branches' => auth()->user()?->can('branch.view') ? $branchQuery->count() : 0,
            'departments' => auth()->user()?->can('department.view') ? $deptQuery->count() : 0,
        ];
    }

    public function widgets(): array
    {
        return [];
    }

    public function reports(): array
    {
        return [];
    }
}
