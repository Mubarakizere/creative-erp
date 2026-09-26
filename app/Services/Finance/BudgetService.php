<?php

namespace App\Services\Finance;

use App\Models\Budget;
use App\Models\GeneralLedger;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    public function getBudgetVsActual(int $budgetId): array
    {
        $budget = Budget::with([
            'project.company', 'project.client', 'project.manager',
            'project.expenses.task',
            'project.materialIssues.task', 'project.materialIssues.items.product',
            'project.budgets', 'project.purchaseOrders.items',
            'fiscalYear', 'lines.category', 'lines.task', 'lines.product', 'lines.chartOfAccount',
        ])->findOrFail($budgetId);

        $actuals = $this->actualSources($budget->project);
        $lineResults = $this->allocateActuals($budget, $actuals['expenses'], $actuals['material_items']);
        if (!$budget->project) {
            foreach ($budget->lines as $line) {
                $lineResults[$line->id] = $this->legacyLedgerActual($budget, $line);
            }
        }
        $budgetTotal = (float) $budget->lines->sum('amount');
        $actualTotal = $budget->project ? $actuals['total'] : (float) array_sum($lineResults);
        $variance = $budgetTotal - $actualTotal;
        $committed = $this->openCommitments($budget->project);
        $payable = (float) $actuals['expenses']->where('payment_status', '!=', 'Paid')->sum('amount');
        $available = $budgetTotal - $actualTotal - $committed;

        $lines = [];
        foreach ($budget->lines as $line) {
            $actual = (float) ($lineResults[$line->id] ?? 0);
            $amount = (float) $line->amount;
            $lines[] = [
                'id' => $line->id,
                'activity' => $line->activity_title,
                'task_id' => $line->task_id,
                'task_code' => $line->task?->task_code,
                'task_name' => $line->task?->name ?? $line->activity_name ?? 'Unassigned activity',
                'cost_type' => $line->cost_type ?: 'other',
                'resource_name' => $line->resource_name ?: $line->product?->name,
                'product_name' => $line->product?->name,
                'category' => $line->category?->name ?? 'Uncategorised',
                'budget_amount' => $amount,
                'actual_amount' => $actual,
                'variance' => $amount - $actual,
                'variance_percentage' => $amount > 0 ? round((($amount - $actual) / $amount) * 100, 2) : 0,
                'status' => $this->getBudgetStatus($amount, $actual),
                'notes' => $line->notes,
            ];
        }

        return [
            'budget' => [
                'id' => $budget->id,
                'name' => $budget->name,
                'status' => $budget->status,
                'total_amount' => $budgetTotal,
                'project_id' => $budget->project_id,
                'project_name' => $budget->project?->name ?? 'General Project',
                'project_code' => $budget->project?->project_code ?? $budget->project?->code,
                'company_name' => $budget->project?->company?->name ?? $budget->company?->name,
                'client_name' => $budget->project?->client?->display_name ?? $budget->project?->client?->name,
                'manager_name' => $budget->project?->manager?->full_name ?? $budget->project?->manager?->name,
            ],
            'summary' => [
                'budget' => $budgetTotal,
                'actual' => $actualTotal,
                'variance' => $variance,
                'variance_percentage' => $budgetTotal > 0 ? round(($variance / $budgetTotal) * 100, 2) : 0,
                'committed' => $committed,
                'payable' => $payable,
                'available' => $available,
                'utilization' => $budgetTotal > 0 ? round(($actualTotal / $budgetTotal) * 100, 1) : 0,
                'labor_actual' => (float) $actuals['expenses']->filter(fn ($expense) => $this->isLabor($expense->category))->sum('amount'),
                'materials_actual' => (float) $actuals['material_items']->sum('total_cost'),
                'other_actual' => (float) $actuals['expenses']->reject(fn ($expense) => $this->isLabor($expense->category))->sum('amount'),
                'unassigned_actual' => max(0, $actualTotal - array_sum($lineResults)),
                'status' => $this->getBudgetStatus($budgetTotal, $actualTotal),
            ],
            'lines' => $lines,
            'task_groups' => $this->taskGroups($budget, $actuals),
        ];
    }

    public function getProjectBudgetAnalysis(Project $project): ?array
    {
        $budget = $project->activeBudget;
        return $budget ? $this->getBudgetVsActual($budget->id) : null;
    }

    private function actualSources(?Project $project): array
    {
        if (!$project) {
            return ['expenses' => collect(), 'material_items' => collect(), 'total' => 0.0];
        }

        $expenses = $project->expenses->values();
        $materialItems = $project->materialIssues
            ->filter(fn ($issue) => mb_strtolower((string) $issue->status) !== 'cancelled')
            ->flatMap(fn ($issue) => $issue->items->map(function ($item) use ($issue) {
                $item->setRelation('issue', $issue);
                return $item;
            }))->values();

        return [
            'expenses' => $expenses,
            'material_items' => $materialItems,
            'total' => (float) $expenses->sum('amount') + (float) $materialItems->sum('total_cost'),
        ];
    }

    private function allocateActuals(Budget $budget, Collection $expenses, Collection $materialItems): array
    {
        $results = $budget->lines->mapWithKeys(fn ($line) => [$line->id => 0.0])->all();
        $unlinkedExpenses = $expenses->reject(fn ($expense) => $expense->budget_line_id && array_key_exists($expense->budget_line_id, $results));

        foreach ($budget->lines as $line) {
            $results[$line->id] += (float) $expenses->where('budget_line_id', $line->id)->sum('amount');
            $type = $line->cost_type ?: 'other';
            if ($type === 'materials') {
                $materialLines = $budget->lines->where('task_id', $line->task_id)->where('cost_type', 'materials');
                if ($line->product_id && $materialLines->where('product_id', $line->product_id)->count() === 1) {
                    $items = $materialItems->filter(fn ($item) =>
                        (int) $item->issue?->task_id === (int) $line->task_id &&
                        (string) $item->product_id === (string) $line->product_id
                    );
                } elseif (!$line->product_id && $line->task_id && $materialLines->count() === 1) {
                    $items = $materialItems->filter(fn ($item) => (int) $item->issue?->task_id === (int) $line->task_id);
                } else {
                    $items = collect();
                }
                $results[$line->id] += (float) $items->sum('total_cost');
                continue;
            }

            if (!$line->task_id) {
                continue;
            }
            $taskLines = $budget->lines->where('task_id', $line->task_id)->where('cost_type', $type);
            $eligible = $unlinkedExpenses->filter(function ($expense) use ($line, $type) {
                if ((int) $expense->task_id !== (int) $line->task_id) {
                    return false;
                }
                return $type === 'labor' ? $this->isLabor($expense->category) : !$this->isLabor($expense->category);
            });

            if ($taskLines->count() === 1) {
                $results[$line->id] += (float) $eligible->sum('amount');
            } elseif ($type === 'labor' && $line->resource_name) {
                $needle = mb_strtolower(trim($line->resource_name));
                $matched = $eligible->filter(fn ($expense) =>
                    str_contains(mb_strtolower((string) $expense->title), $needle) ||
                    str_contains(mb_strtolower((string) $expense->vendor_name), $needle)
                );
                $results[$line->id] += (float) $matched->sum('amount');
            }
        }

        return $results;
    }

    private function taskGroups(Budget $budget, array $actuals): array
    {
        $groups = [];
        foreach ($budget->lines as $line) {
            $key = $line->task_id ? (string) $line->task_id : 'unassigned';
            $groups[$key] ??= [
                'task_id' => $line->task_id,
                'task_code' => $line->task?->task_code,
                'task_name' => $line->task?->name ?? $line->activity_name ?? 'Unassigned activity',
                'labor_budget' => 0.0, 'materials_budget' => 0.0, 'other_budget' => 0.0,
                'labor_actual' => 0.0, 'materials_actual' => 0.0, 'other_actual' => 0.0,
            ];
            $type = in_array($line->cost_type, ['labor', 'materials', 'other'], true) ? $line->cost_type : 'other';
            $groups[$key][$type . '_budget'] += (float) $line->amount;
        }

        foreach ($actuals['expenses'] as $expense) {
            $key = $expense->task_id ? (string) $expense->task_id : 'unassigned';
            $groups[$key] ??= $this->emptyTaskGroup($expense->task_id, $expense->task?->name ?? 'Unassigned costs', $expense->task?->task_code);
            $type = $this->isLabor($expense->category) ? 'labor' : 'other';
            $groups[$key][$type . '_actual'] += (float) $expense->amount;
        }
        foreach ($actuals['material_items'] as $item) {
            $task = $item->issue?->task;
            $key = $task?->id ? (string) $task->id : 'unassigned';
            $groups[$key] ??= $this->emptyTaskGroup($task?->id, $task?->name ?? 'Unassigned costs', $task?->task_code);
            $groups[$key]['materials_actual'] += (float) $item->total_cost;
        }

        return collect($groups)->map(function ($group) {
            $group['budget'] = $group['labor_budget'] + $group['materials_budget'] + $group['other_budget'];
            $group['actual'] = $group['labor_actual'] + $group['materials_actual'] + $group['other_actual'];
            $group['remaining'] = $group['budget'] - $group['actual'];
            return $group;
        })->values()->all();
    }

    private function emptyTaskGroup(?int $taskId, string $name, ?string $code): array
    {
        return [
            'task_id' => $taskId, 'task_code' => $code, 'task_name' => $name,
            'labor_budget' => 0.0, 'materials_budget' => 0.0, 'other_budget' => 0.0,
            'labor_actual' => 0.0, 'materials_actual' => 0.0, 'other_actual' => 0.0,
        ];
    }

    private function openCommitments(?Project $project): float
    {
        if (!$project) {
            return 0.0;
        }

        return (float) $project->purchaseOrders
            ->whereIn('status', ['approved', 'sent', 'partially_received'])
            ->sum(fn ($order) => $order->items->sum(function ($item) {
                $quantity = (float) $item->quantity;
                $remaining = max(0, $quantity - (float) $item->received_quantity);
                return $quantity > 0 ? ((float) $item->total / $quantity) * $remaining : 0;
            }));
    }

    private function legacyLedgerActual(Budget $budget, $line): float
    {
        if (!$line->chart_of_account_id || !$budget->fiscalYear) {
            return 0.0;
        }

        $result = GeneralLedger::where('company_id', $budget->company_id)
            ->whereBetween('date', [$budget->fiscalYear->start_date, $budget->fiscalYear->end_date])
            ->where('chart_of_account_id', $line->chart_of_account_id)
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))
            ->first();

        $isRevenue = strtolower($line->category?->type ?? 'expense') === 'revenue';
        return $isRevenue
            ? (float) (($result->total_credit ?? 0) - ($result->total_debit ?? 0))
            : (float) (($result->total_debit ?? 0) - ($result->total_credit ?? 0));
    }

    private function isLabor(?string $category): bool
    {
        return in_array(mb_strtolower((string) $category), ['worker salary', 'labor', 'payroll'], true);
    }

    private function getBudgetStatus(float $budget, float $actual): string
    {
        if ($budget <= 0) return $actual > 0 ? 'exceeded' : 'no_budget';
        $ratio = $actual / $budget;
        if ($ratio > 1) return 'exceeded';
        if ($ratio > 0.9) return 'warning';
        return 'on_track';
    }
}
