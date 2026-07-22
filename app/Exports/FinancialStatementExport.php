<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Collection;

class FinancialStatementExport implements FromView
{
    protected Collection $data;
    protected string $type;
    protected array $filters;

    public function __construct(Collection $data, string $type, array $filters = [])
    {
        $this->data = $data;
        $this->type = $type;
        $this->filters = $filters;
    }

    public function view(): View
    {
        // For excel export, we can just use the same view logic as PDF but rendered as a clean HTML table
        return view('admin.reports.exports.financial-table', [
            'data' => $this->data,
            'type' => $this->type,
            'filters' => $this->filters,
        ]);
    }
}
