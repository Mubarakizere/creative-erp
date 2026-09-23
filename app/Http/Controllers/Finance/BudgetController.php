<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Finance\BudgetService;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\BudgetCategory;
use App\Models\FiscalYear;
use App\Models\Project;
use App\Models\Company;

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
        $user = auth()->user();
        $companyId = session('company_id') ?? $user->company_id;

        $baseQuery = Budget::query();
        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $baseQuery->where('company_id', $companyId);
        }

        // Fast summary stats calculation (scoped to accessible company)
        $statBudgets = (clone $baseQuery)->select('id', 'total_amount', 'status')->get();
        $stats = [
            'total_count' => $statBudgets->count(),
            'total_amount' => (float)$statBudgets->sum('total_amount'),
            'active_count' => $statBudgets->where('status', 'active')->count(),
            'approved_count' => $statBudgets->where('status', 'approved')->count(),
            'draft_count' => $statBudgets->where('status', 'draft')->count(),
            'closed_count' => $statBudgets->where('status', 'closed')->count(),
            'total_activities' => BudgetLine::whereIn('budget_id', $statBudgets->pluck('id'))->count(),
        ];

        // Filtered Query
        $query = (clone $baseQuery)
            ->with([
                'project.company',
                'project.manager:id,name',
                'lines.task:id,name,task_code',
                'fiscalYear',
                'company:id,name'
            ]);

        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $query->where('company_id', $companyId);
        } elseif ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fiscal_year_id')) {
            $query->where('fiscal_year_id', $request->fiscal_year_id);
        }

        if ($request->filled('min_amount')) {
            $query->where('total_amount', '>=', (float)$request->min_amount);
        }

        if ($request->filled('max_amount')) {
            $query->where('total_amount', '<=', (float)$request->max_amount);
        }

        // Comprehensive Deep Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('project', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('project_code', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhereHas('company', function ($cq) use ($search) {
                             $cq->where('name', 'like', "%{$search}%");
                         });
                  })
                  ->orWhereHas('lines', function ($lq) use ($search) {
                      $lq->where('activity_name', 'like', "%{$search}%")
                         ->orWhereHas('task', function ($tq) use ($search) {
                             $tq->where('name', 'like', "%{$search}%")
                                ->orWhere('task_code', 'like', "%{$search}%");
                         });
                  });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'latest');
        match($sortBy) {
            'oldest' => $query->oldest(),
            'amount_desc' => $query->orderByDesc('total_amount'),
            'amount_asc' => $query->orderBy('total_amount'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };

        // CSV Export capability
        if ($request->query('export') === 'csv') {
            $exportBudgets = $query->get();
            $filename = 'project_budgets_' . now()->format('Y-m-d_His') . '.csv';

            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, [
                'Budget Title',
                'Project Name',
                'Project Code',
                'Company',
                'Fiscal Year',
                'Status',
                'Activities Count',
                'Total Allocated Budget',
                'Project Actual Cost',
                'Created At',
            ]);

            foreach ($exportBudgets as $b) {
                fputcsv($handle, [
                    $b->name,
                    $b->project?->name ?? 'General',
                    $b->project?->project_code ?? $b->project?->code ?? 'N/A',
                    $b->project?->company?->name ?? $b->company?->name ?? 'N/A',
                    $b->fiscalYear?->name ?? 'N/A',
                    ucfirst($b->status),
                    $b->lines->count(),
                    $b->total_amount,
                    $b->project?->actual_cost ?? 0,
                    $b->created_at?->format('Y-m-d H:i') ?? '',
                ]);
            }
            rewind($handle);
            $csv = stream_get_contents($handle);
            fclose($handle);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        $budgets = $query->paginate(15)->withQueryString();

        // Accessible projects for filter
        $projectsQuery = Project::query();
        if ($companyId && !$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $projectsQuery->where('company_id', $companyId);
        }
        $projects = $projectsQuery->where('status', '!=', 'Closed')->orderBy('name')->get();
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        $fiscalYears = FiscalYear::orderByDesc('start_date')->get();

        return view('admin.finance.budgets.index', compact('budgets', 'projects', 'companies', 'fiscalYears', 'stats'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Budget::class);
        $user = auth()->user();
        $companyId = session('company_id') ?? $user->company_id;

        $projectsQuery = Project::query()->with([
            'company:id,name',
            'tasks' => function ($t) {
                $t->select('id', 'project_id', 'task_code', 'name', 'status', 'actual_material_cost');
            }
        ]);

        if (!$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $projectsQuery->accessibleBy($user);
        }

        $projects = $projectsQuery->where('status', '!=', 'Closed')->orderBy('name')->get();
        if ($projects->isEmpty() && ($user->hasRole('Super Admin') || $user->hasRole('CEO'))) {
            $projects = Project::with(['company:id,name', 'tasks'])->orderBy('name')->get();
        }

        $selectedProjectId = $request->query('project_id');

        $projectsData = $projects->mapWithKeys(function ($p) {
            return [
                $p->id => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'code' => $p->project_code ?? $p->code,
                    'company_id' => $p->company_id,
                    'company_name' => $p->company?->name ?? 'General',
                    'tasks' => $p->tasks->map(fn($t) => [
                        'id' => $t->id,
                        'task_code' => $t->task_code,
                        'name' => $t->name,
                        'display_label' => ($t->task_code ? '[' . $t->task_code . '] ' : '') . $t->name,
                    ])->values()->all(),
                ]
            ];
        });

        $categories = BudgetCategory::orderBy('name')->get();
        $fiscalYears = FiscalYear::where('is_closed', false)->orderBy('name')->get();

        return view('admin.finance.budgets.create', compact(
            'projects',
            'projectsData',
            'categories',
            'fiscalYears',
            'selectedProjectId'
        ));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Budget::class);
        $user = auth()->user();

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fiscal_year_id' => 'nullable|exists:fiscal_years,id',
            'status' => 'nullable|string|in:draft,approved,active,closed',
            'lines' => 'required|array|min:1',
            'lines.*.task_id' => 'nullable|exists:tasks,id',
            'lines.*.activity_name' => 'nullable|string|max:255',
            'lines.*.budget_category_id' => 'nullable|exists:budget_categories,id',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.notes' => 'nullable|string',
        ]);

        $project = Project::findOrFail($request->project_id);
        if (!$project->isAssignedTo($user)) {
            abort(403, 'Unauthorized access to project.');
        }

        $totalAmount = collect($request->lines)->sum(function ($line) {
            return (float) ($line['amount'] ?? 0);
        });

        $budget = Budget::create([
            'company_id' => $project->company_id ?? $user->company_id ?? 1,
            'project_id' => $project->id,
            'name' => $request->name,
            'description' => $request->description,
            'fiscal_year_id' => $request->fiscal_year_id,
            'total_amount' => $totalAmount,
            'status' => $request->input('status', 'draft'),
            'created_by' => $user->id,
        ]);

        foreach ($request->lines as $line) {
            BudgetLine::create([
                'budget_id' => $budget->id,
                'project_id' => $project->id,
                'task_id' => $line['task_id'] ?? null,
                'activity_name' => $line['activity_name'] ?? null,
                'budget_category_id' => $line['budget_category_id'] ?? null,
                'amount' => $line['amount'],
                'notes' => $line['notes'] ?? null,
                'created_by' => $user->id,
            ]);
        }

        // Sync with project's estimated_budget
        if ($project->estimated_budget <= 0 || in_array($budget->status, ['active', 'approved'])) {
            $project->update(['estimated_budget' => $totalAmount]);
        }

        $this->logActivity('budget_created', $budget, ['amount' => $totalAmount, 'project_id' => $project->id]);

        return redirect()->route('admin.finance.budgets.show', $budget)
            ->with('success', 'Project Activity Budget created successfully.');
    }

    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);
        $budget->load(['project.company', 'project.manager', 'project.client', 'lines.task', 'lines.category', 'fiscalYear']);
        $analysis = $this->budgetService->getBudgetVsActual($budget->id);

        return view('admin.finance.budgets.show', compact('budget', 'analysis'));
    }

    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        $user = auth()->user();

        $budget->load(['project.tasks', 'lines.task', 'lines.category']);

        $projectsQuery = Project::query()->with([
            'company:id,name',
            'tasks' => function ($t) {
                $t->select('id', 'project_id', 'task_code', 'name', 'status', 'actual_material_cost');
            }
        ]);

        if (!$user->hasRole('Super Admin') && !$user->hasRole('CEO')) {
            $projectsQuery->accessibleBy($user);
        }

        $projects = $projectsQuery->where('status', '!=', 'Closed')->orderBy('name')->get();
        if ($projects->isEmpty() && ($user->hasRole('Super Admin') || $user->hasRole('CEO'))) {
            $projects = Project::with(['company:id,name', 'tasks'])->orderBy('name')->get();
        }

        $projectsData = $projects->mapWithKeys(function ($p) {
            return [
                $p->id => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'code' => $p->project_code ?? $p->code,
                    'company_id' => $p->company_id,
                    'company_name' => $p->company?->name ?? 'General',
                    'tasks' => $p->tasks->map(fn($t) => [
                        'id' => $t->id,
                        'task_code' => $t->task_code,
                        'name' => $t->name,
                        'display_label' => ($t->task_code ? '[' . $t->task_code . '] ' : '') . $t->name,
                    ])->values()->all(),
                ]
            ];
        });

        $categories = BudgetCategory::orderBy('name')->get();
        $fiscalYears = FiscalYear::where('is_closed', false)->orderBy('name')->get();

        $initialLines = $budget->lines->map(function ($l) {
            return [
                'task_id' => $l->task_id ?? '',
                'activity_name' => $l->activity_name ?? '',
                'budget_category_id' => $l->budget_category_id ?? '',
                'amount' => (float)$l->amount,
                'notes' => $l->notes ?? '',
            ];
        })->values()->all();

        $projectTasks = $budget->project ? $budget->project->tasks->map(function ($t) {
            return [
                'id' => $t->id,
                'task_code' => $t->task_code,
                'name' => $t->name,
                'display_label' => ($t->task_code ? '[' . $t->task_code . '] ' : '') . $t->name,
            ];
        })->values()->all() : [];

        return view('admin.finance.budgets.edit', compact(
            'budget',
            'projects',
            'projectsData',
            'categories',
            'fiscalYears',
            'initialLines',
            'projectTasks'
        ));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|string|in:draft,approved,active,closed',
            'fiscal_year_id' => 'nullable|exists:fiscal_years,id',
            'lines' => 'sometimes|required|array|min:1',
            'lines.*.task_id' => 'nullable|exists:tasks,id',
            'lines.*.activity_name' => 'nullable|string|max:255',
            'lines.*.budget_category_id' => 'nullable|exists:budget_categories,id',
            'lines.*.amount' => 'required|numeric|min:0',
            'lines.*.notes' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $request->input('name', $budget->name),
            'description' => $request->input('description', $budget->description),
            'status' => $request->input('status', $budget->status),
            'fiscal_year_id' => $request->input('fiscal_year_id', $budget->fiscal_year_id),
            'updated_by' => $user->id,
        ];

        if ($request->has('lines')) {
            $totalAmount = collect($request->lines)->sum(function ($line) {
                return (float) ($line['amount'] ?? 0);
            });
            $updateData['total_amount'] = $totalAmount;

            $budget->lines()->delete();
            foreach ($request->lines as $line) {
                BudgetLine::create([
                    'budget_id' => $budget->id,
                    'project_id' => $budget->project_id,
                    'task_id' => $line['task_id'] ?? null,
                    'activity_name' => $line['activity_name'] ?? null,
                    'budget_category_id' => $line['budget_category_id'] ?? null,
                    'amount' => $line['amount'],
                    'notes' => $line['notes'] ?? null,
                    'created_by' => $user->id,
                ]);
            }

            // Sync with project's estimated budget if active or approved
            if ($budget->project && in_array($updateData['status'], ['active', 'approved'])) {
                $budget->project->update(['estimated_budget' => $totalAmount]);
            }
        }

        $budget->update($updateData);

        $this->logActivity('budget_updated', $budget, ['status' => $budget->status, 'name' => $budget->name]);

        return redirect()->route('admin.finance.budgets.show', $budget)
            ->with('success', 'Project Activity Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        $budget->lines()->delete();
        $budget->delete();

        return redirect()->route('admin.finance.budgets.index')
            ->with('success', 'Budget deleted successfully.');
    }
}
