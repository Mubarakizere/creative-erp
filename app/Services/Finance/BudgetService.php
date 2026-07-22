<?php

namespace App\Services\Finance;

use App\Models\Budget;
use App\Models\GeneralLedger;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BudgetService
{
    public function getBudgetVsActual(int $budgetId): array
    {
        $budget = Budget::with(['lines.category', 'lines.chartOfAccount', 'lines.department', 'lines.project'])->findOrFail($budgetId);
        
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
                'category' => $line->category->name ?? 'Uncategorized',
                'account' => $line->chartOfAccount ? $line->chartOfAccount->code . ' - ' . $line->chartOfAccount->name : null,
                'department' => $line->department->name ?? null,
                'project' => $line->project->name ?? null,
                'budget_amount' => $line->amount,
                'actual_amount' => $actualAmount,
                'variance' => $variance,
                'variance_percentage' => round($variancePercentage, 2),
                'status' => $this->getBudgetStatus($line->amount, $actualAmount)
            ];

            $totalBudget += $line->amount;
            $totalActual += $actualAmount;
            $totalVariance += $variance;
        }

        $totalVariancePercentage = $totalBudget > 0 ? ($totalVariance / $totalBudget) * 100 : 0;

        return [
            'budget' => [
                'id' => $budget->id,
                'name' => $budget->name,
                'status' => $budget->status,
                'total_amount' => $budget->total_amount,
            ],
            'summary' => [
                'budget' => $totalBudget,
                'actual' => $totalActual,
                'variance' => $totalVariance,
                'variance_percentage' => round($totalVariancePercentage, 2),
                'status' => $this->getBudgetStatus($totalBudget, $totalActual)
            ],
            'lines' => $lines
        ];
    }

    private function calculateActual(Budget $budget, $line): float
    {
        $query = GeneralLedger::where('company_id', $budget->company_id)
            ->whereBetween('date', [$budget->fiscalYear->start_date ?? '1900-01-01', $budget->fiscalYear->end_date ?? '2100-12-31']);
            
        if ($line->chart_of_account_id) {
            $query->where('chart_of_account_id', $line->chart_of_account_id);
        }

        // Support future filtering by department or project when GeneralLedger supports it.
        // For now, it queries purely by Account.
        
        $result = $query->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'))->first();
        
        // Expenses are usually debit balances. Revenues are credit balances.
        // We will assume Budget lines are mostly Expense lines. 
        // If it's a revenue category, it's credit - debit.
        $isRevenue = strtolower($line->category->type ?? 'expense') === 'revenue';
        
        if ($isRevenue) {
            return ($result->total_credit ?? 0) - ($result->total_debit ?? 0);
        }

        return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
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
