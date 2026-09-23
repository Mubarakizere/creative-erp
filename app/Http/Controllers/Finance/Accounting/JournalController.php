<?php

namespace App\Http\Controllers\Finance\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Models\Company;
use App\Models\Project;
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

        $query = Journal::query()
            ->with(['company', 'project', 'fiscalYear', 'accountingPeriod']);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        } else {
            $query->where('company_id', $companyId);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function ($q) use ($search) {
                $q->where('journal_number', 'like', "%{$search}%")
                  ->orWhere('memo', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($projQ) use ($search) {
                      $projQ->where('name', 'like', "%{$search}%")
                            ->orWhere('project_code', 'like', "%{$search}%");
                  });
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

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        if ($companies->isEmpty()) {
            $companies = Company::all();
        }

        $projects = Project::where('company_id', $companyId)
            ->whereNotIn('status', ['Cancelled', 'Closed'])
            ->orderBy('name')
            ->get(['id', 'name', 'project_code']);

        return view('admin.finance.accounting.journals.index', compact('journals', 'stats', 'companies', 'projects'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Journal::class);
        $userCompanyId = auth()->user()->company_id ?? 1;

        $companies = Company::where('status', 'active')->orderBy('name')->get();
        if ($companies->isEmpty()) {
            $companies = Company::all();
        }

        $projects = Project::whereNotIn('status', ['Cancelled', 'Closed'])
            ->with(['company:id,name', 'client:id,name'])
            ->orderBy('name')
            ->get(['id', 'name', 'project_code', 'company_id', 'client_id']);

        // Load active chart of accounts with account types
        $accounts = ChartOfAccount::where('is_active', true)
            ->with('accountType')
            ->orderBy('code')
            ->get();

        $selectedProjectId = $request->input('project_id');
        $selectedCompanyId = $request->input('company_id', $userCompanyId);
        if ($selectedProjectId) {
            $preselectedProject = $projects->firstWhere('id', $selectedProjectId);
            if ($preselectedProject && $preselectedProject->company_id) {
                $selectedCompanyId = $preselectedProject->company_id;
            }
        }

        $journal_number = app(\App\Services\SequenceService::class)->generate('journal', $selectedCompanyId);

        return view('admin.finance.accounting.journals.create', compact(
            'companies',
            'projects',
            'accounts',
            'selectedCompanyId',
            'selectedProjectId',
            'journal_number'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Journal::class);

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'project_id' => 'nullable|exists:projects,id',
            'journal_number' => 'required|string|unique:journals,journal_number',
            'date' => 'required|date',
            'reference_number' => 'nullable|string|max:255',
            'memo' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'entries.*.description' => 'required|string',
            'entries.*.debit' => 'required|numeric|min:0',
            'entries.*.credit' => 'required|numeric|min:0',
        ]);

        $data = [
            'company_id' => $validated['company_id'],
            'project_id' => $validated['project_id'] ?? null,
            'journal_number' => $validated['journal_number'],
            'date' => $validated['date'],
            'reference_number' => $validated['reference_number'] ?? null,
            'memo' => $validated['memo'],
            'status' => 'Draft',
            'created_by' => auth()->id(),
        ];

        // If project is set and has a client, attach client_id as well
        if (!empty($validated['project_id'])) {
            $project = Project::find($validated['project_id']);
            if ($project && $project->client_id) {
                $data['client_id'] = $project->client_id;
            }
        }

        try {
            $this->journalService->createManualJournal($data, $validated['entries']);
            return redirect()->route('admin.finance.accounting.journals.index')
                ->with('success', 'Journal entry created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Journal $journal)
    {
        $journal->load(['entries.chartOfAccount.accountType', 'project', 'company', 'branch', 'department', 'fiscalYear', 'accountingPeriod']);
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
