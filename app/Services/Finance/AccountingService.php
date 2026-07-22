<?php

namespace App\Services\Finance;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\OpeningBalance;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Exception;

class AccountingService
{
    use LogsActivity;

    public function createAccountType(array $data): AccountType
    {
        return DB::transaction(function () use ($data) {
            $accountType = AccountType::create($data);
            $this->logActivity('account_type_created', $accountType, ['name' => $accountType->name]);
            return $accountType;
        });
    }

    public function createChartOfAccount(array $data): ChartOfAccount
    {
        return DB::transaction(function () use ($data) {
            // Ensure parent account belongs to the same company
            if (isset($data['parent_id'])) {
                $parent = ChartOfAccount::findOrFail($data['parent_id']);
                if ($parent->company_id !== $data['company_id']) {
                    throw new Exception("Parent account does not belong to this company.");
                }
            }

            $account = ChartOfAccount::create($data);
            $this->logActivity('account_created', $account, ['code' => $account->code, 'name' => $account->name]);
            return $account;
        });
    }

    public function updateChartOfAccount(ChartOfAccount $account, array $data): ChartOfAccount
    {
        return DB::transaction(function () use ($account, $data) {
            if ($account->is_system && isset($data['is_active']) && !$data['is_active']) {
                throw new Exception("System accounts cannot be deactivated.");
            }

            $account->update($data);
            $this->logActivity('account_updated', $account, ['name' => $account->name]);
            return $account;
        });
    }

    public function setOpeningBalance(array $data): OpeningBalance
    {
        return DB::transaction(function () use ($data) {
            $openingBalance = OpeningBalance::updateOrCreate(
                [
                    'company_id' => $data['company_id'],
                    'fiscal_year_id' => $data['fiscal_year_id'],
                    'chart_of_account_id' => $data['chart_of_account_id'],
                ],
                [
                    'debit' => $data['debit'] ?? 0,
                    'credit' => $data['credit'] ?? 0,
                    'import_date' => $data['import_date'],
                    'imported_by' => $data['imported_by'] ?? auth()->id(),
                ]
            );

            $this->logActivity('opening_balance_set', $openingBalance, [
                'account_id' => $data['chart_of_account_id'],
                'debit' => $openingBalance->debit,
                'credit' => $openingBalance->credit
            ]);

            return $openingBalance;
        });
    }

