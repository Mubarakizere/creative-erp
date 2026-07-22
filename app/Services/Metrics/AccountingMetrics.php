<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\JournalEntry;
use App\Models\GeneralLedger;
use App\Models\FiscalYear;
use App\Models\Journal;
use App\Models\AccountingPeriod;

class AccountingMetrics implements MetricProvider
{
    public function cards(array $filters = []): array
    {
        $companyId = auth()->user()?->company_id ?? 1;

        // Simplify for metrics: get totals for active fiscal year
        $activeYear = FiscalYear::where('company_id', $companyId)->where('is_closed', false)->first();

        // Very basic approximations. Real accounting metrics require parsing account types.
        $totalAssets = GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Asset%');
            })->sum('debit') - GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Asset%');
            })->sum('credit');

        $totalLiabilities = GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Liability%');
            })->sum('credit') - GeneralLedger::where('company_id', $companyId)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Liability%');
            })->sum('debit');

        $revenue = GeneralLedger::where('company_id', $companyId)
            ->where('fiscal_year_id', $activeYear?->id)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Revenue%')->orWhere('name', 'like', '%Sales%');
            })->sum('credit') - GeneralLedger::where('company_id', $companyId)
            ->where('fiscal_year_id', $activeYear?->id)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Revenue%')->orWhere('name', 'like', '%Sales%');
            })->sum('debit');

        $expenses = GeneralLedger::where('company_id', $companyId)
            ->where('fiscal_year_id', $activeYear?->id)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Expense%');
            })->sum('debit') - GeneralLedger::where('company_id', $companyId)
            ->where('fiscal_year_id', $activeYear?->id)
            ->whereHas('chartOfAccount.accountType', function($q) {
                $q->where('name', 'like', '%Expense%');
            })->sum('credit');

        return [
            'Total Assets' => [
                'value' => number_format(max(0, $totalAssets), 2),
                'icon' => 'building-library',
                'color' => 'indigo',
                'trend' => '+0%',
            ],
            'Total Liabilities' => [
                'value' => number_format(max(0, $totalLiabilities), 2),
                'icon' => 'banknotes',
                'color' => 'red',
                'trend' => '+0%',
            ],
            'Revenue' => [
                'value' => number_format(max(0, $revenue), 2),
                'icon' => 'arrow-trending-up',
                'color' => 'green',
                'trend' => '+0%',
            ],
            'Expenses' => [
                'value' => number_format(max(0, $expenses), 2),
                'icon' => 'arrow-trending-down',
                'color' => 'orange',
                'trend' => '+0%',
            ],
            'Net Income' => [
                'value' => number_format($revenue - $expenses, 2),
                'icon' => 'currency-dollar',
                'color' => 'emerald',
                'trend' => '+0%',
            ],
            'Journal Count' => [
                'value' => Journal::where('company_id', $companyId)->count(),
                'icon' => 'document-text',
                'color' => 'blue',
                'trend' => '+0%',
            ],
            'Ledger Activity' => [
                'value' => GeneralLedger::where('company_id', $companyId)->count(),
                'icon' => 'list-bullet',
                'color' => 'purple',
                'trend' => '+0%',
            ],
            'Open Fiscal Periods' => [
                'value' => AccountingPeriod::where('company_id', $companyId)->where('is_locked', false)->count(),
                'icon' => 'calendar-days',
                'color' => 'teal',
                'trend' => '+0%',
            ],
        ];
    }

    public function widgets(array $filters = []): array
    {
        return [];
    }

    public function reports(array $filters = []): array
    {
        return [];
    }
}
