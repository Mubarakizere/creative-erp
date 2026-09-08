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
}
