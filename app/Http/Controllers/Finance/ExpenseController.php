<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Services\ProjectFinancialService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct(private ProjectFinancialService $financialService) {}

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ProjectExpense::class);

        $query = ProjectExpense::query()->with(['project', 'task', 'user']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('expense_type')) {
            match ($request->input('expense_type')) {
                'labor' => $query->whereIn('category', ['Labor', 'Worker Salary', 'Payroll']),
                'materials' => $query->whereIn('category', ['Materials', 'Material']),
                'other' => $query->whereNotIn('category', ['Labor', 'Worker Salary', 'Payroll', 'Materials', 'Material']),
                default => null,
            };
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($expenseQuery) use ($search) {
                $expenseQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('vendor_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($projectQuery) use ($search) {
                        $projectQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('project_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('task', fn ($taskQuery) => $taskQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $entries = $query->orderByDesc('expense_date')->orderByDesc('id')->get();
        $groups = $this->groupExpenses($entries);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $taskGroups = new LengthAwarePaginator(
            $groups->forPage($page, $perPage)->values(),
            $groups->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $allExpenses = ProjectExpense::query();
        $laborCategories = ['Labor', 'Worker Salary', 'Payroll'];
        $materialCategories = ['Materials', 'Material'];
        $stats = [
            'labor' => (clone $allExpenses)->whereIn('category', $laborCategories)->count(),
            'materials' => (clone $allExpenses)->whereIn('category', $materialCategories)->count(),
            'other' => (clone $allExpenses)->whereNotIn('category', array_merge($laborCategories, $materialCategories))->count(),
        ];

        $projects = Project::query()->orderBy('name')->get(['id', 'name', 'project_code']);
        $formProjects = $projects
            ->filter(fn (Project $project) => Gate::allows('create', [ProjectExpense::class, $project]))
            ->values();
        $editableProjectIds = $entries
            ->filter(fn (ProjectExpense $expense) => Gate::allows('update', $expense))
            ->pluck('project_id')
            ->unique();
        $formOptionProjects = $projects
            ->filter(fn (Project $project) => $formProjects->contains('id', $project->id) || $editableProjectIds->contains($project->id))
            ->values();
        $projectOptions = $formOptionProjects->map(function (Project $project) use ($formProjects) {
            $project->loadMissing('tasks:id,project_id,name,task_code');

            return [
                'id' => $project->id,
                'label' => ($project->project_code ? $project->project_code . ' · ' : '') . $project->name,
                'currency' => $project->currency ?: 'RWF',
                'canCreate' => $formProjects->contains('id', $project->id),
                'tasks' => $project->tasks->map(fn ($task) => [
                    'id' => $task->id,
                    'name' => ($task->task_code ? '[' . $task->task_code . '] ' : '') . $task->name,
                ])->values(),
            ];
        })->values();

        $accountOptions = ChartOfAccount::query()
            ->with('accountType')
            ->where('is_active', true)
            ->whereHas('accountType', fn ($accountTypeQuery) => $accountTypeQuery->where('category', 'Expense'))
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn (ChartOfAccount $account) => [
                'value' => trim($account->code . ' · ' . $account->name),
                'label' => trim($account->code . ' · ' . $account->name),
            ])
            ->values();

        $resourceTypes = ["Helper's", 'Mason', 'Carpenter', 'Steel fixer', 'Welder', 'Electrician', 'Plumber', 'Engineers', 'Specialist', 'Other'];

        return view('admin.finance.expenses.index', compact(
            'taskGroups',
            'projects',
            'formProjects',
            'projectOptions',
            'accountOptions',
            'resourceTypes',
            'stats'
        ));
    }

    public function show(Project $project): View
    {
        Gate::authorize('viewAny', ProjectExpense::class);

        $entries = ProjectExpense::query()
            ->with(['project', 'task', 'user'])
            ->where('project_id', $project->id)
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();
        $taskGroups = $this->groupExpenses($entries);

        return view('admin.finance.expenses.show', compact('project', 'taskGroups'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEntry($request, true);
        $project = Project::findOrFail($validated['project_id']);
        Gate::authorize('create', [ProjectExpense::class, $project]);
        $this->validateTaskProject($project, $validated['task_id'] ?? null);

        $data = $this->expenseData($validated);
        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('expenses', 'public');
        }

        $this->financialService->createExpense($project, $data);

        return back()->with('success', 'Expense line added successfully.');
    }

    public function update(Request $request, ProjectExpense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $this->validateEntry($request, false);
        $project = $expense->project;
        abort_unless($project, 404);
        $this->validateTaskProject($project, $validated['task_id'] ?? null);

        $data = $this->expenseData($validated);
        if ($this->isLabor($expense) !== ($validated['kind'] === 'labor')) {
            $data['budget_line_id'] = null;
        }

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = $request->file('receipt')->store('expenses', 'public');
            if ($expense->receipt_path) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
        }

        $this->financialService->updateExpense($expense, $data);

        return back()->with('success', 'Expense line updated successfully.');
    }

    public function destroy(ProjectExpense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);
        $this->financialService->deleteExpense($expense);

        return back()->with('success', 'Expense line deleted successfully.');
    }

    private function validateEntry(Request $request, bool $creating): array
    {
        $resourceTypes = implode(',', ["Helper's", 'Mason', 'Carpenter', 'Steel fixer', 'Welder', 'Electrician', 'Plumber', 'Engineers', 'Specialist', 'Other']);
        $rules = [
            'kind' => 'required|in:labor,materials',
            'task_id' => 'nullable|exists:tasks,id',
            'resource_type' => "nullable|in:{$resourceTypes}",
            'account_type' => 'nullable|string|max:255',
            'custom_type' => 'nullable|string|max:255|required_if:resource_type,Other|required_if:account_type,Other',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'vendor_name' => 'nullable|string|max:255',
            'payment_status' => 'required|in:Paid,Pending,Reimbursement',
            'payment_method' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'receipt' => 'nullable|file|mimes:pdf,png,jpg,jpeg,webp,doc,docx|max:10240',
        ];

        if ($creating) {
            $rules['project_id'] = 'required|exists:projects,id';
        }

        $rules['resource_type'] .= '|required_if:kind,labor';
        $rules['account_type'] .= '|required_if:kind,materials';

        return $request->validate($rules);
    }

    private function groupExpenses(Collection $entries): Collection
    {
        return $entries
            ->groupBy(fn (ProjectExpense $expense) => $expense->project_id . ':' . ($expense->task_id ?? 'unassigned'))
            ->map(function (Collection $lines) {
                $labor = $lines->filter(fn (ProjectExpense $expense) => $this->isLabor($expense));
                $materials = $lines->filter(fn (ProjectExpense $expense) => $this->isMaterials($expense));
                $other = $lines->reject(fn (ProjectExpense $expense) => $this->isLabor($expense) || $this->isMaterials($expense));

                return [
                    'project' => $lines->first()->project,
                    'task' => $lines->first()->task,
                    'labor' => $labor,
                    'materials' => $materials,
                    'other' => $other,
                    'labor_total' => (float) $labor->sum('amount'),
                    'materials_total' => (float) $materials->sum('amount'),
                ];
            })
            ->sortBy(fn (array $group) => strtolower(($group['project']?->name ?? '') . ' ' . ($group['task']?->name ?? '')))
            ->values();
    }

    private function expenseData(array $validated): array
    {
        $isLabor = $validated['kind'] === 'labor';
        $typeValue = $isLabor ? ($validated['resource_type'] ?? '') : ($validated['account_type'] ?? '');

        return [
            'task_id' => $validated['task_id'] ?? null,
            'category' => $isLabor ? 'Labor' : 'Materials',
            'title' => $typeValue === 'Other' ? trim($validated['custom_type'] ?? '') : $typeValue,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'vendor_name' => $validated['vendor_name'] ?? null,
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_method'] ?? null,
            'description' => $validated['description'] ?? null,
        ];
    }

    private function validateTaskProject(Project $project, ?int $taskId): void
    {
        if ($taskId && !$project->tasks()->whereKey($taskId)->exists()) {
            abort(422, 'The selected task does not belong to this project.');
        }
    }

    private function isLabor(ProjectExpense $expense): bool
    {
        return in_array(strtolower($expense->category), ['labor', 'worker salary', 'payroll'], true);
    }

    private function isMaterials(ProjectExpense $expense): bool
    {
        return in_array(strtolower($expense->category), ['materials', 'material'], true);
    }
}
