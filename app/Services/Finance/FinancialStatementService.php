<?php

namespace App\Services\Finance;

use App\Models\GeneralLedger;
use App\Models\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialStatementService
{
    public function generateProfitAndLoss(int $companyId, ?string $startDate = null, ?string $endDate = null, array $filters = []): array
    {
        $query = GeneralLedger::select(
            'chart_of_account_id',
            DB::raw('SUM(debit) as total_debit'),
            DB::raw('SUM(credit) as total_credit')
        )->where('company_id', $companyId);

        if ($startDate) $query->whereDate('date', '>=', $startDate);
        if ($endDate) $query->whereDate('date', '<=', $endDate);

        $this->applyFilters($query, $filters);

        $ledgers = $query->groupBy('chart_of_account_id')->with('chartOfAccount.accountType')->get();

        $revenue = 0;
        $costOfSales = 0;
        $operatingExpenses = 0;
        $otherIncome = 0;
        $otherExpenses = 0;

        $revenueAccounts = [];
        $costOfSalesAccounts = [];
        $operatingExpenseAccounts = [];
        $otherIncomeAccounts = [];
        $otherExpenseAccounts = [];

        foreach ($ledgers as $ledger) {
            $account = $ledger->chartOfAccount;
            if (!$account || !$account->accountType) continue;

            $type = strtolower($account->accountType->name);
            $category = strtolower($account->accountType->category ?? '');

            // Revenue is credit balance, Expenses are debit balance
            if (str_contains($type, 'cost of sales') || str_contains($type, 'cogs')) {
                $balance = $ledger->total_debit - $ledger->total_credit;
                $costOfSales += $balance;
                $costOfSalesAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($type, 'revenue') || str_contains($type, 'sales') || str_contains($category, 'revenue')) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $revenue += $balance;
                $revenueAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($type, 'expense') || str_contains($category, 'expense')) {
                $balance = $ledger->total_debit - $ledger->total_credit;
                if (str_contains($type, 'other')) {
                    $otherExpenses += $balance;
                    $otherExpenseAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
                } else {
                    $operatingExpenses += $balance;
                    $operatingExpenseAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
                }
            } elseif (str_contains($type, 'other income')) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $otherIncome += $balance;
                $otherIncomeAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            }
        }

        $grossProfit = $revenue - $costOfSales;
        $operatingIncome = $grossProfit - $operatingExpenses;
        $netProfit = $operatingIncome + $otherIncome - $otherExpenses;

        return [
            'revenue' => [
                'total' => $revenue,
                'accounts' => $revenueAccounts
            ],
            'cost_of_sales' => [
                'total' => $costOfSales,
                'accounts' => $costOfSalesAccounts
            ],
            'gross_profit' => $grossProfit,
            'operating_expenses' => [
                'total' => $operatingExpenses,
                'accounts' => $operatingExpenseAccounts
            ],
            'operating_income' => $operatingIncome,
            'other_income' => [
                'total' => $otherIncome,
                'accounts' => $otherIncomeAccounts
            ],
            'other_expenses' => [
                'total' => $otherExpenses,
                'accounts' => $otherExpenseAccounts
            ],
            'net_profit' => $netProfit,
        ];
    }

    public function generateBalanceSheet(int $companyId, ?string $asOfDate = null, array $filters = []): array
    {
        $query = GeneralLedger::select(
            'chart_of_account_id',
            DB::raw('SUM(debit) as total_debit'),
            DB::raw('SUM(credit) as total_credit')
        )->where('company_id', $companyId);

        if ($asOfDate) {
            $query->whereDate('date', '<=', $asOfDate);
        }

        $this->applyFilters($query, $filters);

        $ledgers = $query->groupBy('chart_of_account_id')->with('chartOfAccount.accountType')->get();

        $currentAssets = 0;
        $fixedAssets = 0;
        $currentLiabilities = 0;
        $longTermLiabilities = 0;
        $equity = 0;
        
        $currentAssetAccounts = [];
        $fixedAssetAccounts = [];
        $currentLiabilityAccounts = [];
        $longTermLiabilityAccounts = [];
        $equityAccounts = [];

        // For Retained Earnings calculation (Profit/Loss up to the asOfDate)
        $retainedEarnings = 0;

        foreach ($ledgers as $ledger) {
            $account = $ledger->chartOfAccount;
            if (!$account || !$account->accountType) continue;

            $type = strtolower($account->accountType->name);
            $category = strtolower($account->accountType->category ?? '');

            // Asset balances are debit, Liabilities & Equity are credit
            if (str_contains($type, 'current asset') || str_contains($type, 'bank') || str_contains($type, 'cash') || str_contains($type, 'accounts receivable') || ($category === 'asset' && !str_contains($type, 'fixed'))) {
                $balance = $ledger->total_debit - $ledger->total_credit;
                $currentAssets += $balance;
                $currentAssetAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($type, 'fixed asset') || str_contains($category, 'asset')) {
                $balance = $ledger->total_debit - $ledger->total_credit;
                $fixedAssets += $balance;
                $fixedAssetAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($type, 'current liability') || str_contains($type, 'accounts payable') || str_contains($type, 'tax') || ($category === 'liability' && !str_contains($type, 'long'))) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $currentLiabilities += $balance;
                $currentLiabilityAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($type, 'long term liability') || str_contains($type, 'non-current liability') || str_contains($category, 'liability')) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $longTermLiabilities += $balance;
                $longTermLiabilityAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($category, 'equity') || str_contains($type, 'equity')) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $equity += $balance;
                $equityAccounts[] = ['name' => $account->name, 'code' => $account->code, 'balance' => $balance];
            } elseif (str_contains($category, 'revenue') || str_contains($type, 'revenue') || str_contains($type, 'sales')) {
                $balance = $ledger->total_credit - $ledger->total_debit;
                $retainedEarnings += $balance;
            } elseif (str_contains($category, 'expense') || str_contains($type, 'expense') || str_contains($type, 'cost of sales')) {
                $balance = $ledger->total_debit - $ledger->total_credit;
                $retainedEarnings -= $balance;
            }
        }
        
        $totalAssets = $currentAssets + $fixedAssets;
        $totalLiabilities = $currentLiabilities + $longTermLiabilities;
        $totalEquity = $equity + $retainedEarnings;

        return [
            'assets' => [
                'current' => [
                    'total' => $currentAssets,
                    'accounts' => $currentAssetAccounts
                ],
                'fixed' => [
                    'total' => $fixedAssets,
                    'accounts' => $fixedAssetAccounts
                ],
                'total' => $totalAssets
            ],
            'liabilities' => [
                'current' => [
                    'total' => $currentLiabilities,
                    'accounts' => $currentLiabilityAccounts
                ],
                'long_term' => [
                    'total' => $longTermLiabilities,
                    'accounts' => $longTermLiabilityAccounts
                ],
                'total' => $totalLiabilities
            ],
            'equity' => [
                'base' => [
                    'total' => $equity,
                    'accounts' => $equityAccounts
                ],
                'retained_earnings' => $retainedEarnings,
                'total' => $totalEquity
            ],
            'is_balanced' => round($totalAssets, 2) === round($totalLiabilities + $totalEquity, 2)
        ];
    }

    public function generateCashFlowStatement(int $companyId, ?string $startDate = null, ?string $endDate = null, array $filters = []): array
    {
        // Calculate operating, investing, and financing cash flows.
        // For simplicity, we will look at GeneralLedger entries where the offset account is Cash/Bank.
        // But since double entry is complex, we will approximate based on Account Type changes for the period.
        
        // This requires getting balances at start vs end of period.
        $startBalances = $this->generateBalanceSheet($companyId, $startDate ? Carbon::parse($startDate)->subDay()->toDateString() : null, $filters);
        $endBalances = $this->generateBalanceSheet($companyId, $endDate, $filters);
        $pnl = $this->generateProfitAndLoss($companyId, $startDate, $endDate, $filters);
        
        $netIncome = $pnl['net_profit'];

        // Operating Activities = Net Income + Depreciation (ignored here for simplicity) + Changes in Working Capital
        $changeInCurrentAssets = ($endBalances['assets']['current']['total'] - $startBalances['assets']['current']['total']);
        // Cash is a current asset, we should exclude cash from "change in current assets"
        $startCash = $this->getCashBalance($companyId, $startDate ? Carbon::parse($startDate)->subDay()->toDateString() : null, $filters);
        $endCash = $this->getCashBalance($companyId, $endDate, $filters);
        
        $changeInOperatingAssets = $changeInCurrentAssets - ($endCash - $startCash);
        
        $changeInCurrentLiabilities = ($endBalances['liabilities']['current']['total'] - $startBalances['liabilities']['current']['total']);
        
        $operatingCashFlow = $netIncome - $changeInOperatingAssets + $changeInCurrentLiabilities;
        
        // Investing Activities = Changes in Fixed Assets
        $investingCashFlow = -($endBalances['assets']['fixed']['total'] - $startBalances['assets']['fixed']['total']);
        
        // Financing Activities = Changes in Long Term Liabilities & Equity (excluding retained earnings)
        $changeInLongTermLiab = ($endBalances['liabilities']['long_term']['total'] - $startBalances['liabilities']['long_term']['total']);
        $changeInEquity = ($endBalances['equity']['base']['total'] - $startBalances['equity']['base']['total']);
        
        $financingCashFlow = $changeInLongTermLiab + $changeInEquity;
        
        $netCashFlow = $operatingCashFlow + $investingCashFlow + $financingCashFlow;

        return [
            'operating_activities' => [
                'net_income' => $netIncome,
                'changes_in_working_capital' => -$changeInOperatingAssets + $changeInCurrentLiabilities,
                'total' => $operatingCashFlow
            ],
            'investing_activities' => [
                'total' => $investingCashFlow
            ],
            'financing_activities' => [
                'total' => $financingCashFlow
            ],
            'opening_cash' => $startCash,
            'closing_cash' => $endCash,
            'net_cash_flow' => $netCashFlow,
            'is_reconciled' => round($startCash + $netCashFlow, 2) === round($endCash, 2)
        ];
    }
    
    private function getCashBalance(int $companyId, ?string $asOfDate = null, array $filters = []): float
    {
        $query = GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount', function($q) {
                $q->where(function($q2) {
                    $q2->where('name', 'like', '%bank%')
                      ->orWhere('name', 'like', '%cash%')
                      ->orWhereHas('accountType', function($q3) {
                          $q3->where('name', 'like', '%bank%')->orWhere('name', 'like', '%cash%');
                      });
                })->whereHas('accountType', function($q4) {
                    $q4->where('category', 'Asset');
                });
            })
            ->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(credit) as total_credit'));
            
        if ($asOfDate) {
            $query->whereDate('date', '<=', $asOfDate);
        }
        
        $this->applyFilters($query, $filters);

        $result = $query->first();
        return ($result->total_debit ?? 0) - ($result->total_credit ?? 0);
    }

    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }
        if (!empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
        if (!empty($filters['currency_code'])) {
            $query->where('currency_code', $filters['currency_code']);
        }
        if (!empty($filters['accounting_period_id'])) {
            $query->where('accounting_period_id', $filters['accounting_period_id']);
        }
        if (!empty($filters['fiscal_year_id'])) {
            $query->where('fiscal_year_id', $filters['fiscal_year_id']);
        }
    }
}
