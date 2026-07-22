<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Services\Finance\JournalService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $journals = Journal::where('company_id', $companyId)
            ->with('fiscalYear', 'accountingPeriod')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('admin.finance.accounting.journals.index', compact('journals'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $accounts = ChartOfAccount::where('company_id', $companyId)->where('is_active', true)->orderBy('code')->get();
        $fiscalYears = FiscalYear::where('company_id', $companyId)->where('is_closed', false)->get();
        $periods = AccountingPeriod::where('company_id', $companyId)->where('status', 'Open')->get();

        return view('admin.finance.accounting.journals.create', compact('accounts', 'fiscalYears', 'periods'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'date' => 'required|date',
            'fiscal_year_id' => 'nullable|exists:fiscal_years,id',
            'accounting_period_id' => 'nullable|exists:accounting_periods,id',
            'reference_number' => 'nullable|string|max:255',
            'memo' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.description' => 'required|string',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $data = [
            'company_id' => $companyId,
            'date' => $validated['date'],
            'fiscal_year_id' => $validated['fiscal_year_id'],
            'accounting_period_id' => $validated['accounting_period_id'],
            'reference_number' => $validated['reference_number'],
            'memo' => $validated['memo'],
            'status' => 'Draft',
            'created_by' => auth()->id(),
        ];

        try {
            $this->journalService->createManualJournal($data, $validated['entries']);
            return redirect()->route('admin.finance.accounting.journals.index')
                ->with('success', 'Journal created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Journal $journal)
    {
        $journal->load(['entries.chartOfAccount', 'fiscalYear', 'accountingPeriod', 'company']);
        return view('admin.finance.accounting.journals.show', compact('journal'));
    }

    public function post(Journal $journal)
    {
        try {
            $this->journalService->postJournal($journal);
            return back()->with('success', 'Journal posted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
