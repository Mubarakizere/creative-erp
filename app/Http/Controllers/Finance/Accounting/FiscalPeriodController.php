<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Services\Finance\FiscalPeriodService;
use Illuminate\Http\Request;

class FiscalPeriodController extends Controller
{
    protected FiscalPeriodService $fiscalPeriodService;

    public function __construct(FiscalPeriodService $fiscalPeriodService)
    {
        $this->fiscalPeriodService = $fiscalPeriodService;
    }

    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $fiscalYears = FiscalYear::where('company_id', $companyId)
            ->orderBy('start_date', 'desc')
            ->with('periods')
            ->get();

        return view('admin.finance.accounting.fiscal-periods.index', compact('fiscalYears'));
    }

    public function storeYear(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $validated['company_id'] = $companyId;

        $this->fiscalPeriodService->createFiscalYear($validated);

        return back()->with('success', 'Fiscal Year created successfully.');
    }

    public function storePeriod(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);
        
        $validated['company_id'] = $companyId;
        $validated['status'] = 'Open';

        $this->fiscalPeriodService->createAccountingPeriod($validated);

        return back()->with('success', 'Accounting Period created successfully.');
    }
    
    public function closePeriod(AccountingPeriod $period)
    {
        try {
            $this->fiscalPeriodService->closeAccountingPeriod($period);
            return back()->with('success', 'Accounting Period closed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function closeYear(FiscalYear $year)
    {
        try {
            $this->fiscalPeriodService->closeFiscalYear($year);
            return back()->with('success', 'Fiscal Year closed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
