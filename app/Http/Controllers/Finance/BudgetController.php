<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\BudgetService;
use App\Models\Budget;

class BudgetController extends Controller
{
    use \App\Traits\LogsActivity;

    protected BudgetService $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Budget::class);
        $budgets = Budget::where('company_id', auth()->user()->company_id ?? 1)
            ->with(['fiscalYear'])
            ->latest()
            ->paginate(15);
        
        return view('admin.finance.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $this->authorize('create', Budget::class);
        $companyId = auth()->user()->company_id ?? 1;
        $fiscalYears = \App\Models\FiscalYear::where('company_id', $companyId)->get();
        $accounts = \App\Models\ChartOfAccount::where('company_id', $companyId)->where('status', 'active')->get();
        $categories = \App\Models\BudgetCategory::where('company_id', $companyId)->get();

        return view('admin.finance.budgets.create', compact('fiscalYears', 'accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Budget::class);
        $companyId = auth()->user()->company_id ?? 1;

        $request->validate([
            'name' => 'required|string|max:255',
            'fiscal_year_id' => 'required|exists:fiscal_years,id',
            'lines' => 'required|array|min:1',
            'lines.*.budget_category_id' => 'nullable|exists:budget_categories,id',
            'lines.*.chart_of_account_id' => 'nullable|exists:chart_of_accounts,id',
            'lines.*.amount' => 'required|numeric|min:0',
        ]);

        $totalAmount = collect($request->lines)->sum('amount');

        $budget = Budget::create([
            'company_id' => $companyId,
            'name' => $request->name,
            'fiscal_year_id' => $request->fiscal_year_id,
            'total_amount' => $totalAmount,
            'status' => 'draft',
        ]);

        foreach ($request->lines as $line) {
            \App\Models\BudgetLine::create([
                'budget_id' => $budget->id,
                'budget_category_id' => $line['budget_category_id'] ?? null,
                'chart_of_account_id' => $line['chart_of_account_id'] ?? null,
                'amount' => $line['amount'],
            ]);
        }

        $this->logActivity('budget_created', $budget, ['amount' => $totalAmount]);

        return redirect()->route('admin.finance.budgets.show', $budget)
            ->with('success', 'Budget created successfully.');
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        $analysis = $this->budgetService->getBudgetVsActual($budget->id);
        return view('admin.finance.budgets.show', compact('budget', 'analysis'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:draft,approved,active,closed',
        ]);

        $budget->update($request->only(['name', 'status']));

        $this->logActivity('budget_updated', $budget, ['status' => $budget->status, 'name' => $budget->name]);

        return redirect()->route('admin.finance.budgets.show', $budget)
            ->with('success', 'Budget updated successfully.');
    }
}
