<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectExpense;
use App\Models\ProjectMaterialIssueItem;
use App\Models\Invoice;
use App\Models\TimeEntry;
use Illuminate\Support\Facades\DB;

class ProjectFinancialService
{
    /**
     * Get a comprehensive financial & Profit/Loss breakdown for a project.
     */
    public function getProjectFinancialSummary(Project $project): array
    {
        // 1. Revenue: Total invoiced amount for non-cancelled invoices linked to project
        $invoicedRevenue = Invoice::where('project_id', $project->id)
            ->whereNotIn('status', ['Cancelled', 'Voided'])
            ->sum('total_amount');

        // Fallback to project actual_budget if no invoices are logged yet
        $revenue = $invoicedRevenue > 0 ? (float) $invoicedRevenue : (float) ($project->actual_budget ?? $project->estimated_budget ?? 0);

        // 2. Direct Operational Expenses (excluding Worker Salary/Labor/Payroll category)
        $directExpenses = (float) ProjectExpense::where('project_id', $project->id)
            ->directExpenses()
            ->sum('amount');

        // 3. Worker Salary / Direct Labor Expenses
        $directLaborExpenses = (float) ProjectExpense::where('project_id', $project->id)
            ->labor()
            ->sum('amount');

        // 4. Timesheet Logged Labor Cost (duration_minutes / 60 * hourly_rate)
        $timeEntries = TimeEntry::where('project_id', $project->id)
            ->where('status', 'completed')
            ->get();

        $timeEntriesLaborCost = 0.0;
        foreach ($timeEntries as $entry) {
            $hours = ($entry->duration_minutes ?? 0) / 60;
            $rate = (float) ($entry->hourly_rate ?? 0);
            if ($rate <= 0 && $entry->user_id) {
                // Fallback to member's project hourly rate if not explicitly set on time entry
                $memberRate = DB::table('project_members')
                    ->where('project_id', $project->id)
                    ->where('user_id', $entry->user_id)
                    ->value('hourly_rate');
                $rate = (float) ($memberRate ?? 0);
            }
            $timeEntriesLaborCost += ($hours * $rate);
        }

        $totalLaborCost = $directLaborExpenses + $timeEntriesLaborCost;

        // 5. Material Cost (Issued materials for project)
        $materialCost = (float) ProjectMaterialIssueItem::whereHas('issue', function ($q) use ($project) {
            $q->where('project_id', $project->id)->where('status', '!=', 'Cancelled');
        })->sum('total_cost');

        // Total Expenses & Costs
        $totalCosts = $directExpenses + $totalLaborCost + $materialCost;

        // Net Profit / Loss
        $netProfit = $revenue - $totalCosts;
        $profitMargin = $revenue > 0 ? round(($netProfit / $revenue) * 100, 2) : 0;

        return [
            'revenue' => $revenue,
            'invoiced_revenue' => (float) $invoicedRevenue,
            'budget_revenue' => (float) ($project->actual_budget ?? $project->estimated_budget ?? 0),
            'direct_expenses' => $directExpenses,
            'direct_labor_expenses' => $directLaborExpenses,
            'time_entries_labor_cost' => round($timeEntriesLaborCost, 2),
            'total_labor_cost' => round($totalLaborCost, 2),
            'material_cost' => $materialCost,
            'total_costs' => round($totalCosts, 2),
            'net_profit' => round($netProfit, 2),
            'profit_margin' => $profitMargin,
            'is_profitable' => $netProfit >= 0,
            'currency' => $project->currency ?? 'USD',
        ];
    }

    /**
     * Create a new project expense / salary entry.
     */
    public function createExpense(Project $project, array $data): ProjectExpense
    {
        $data['project_id'] = $project->id;
        $data['company_id'] = $project->company_id;
        $data['branch_id'] = $project->branch_id;
        $data['currency'] = $project->currency ?? 'USD';
        $data['created_by'] = auth()->id();

        $expense = ProjectExpense::create($data);

        $this->syncProjectActualCost($project);

        return $expense;
    }

    /**
     * Update an existing project expense.
     */
    public function updateExpense(ProjectExpense $expense, array $data): ProjectExpense
    {
        $data['updated_by'] = auth()->id();
        $expense->update($data);

        if ($expense->project) {
            $this->syncProjectActualCost($expense->project);
        }

        return $expense->fresh();
    }

    /**
     * Delete a project expense.
     */
    public function deleteExpense(ProjectExpense $expense): bool
    {
        $project = $expense->project;
        $deleted = (bool) $expense->delete();

        if ($project) {
            $this->syncProjectActualCost($project);
        }

        return $deleted;
    }

    /**
     * Sync and update the project's actual_cost column.
     */
    public function syncProjectActualCost(Project $project): float
    {
        $summary = $this->getProjectFinancialSummary($project);
        $totalCost = $summary['total_costs'];

        $project->update([
            'actual_cost' => $totalCost,
            'updated_by' => auth()->id(),
        ]);

        return $totalCost;
    }
}
