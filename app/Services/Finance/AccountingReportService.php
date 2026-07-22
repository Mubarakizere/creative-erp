<?php

namespace App\Services\Finance;

use App\Models\GeneralLedger;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingReportService
{
    public function generateTrialBalance(int $companyId, ?int $fiscalYearId = null): Collection
    {
        // A simple trial balance sums all debits and credits per account for a given fiscal year or company.
        $query = GeneralLedger::select(
                'chart_of_account_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit')
            )
            ->where('company_id', $companyId);
            
        if ($fiscalYearId) {
            $query->where('fiscal_year_id', $fiscalYearId);
        }

        $ledgers = $query->groupBy('chart_of_account_id')->with('chartOfAccount')->get();

        $trialBalance = collect();
        foreach ($ledgers as $ledger) {
            $account = $ledger->chartOfAccount;
            $isDebitBalance = in_array(strtolower($account->accountType->name ?? ''), ['asset', 'expense', 'current asset', 'fixed asset', 'bank', 'cash', 'accounts receivable']);
            
            $balance = $isDebitBalance ? ($ledger->total_debit - $ledger->total_credit) : ($ledger->total_credit - $ledger->total_debit);

            $trialBalance->push([
                'account_code' => $account->code,
                'account_name' => $account->name,
                'account_type' => $account->accountType->name ?? 'Unknown',
                'total_debit' => $ledger->total_debit,
                'total_credit' => $ledger->total_credit,
                'balance' => $balance,
                'balance_type' => $isDebitBalance ? 'Debit' : 'Credit',
            ]);
        }

        return $trialBalance->sortBy('account_code')->values();
    }

    public function generateGeneralLedgerReport(int $companyId, int $accountId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = GeneralLedger::where('company_id', $companyId)
            ->where('chart_of_account_id', $accountId)
            ->with(['journalEntry.journal']);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        return $query->orderBy('date')->orderBy('id')->get()->map(function ($ledger) {
            return [
                'date' => $ledger->date->format('Y-m-d'),
                'reference' => $ledger->reference,
                'description' => $ledger->journalEntry->description ?? '',
                'journal_number' => $ledger->journalEntry->journal->journal_number ?? '',
                'debit' => $ledger->debit,
                'credit' => $ledger->credit,
                'balance' => $ledger->balance,
            ];
        });
    }

    public function generateJournalReport(int $companyId, ?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = JournalEntry::where('company_id', $companyId)->with(['journal', 'chartOfAccount']);

        if ($startDate) {
            $query->whereHas('journal', function($q) use ($startDate) {
                $q->where('date', '>=', $startDate);
            });
        }
        if ($endDate) {
            $query->whereHas('journal', function($q) use ($endDate) {
                $q->where('date', '<=', $endDate);
            });
        }

        return $query->get()->groupBy('journal_id')->map(function ($entries) {
            $journal = $entries->first()->journal;
            return [
                'journal_number' => $journal->journal_number,
                'date' => $journal->date->format('Y-m-d'),
                'reference' => $journal->reference_number,
                'status' => $journal->status,
                'entries' => $entries->map(function ($entry) {
                    return [
                        'account_code' => $entry->chartOfAccount->code,
                        'account_name' => $entry->chartOfAccount->name,
                        'description' => $entry->description,
                        'debit' => $entry->debit,
                        'credit' => $entry->credit,
                    ];
                }),
                'total_debit' => $journal->total_debit,
                'total_credit' => $journal->total_credit,
            ];
        })->values();
    }

    public function generateFiscalYearSummary(int $companyId, int $fiscalYearId): array
    {
        $trialBalance = $this->generateTrialBalance($companyId, $fiscalYearId);
        
        $revenue = 0;
        $expenses = 0;
        $assets = 0;
        $liabilities = 0;
        $equity = 0;

        foreach ($trialBalance as $account) {
            $type = strtolower($account['account_type']);
            if (str_contains($type, 'revenue') || str_contains($type, 'sales')) {
                $revenue += $account['balance'];
            } elseif (str_contains($type, 'expense')) {
                $expenses += $account['balance'];
            } elseif (str_contains($type, 'asset') || str_contains($type, 'bank') || str_contains($type, 'cash') || str_contains($type, 'accounts receivable')) {
                $assets += $account['balance'];
            } elseif (str_contains($type, 'liability') || str_contains($type, 'accounts payable')) {
                $liabilities += $account['balance'];
            } elseif (str_contains($type, 'equity')) {
                $equity += $account['balance'];
            }
        }

        return [
            'total_revenue' => $revenue,
            'total_expenses' => $expenses,
            'net_income' => $revenue - $expenses,
            'total_assets' => $assets,
            'total_liabilities' => $liabilities,
            'total_equity' => $equity,
        ];
    }
}
