<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectExpenseRequest;
use App\Http\Requests\Admin\UpdateProjectExpenseRequest;
use App\Models\Project;
use App\Models\ProjectExpense;
use App\Services\ProjectFinancialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ProjectExpenseController extends Controller
{
    public function __construct(
        protected ProjectFinancialService $financialService
    ) {}

    /**
     * Store a newly created expense for a project.
     */
    public function store(StoreProjectExpenseRequest $request, Project $project): RedirectResponse
    {
        Gate::authorize('create', [ProjectExpense::class, $project]);

        $validated = $request->validated();
        $this->validateBudgetAssignment($project, $validated);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('expenses', 'public');
            $validated['receipt_path'] = $path;
        }
        unset($validated['receipt']);

        $expense = $this->financialService->createExpense($project, $validated);

        return redirect()->back()->with('success', "Project expense '{$expense->title}' added successfully.");
    }

    /**
     * Update the specified expense.
     */
    public function update(UpdateProjectExpenseRequest $request, ProjectExpense $expense): RedirectResponse
    {
        Gate::authorize('update', $expense);

        $validated = $request->validated();
        $this->validateBudgetAssignment($expense->project, $validated);

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path && Storage::disk('public')->exists($expense->receipt_path)) {
                Storage::disk('public')->delete($expense->receipt_path);
            }
            $path = $request->file('receipt')->store('expenses', 'public');
            $validated['receipt_path'] = $path;
        }
        unset($validated['receipt']);

        $this->financialService->updateExpense($expense, $validated);

        return redirect()->back()->with('success', "Project expense '{$expense->title}' updated successfully.");
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(ProjectExpense $expense): RedirectResponse
    {
        Gate::authorize('delete', $expense);

        $title = $expense->title;
        $this->financialService->deleteExpense($expense);

        return redirect()->back()->with('success', "Expense '{$title}' deleted successfully.");
    }

    private function validateBudgetAssignment(Project $project, array &$data): void
    {
        if (!empty($data['task_id']) && !$project->tasks()->whereKey($data['task_id'])->exists()) {
            abort(422, 'The selected task does not belong to this project.');
        }

        if (empty($data['budget_line_id'])) {
            return;
        }

        $activeBudgetId = $project->activeBudget()->value('id');
        $line = \App\Models\BudgetLine::query()
            ->whereKey($data['budget_line_id'])
            ->where('project_id', $project->id)
            ->where('budget_id', $activeBudgetId)
            ->firstOrFail();

        if (!empty($data['task_id']) && $line->task_id && (int) $data['task_id'] !== (int) $line->task_id) {
            abort(422, 'The selected budget line belongs to a different task.');
        }
        if (($line->cost_type ?: 'other') === 'materials') {
            abort(422, 'Material actuals are tracked from project material issues. Select a labor or other cost line for this expense.');
        }
        $isLaborExpense = in_array(strtolower($data['category'] ?? ''), ['worker salary', 'labor', 'payroll'], true);
        if ($isLaborExpense !== (($line->cost_type ?: 'other') === 'labor')) {
            abort(422, 'Choose a budget line with a cost type that matches this expense category.');
        }
        $data['task_id'] = $line->task_id ?? ($data['task_id'] ?? null);
    }
}
