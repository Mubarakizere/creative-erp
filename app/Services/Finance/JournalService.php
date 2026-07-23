<?php

namespace App\Services\Finance;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalService
{
    use LogsActivity;

    protected LedgerPostingService $ledgerPostingService;

    public function __construct(LedgerPostingService $ledgerPostingService)
    {
        $this->ledgerPostingService = $ledgerPostingService;
    }

    public function createManualJournal(array $data, array $entries): Journal
    {
        return DB::transaction(function () use ($data, $entries) {
            $data['journal_number'] = $data['journal_number'] ?? $this->generateJournalNumber();
            $data['status'] = $data['status'] ?? 'Draft';
            
            $journal = Journal::create($data);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $entryData) {
                $account = \App\Models\ChartOfAccount::find($entryData['chart_of_account_id']);
                if (!$account || !$account->is_active) {
                    throw new Exception("Account {$account?->code} is inactive or invalid. Inactive accounts cannot be used for new journal entries.");
                }
                
                if ($entryData['debit'] < 0 || $entryData['credit'] < 0) {
                    throw new Exception("Journal entry lines cannot have negative values.");
                }

                $entryData['company_id'] = $journal->company_id;
                $entryData['journal_id'] = $journal->id;
                
                $entry = JournalEntry::create($entryData);
                
                $totalDebit += $entry->debit;
                $totalCredit += $entry->credit;
            }

            if (round($totalDebit, 2) <= 0) {
                throw new Exception("Zero-value journals are not permitted.");
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new Exception("Journal entries must be balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            $journal->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            $this->logActivity('journal_created', ['journal_id' => $journal->id, 'journal_number' => $journal->journal_number]);

            return $journal;
        });
    }

    public function createAutomaticJournal(array $data, array $entries): Journal
    {
        // For automatic journals, they can be immediately posted
        $journal = $this->createManualJournal($data, $entries);
        $this->ledgerPostingService->postJournal($journal);
        return $journal;
    }

    public function approveJournal(Journal $journal): Journal
    {
        if ($journal->created_by === auth()->id()) {
            throw new Exception("Users cannot approve their own journals.");
        }

        $journal->update([
            'status' => 'Pending Approval', // Assuming it moves from Draft -> Pending Approval -> Posted, or directly approved
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $this->logActivity('journal_approved', ['journal_id' => $journal->id, 'journal_number' => $journal->journal_number]);
        
        return $journal;
    }

    public function postJournal(Journal $journal): void
    {
        $this->ledgerPostingService->postJournal($journal);
    }

    public function reverseJournal(Journal $journal, array $reversalData = []): Journal
    {
        if ($journal->status !== 'Posted') {
            throw new Exception("Only posted journals can be reversed.");
        }

        return DB::transaction(function () use ($journal, $reversalData) {
            $reversal = Journal::create([
                'company_id' => $journal->company_id,
                'branch_id' => $journal->branch_id,
                'department_id' => $journal->department_id,
                'project_id' => $journal->project_id,
                'client_id' => $journal->client_id,
                'currency_code' => $journal->currency_code,
                'fiscal_year_id' => $journal->fiscal_year_id,
                'accounting_period_id' => $journal->accounting_period_id,
                'journal_number' => $this->generateJournalNumber(),
                'reference_number' => 'REV-' . $journal->journal_number,
                'date' => $reversalData['date'] ?? now(),
                'memo' => 'Reversal of ' . $journal->journal_number,
                'status' => 'Draft',
            ]);

            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($journal->entries as $entry) {
                $reversalEntry = JournalEntry::create([
                    'company_id' => $reversal->company_id,
                    'journal_id' => $reversal->id,
                    'chart_of_account_id' => $entry->chart_of_account_id,
                    'description' => 'Reversal of: ' . $entry->description,
                    'debit' => $entry->credit, // Swap debit/credit
                    'credit' => $entry->debit,
                    'branch_id' => $entry->branch_id,
                    'department_id' => $entry->department_id,
                    'project_id' => $entry->project_id,
                    'client_id' => $entry->client_id,
                    'currency_code' => $entry->currency_code,
                ]);

                $totalDebit += $reversalEntry->debit;
                $totalCredit += $reversalEntry->credit;
            }

            $reversal->update([
                'total_debit' => $totalDebit,
                'total_credit' => $totalCredit,
            ]);

            $journal->update(['status' => 'Reversed']);
            $this->logActivity('journal_reversed', ['journal_id' => $journal->id, 'reversal_journal' => $reversal->journal_number]);

            return $reversal;
        });
    }

    public function cancelJournal(Journal $journal): Journal
    {
        if ($journal->status !== 'Draft') {
            throw new Exception("Only draft journals can be cancelled.");
        }

        $journal->update(['status' => 'Cancelled']);
        $this->logActivity('journal_cancelled', ['journal_id' => $journal->id, 'journal_number' => $journal->journal_number]);

        return $journal;
    }

    private function generateJournalNumber(): string
    {
        return 'JRN-' . strtoupper(uniqid());
    }
}