    public function importOpeningBalances(array $balances, int $companyId, int $fiscalYearId, ?int $accountingPeriodId = null): \App\Models\Journal
    {
        return DB::transaction(function () use ($balances, $companyId, $fiscalYearId, $accountingPeriodId) {
            $totalDebit = 0;
            $totalCredit = 0;
            $journalEntries = [];

            foreach ($balances as $balanceData) {
                $debit = $balanceData['debit'] ?? 0;
                $credit = $balanceData['credit'] ?? 0;

                if ($debit < 0 || $credit < 0) {
                    throw new Exception("Opening balances cannot be negative.");
                }

                $totalDebit += $debit;
                $totalCredit += $credit;

                // Create or update OpeningBalance record
                $this->setOpeningBalance([
                    'company_id' => $companyId,
                    'fiscal_year_id' => $fiscalYearId,
                    'chart_of_account_id' => $balanceData['chart_of_account_id'],
                    'debit' => $debit,
                    'credit' => $credit,
                    'import_date' => $balanceData['import_date'] ?? now()->toDateString(),
                    'imported_by' => auth()->id() ?? 1,
                ]);

                // Prepare journal entry
                if ($debit > 0 || $credit > 0) {
                    $journalEntries[] = [
                        'chart_of_account_id' => $balanceData['chart_of_account_id'],
                        'description' => 'Opening Balance',
                        'debit' => $debit,
                        'credit' => $credit,
                    ];
                }
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                throw new Exception("Opening balances must be balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            // Create and post the journal
            $journalService = app(JournalService::class);
            $journal = $journalService->createAutomaticJournal(
                [
                    'company_id' => $companyId,
                    'fiscal_year_id' => $fiscalYearId,
                    'accounting_period_id' => $accountingPeriodId,
                    'date' => $balances[0]['import_date'] ?? now()->toDateString(),
                    'memo' => 'Opening Balances Import',
                    'reference_number' => 'OB-' . date('Ymd'),
                ],
                $journalEntries
            );

            // The 'import_opening_balances' activity is logged implicitly by createAutomaticJournal -> postJournal,
            // but we can add a specific log here.
            $this->logActivity('opening_balances_imported', $journal, ['fiscal_year_id' => $fiscalYearId]);

            return $journal;
        });
    }

    public function closeFiscalYear(int $companyId, int $fiscalYearId, int $nextFiscalYearId, int $retainedEarningsAccountId): \App\Models\ClosingEntry
    {
        return DB::transaction(function () use ($companyId, $fiscalYearId, $nextFiscalYearId, $retainedEarningsAccountId) {
            $fiscalYear = \App\Models\FiscalYear::findOrFail($fiscalYearId);
            $nextFiscalYear = \App\Models\FiscalYear::findOrFail($nextFiscalYearId);

            if ($fiscalYear->is_closed) {
                throw new Exception("Fiscal year is already closed.");
            }

            $accounts = \App\Models\ChartOfAccount::with('accountType')->where('company_id', $companyId)->get();
            
            $journalEntries = [];
            $totalRetainedEarningsDelta = 0; // Credit increases RE, Debit decreases RE
            
            $openingBalancesNextYear = [];

            foreach ($accounts as $account) {
                $category = strtolower($account->accountType->category ?? '');
                
                $lastLedger = \App\Models\GeneralLedger::where('company_id', $companyId)
                    ->where('chart_of_account_id', $account->id)
                    ->where('date', '<=', $fiscalYear->end_date)
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();
                
                $balance = $lastLedger ? $lastLedger->balance : 0;
                
                if ($balance == 0) continue;

                if (in_array($category, ['revenue', 'expense'])) {
                    // Close the account to 0.
                    // For revenue (normal credit), we debit the balance.
                    // For expense (normal debit), we credit the balance.
                    if ($category === 'revenue') {
                        $debit = $balance;
                        $credit = 0;
                        $totalRetainedEarningsDelta += $balance; // Revenue increases RE
                    } else {
                        $debit = 0;
                        $credit = $balance;
                        $totalRetainedEarningsDelta -= $balance; // Expense decreases RE
                    }

                    $journalEntries[] = [
                        'chart_of_account_id' => $account->id,
                        'description' => 'Year-end closing entry',
                        'debit' => $debit,
                        'credit' => $credit,
                    ];
                } else {
                    // Asset, Liability, Equity: carry forward
                    // If Asset (normal debit), balance is debit. If Liability/Equity (normal credit), balance is credit.
                    $isDebitNormal = in_array($category, ['asset']);
                    $debit = $isDebitNormal ? $balance : 0;
                    $credit = !$isDebitNormal ? $balance : 0;

                    $openingBalancesNextYear[] = [
                        'chart_of_account_id' => $account->id,
                        'debit' => $debit,
                        'credit' => $credit,
                        'import_date' => $nextFiscalYear->start_date->toDateString(),
                    ];
                }
            }

            // Balancing entry to Retained Earnings
            if ($totalRetainedEarningsDelta !== 0 || !empty($journalEntries)) {
                $reDebit = 0;
                $reCredit = 0;
                
                if ($totalRetainedEarningsDelta > 0) {
                    $reCredit = $totalRetainedEarningsDelta; // Net income -> Credit RE
                } else {
                    $reDebit = abs($totalRetainedEarningsDelta); // Net loss -> Debit RE
                }
                
                $journalEntries[] = [
                    'chart_of_account_id' => $retainedEarningsAccountId,
                    'description' => 'Year-end closing entry - Net Income/Loss',
                    'debit' => $reDebit,
                    'credit' => $reCredit,
                ];

                // Create the closing journal
                $journalService = app(JournalService::class);
                $closingJournal = $journalService->createAutomaticJournal(
                    [
                        'company_id' => $companyId,
                        'fiscal_year_id' => $fiscalYearId,
                        'date' => $fiscalYear->end_date->toDateString(),
                        'memo' => 'Year-End Closing Journal',
                        'reference_number' => 'CL-' . $fiscalYear->name,
                    ],
                    $journalEntries
                );

                $closingEntry = \App\Models\ClosingEntry::create([
                    'company_id' => $companyId,
                    'fiscal_year_id' => $fiscalYearId,
                    'journal_id' => $closingJournal->id,
                    'created_by' => auth()->id() ?? 1,
                ]);
            } else {
                // No revenue or expense to close
                $closingEntry = \App\Models\ClosingEntry::create([
                    'company_id' => $companyId,
                    'fiscal_year_id' => $fiscalYearId,
                    'journal_id' => 1, // Dummy or we make journal_id nullable? Let's check migration.
                    // Wait, journal_id is constrained. If there's no revenue/expense, we still create a 0-value journal?
                    // But our validation prevents 0-value journals. Let's just not create a ClosingEntry if no journal,
                    // or maybe we just skip it.
                ]);
            }

            // Carry forward Retained Earnings ending balance correctly:
            // The retained earnings account ALSO gets carried forward, but it needs to include the newly posted net income!
            // Wait, our previous loop gathered the balances BEFORE the closing journal was posted!
            // So we need to add $totalRetainedEarningsDelta to the RE account's opening balance for next year.
            $reFound = false;
            foreach ($openingBalancesNextYear as &$ob) {
                if ($ob['chart_of_account_id'] == $retainedEarningsAccountId) {
                    $ob['credit'] += $totalRetainedEarningsDelta; 
                    // If credit < 0, handle it properly (turn into debit). 
                    // We can just calculate the net credit/debit.
                    $netCredit = $ob['credit'] - $ob['debit'];
                    if ($netCredit > 0) {
                        $ob['credit'] = $netCredit;
                        $ob['debit'] = 0;
                    } else {
                        $ob['debit'] = abs($netCredit);
                        $ob['credit'] = 0;
                    }
                    $reFound = true;
                    break;
                }
            }
            if (!$reFound && $totalRetainedEarningsDelta !== 0) {
                $openingBalancesNextYear[] = [
                    'chart_of_account_id' => $retainedEarningsAccountId,
                    'debit' => $totalRetainedEarningsDelta < 0 ? abs($totalRetainedEarningsDelta) : 0,
                    'credit' => $totalRetainedEarningsDelta > 0 ? $totalRetainedEarningsDelta : 0,
                    'import_date' => $nextFiscalYear->start_date->toDateString(),
                ];
            }

            // Import Opening Balances for next year
            if (!empty($openingBalancesNextYear)) {
                $this->importOpeningBalances($openingBalancesNextYear, $companyId, $nextFiscalYearId);
            }

            // Close the current fiscal year
            $fiscalYear->update([
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => auth()->id() ?? 1,
            ]);

            $this->logActivity('fiscal_year_closed', $fiscalYear, ['fiscal_year' => $fiscalYear->name]);

            return $closingEntry ?? new \App\Models\ClosingEntry(); // Or throw/handle
        });
    }
}
