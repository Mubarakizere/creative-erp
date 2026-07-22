<?php

namespace App\Services\Finance;

use App\Models\GeneralLedger;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Exception;

class LedgerPostingService
{
    use LogsActivity;

    public function postJournal(Journal $journal): void
    {
        if ($journal->status !== 'Pending Approval' && $journal->status !== 'Draft') {
            throw new Exception("Only Draft or Pending Approval journals can be posted.");
        }

        if ($journal->total_debit !== $journal->total_credit) {
            throw new Exception("Cannot post unbalanced journal.");
        }

        DB::transaction(function () use ($journal) {
            // Check if FiscalYear is closed
            if ($journal->fiscalYear && $journal->fiscalYear->is_closed) {
                throw new Exception("Cannot post journal to a closed Fiscal Year.");
            }
            if ($journal->accountingPeriod && in_array($journal->accountingPeriod->status, ['Closed', 'Locked'])) {
                throw new Exception("Cannot post journal to a closed or locked Accounting Period.");
            }

            foreach ($journal->entries as $entry) {
                // Calculate new balance
                $lastLedger = GeneralLedger::where('company_id', $journal->company_id)
                    ->where('chart_of_account_id', $entry->chart_of_account_id)
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $previousBalance = $lastLedger ? $lastLedger->balance : 0;
                
                // Balance logic: Assuming debit increases balance for Assets/Expenses, credit decreases.
                // For Liabilities/Equity/Revenue, credit increases, debit decreases.
                // We'll calculate a raw numeric balance based on typical conventions, or simply maintain a normalized balance.
                // Standard convention: 
                // Assets & Expenses: Balance = previous + debit - credit
                // Liabilities, Equity, Revenue: Balance = previous - debit + credit
                
                // Let's look up AccountType to know how to adjust balance
                $accountCategory = $entry->chartOfAccount->accountType->category;
                
                $isDebitNormal = in_array(strtolower($accountCategory), ['asset', 'expense']);
                
                if ($isDebitNormal) {
                    $newBalance = $previousBalance + $entry->debit - $entry->credit;
                } else {
                    $newBalance = $previousBalance - $entry->debit + $entry->credit;
                }

                GeneralLedger::create([
                    'company_id' => $journal->company_id,
                    'journal_entry_id' => $entry->id,
                    'chart_of_account_id' => $entry->chart_of_account_id,
                    'fiscal_year_id' => $journal->fiscal_year_id,
                    'accounting_period_id' => $journal->accounting_period_id,
                    'date' => $journal->date,
                    'debit' => $entry->debit,
                    'credit' => $entry->credit,
                    'balance' => $newBalance,
                    'reference' => $journal->reference_number,
                    'source_type' => Journal::class,
                    'source_id' => $journal->id,
                ]);
            }

            $journal->update([
                'status' => 'Posted',
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);

            $this->logActivity('journal_posted', $journal, ['journal_number' => $journal->journal_number]);
        });
    }
}
