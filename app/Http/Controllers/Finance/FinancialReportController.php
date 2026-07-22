<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\FinancialStatementService;

class FinancialReportController extends Controller
{
    protected FinancialStatementService $financialService;

    public function __construct(FinancialStatementService $financialService)
    {
        $this->financialService = $financialService;
    }

    private function extractFilters(Request $request): array
    {
        return $request->only([
            'branch_id',
            'department_id',
            'project_id',
            'client_id',
            'currency_code',
            'fiscal_year_id',
            'accounting_period_id'
        ]);
    }

    public function profitAndLoss(Request $request)
    {
        $this->authorize('view', \App\Policies\FinancialReportPolicy::class);
        $companyId = auth()->user()->company_id;
        $filters = $this->extractFilters($request);
        $data = $this->financialService->generateProfitAndLoss($companyId, $request->get('start_date'), $request->get('end_date'), $filters);
        return response()->json($data);
    }

    public function balanceSheet(Request $request)
    {
        $this->authorize('view', \App\Policies\FinancialReportPolicy::class);
        $companyId = auth()->user()->company_id;
        $filters = $this->extractFilters($request);
        $data = $this->financialService->generateBalanceSheet($companyId, $request->get('as_of_date'), $filters);
        return response()->json($data);
    }

    public function cashFlow(Request $request)
    {
        $this->authorize('view', \App\Policies\FinancialReportPolicy::class);
        $companyId = auth()->user()->company_id;
        $filters = $this->extractFilters($request);
        $data = $this->financialService->generateCashFlowStatement($companyId, $request->get('start_date'), $request->get('end_date'), $filters);
        return response()->json($data);
    }
}
