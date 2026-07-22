<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Services\Finance\AccountingReportService;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    protected AccountingReportService $reportService;

    public function __construct(AccountingReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;
        $fiscalYearId = $request->input('fiscal_year_id');
        
        $fiscalYears = FiscalYear::where('company_id', $companyId)->orderBy('start_date', 'desc')->get();
        
        if (!$fiscalYearId && $fiscalYears->where('is_closed', false)->first()) {
            $fiscalYearId = $fiscalYears->where('is_closed', false)->first()->id;
        }

        $filters = $request->only([
            'branch_id',
            'department_id',
            'project_id',
            'client_id',
            'currency_code'
        ]);

        $trialBalance = $this->reportService->generateTrialBalance($companyId, $fiscalYearId, $filters);

        // Fetch dimension lists for the filter bar
        $branches = \App\Models\Branch::where('company_id', $companyId)->get();
        $departments = \App\Models\Department::where('company_id', $companyId)->get();
        $projects = \App\Models\Project::where('company_id', $companyId)->get();
        $clients = \App\Models\Client::where('company_id', $companyId)->get();

        return view('admin.finance.accounting.ledger.index', compact('trialBalance', 'fiscalYears', 'fiscalYearId', 'filters', 'branches', 'departments', 'projects', 'clients'));
    }
}
