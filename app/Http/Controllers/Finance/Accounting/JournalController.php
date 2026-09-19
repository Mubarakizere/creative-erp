<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Services\Finance\JournalService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JournalController extends Controller
{
    use AuthorizesRequests;

    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $query = Journal::where('company_id', $companyId)
            ->with(['fiscalYear', 'accountingPeriod']);

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $journals = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Journal::where('company_id', $companyId)->count(),
            'posted' => Journal::where('company_id', $companyId)->where('status', 'Posted')->count(),
            'draft' => Journal::where('company_id', $companyId)->where('status', 'Draft')->count(),
            'total_volume' => Journal::where('company_id', $companyId)->where('status', 'Posted')->sum('total_debit'),
        ];

        return view('admin.finance.accounting.journals.index', compact('journals', 'stats'));
    }

    public function create()
    {
        $companyId = auth()->user()->company_id ?? 1;
        
        $accounts = ChartOfAccount::where('company_id', $companyId)->where('is_active', true)->orderBy('code')->get();
        $fiscalYears = FiscalYear::where('company_id', $companyId)->where('is_closed', false)->get();
        $periods = AccountingPeriod::where('company_id', $companyId)->where('status', 'Open')->get();

        $journal_number = app(\App\Services\SequenceService::class)->generate('journal', $companyId);

        return view('admin.finance.accounting.journals.create', compact('accounts', 'fiscalYears', 'periods', 'journal_number'));
    }

    public function store(Request $request)
    {
        $companyId = auth()->user()->company_id ?? 1;

        $validated = $request->validate([
            'journal_number' => 'required|string|unique:journals,journal_number',
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
            'journal_number' => $validated['journal_number'],
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
        $journal->load(['entries.chartOfAccount.accountType', 'fiscalYear', 'accountingPeriod', 'company', 'branch', 'department']);
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

    public function destroy(Journal $journal)
    {
        $this->authorize('delete', $journal);

        if ($journal->status !== 'Draft') {
            return back()->with('error', 'Only draft journals can be deleted.');
        }

        $journal->delete();

        return redirect()->route('admin.finance.accounting.journals.index')
            ->with('success', 'Journal entry deleted successfully.');
    }
}
