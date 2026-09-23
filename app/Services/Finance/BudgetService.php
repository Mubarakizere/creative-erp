<?php

namespace App\Services\Finance;

use App\Models\Budget;
use App\Models\GeneralLedger;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    public function getBudgetVsActual(int $budgetId): array
    {
        $budget = Budget::with([
            'project.company',
            'project.client',
            'project.manager',
            'fiscalYear',
            'lines.category',
            'lines.task',
            'lines.chartOfAccount',
        ])->findOrFail($budgetId);
        
        $lines = [];
        $totalBudget = 0;
        $totalActual = 0;
        $totalVariance = 0;

        foreach ($budget->lines as $line) {
            $actualAmount = $this->calculateActual($budget, $line);
            
            $variance = $line->amount - $actualAmount;
            $variancePercentage = $line->amount > 0 ? ($variance / $line->amount) * 100 : 0;
            
            $lines[] = [
                'id' => $line->id,
                'activity' => $line->activity_title,
                'task_code' => $line->task?->task_code,
                'task_name' => $line->task?->name ?? $line->activity_name ?? 'General Activity',
                'category' => $line->category->name ?? 'Direct Activity Cost',
                'budget_amount' => (float) $line->amount,
                'actual_amount' => (float) $actualAmount,
                'variance' => (float) $variance,
                'variance_percentage' => round($variancePercentage, 2),
                'status' => $this->getBudgetStatus((float) $line->amount, (float) $actualAmount),
                'notes' => $line->notes,
            ];

            $totalBudget += (float) $line->amount;
            $totalActual += (float) $actualAmount;
            $totalVariance += (float) $variance;
        }

        $totalVariancePercentage = $totalBudget > 0 ? ($totalVariance / $totalBudget) * 100 : 0;

        return [
            'budget' => [
                'id' => $budget->id,
                'name' => $budget->name,
                'status' => $budget->status,
                'total_amount' => (float) $budget->total_amount,
                'project_id' => $budget->project_id,
                'project_name' => $budget->project?->name ?? 'General Project',
                'project_code' => $budget->project?->project_code ?? $budget->project?->code,
                'company_name' => $budget->project?->company?->name ?? $budget->company?->name,
                'client_name' => $budget->project?->client?->display_name ?? $budget->project?->client?->name,
                'manager_name' => $budget->project?->manager?->full_name ?? $budget->project?->manager?->name,
            ],
            'summary' => [
                'budget' => $totalBudget,
                'actual' => $totalActual,
                'variance' => $totalVariance,
                'variance_percentage' => round($totalVariancePercentage, 2),
                'status' => $this->getBudgetStatus((float) $totalBudget, (float) $totalActual),
            ],
            'lines' => $lines
        ];
    }

    private function calculateActual(Budget $budget, $line): float
    {
        // 1. If line has an associated Task/Activity, check tracked material/direct costs
        if ($line->task_id && $line->task) {
            return (float) ($line->task->actual_material_cost ?? 0);
        }

        // 2. Fallback to ChartOfAccount if task not linked but account is present
        if ($line->chart_of_account_id && $budget->fiscalYear) {
            $query = GeneralLedger::where('company_id', $budget->company_id)
                ->whereBetween('date', [$budget->fiscalYear->start_date ?? '1900-01-01', $budget->fiscalYear->end_date ?? '2100-12-31'])
                ->where('chart_of_account_id', $line->chart_of_account_id);
                
            $result = $query->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))->first();
            $isRevenue = strtolower($line->category->type ?? 'expense') === 'revenue';
            
            if ($isRevenue) {
                return (float)(($result->total_credit ?? 0) - ($result->total_debit ?? 0));
            }

            return (float)(($result->total_debit ?? 0) - ($result->total_credit ?? 0));
        }

        return 0.0;
    }

    private function getBudgetStatus(float $budget, float $actual): string
    {
        if ($budget == 0) return 'no_budget';
        $ratio = $actual / $budget;
        
        if ($ratio > 1) return 'exceeded';
        if ($ratio > 0.9) return 'warning';
        return 'on_track';
    }
}
