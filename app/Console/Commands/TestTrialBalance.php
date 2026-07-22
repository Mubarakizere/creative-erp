<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Services\Finance\AccountingReportService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TestTrialBalance extends Command
{
    protected $signature = 'test:tb';
    protected $description = 'Test Trial Balance Statement logic against General Ledger';

    public function handle(AccountingReportService $service)
    {
        $this->info("Creating Trial Balance test environment...");

        $company = Company::create([
            'name' => 'Test TB Company ' . Str::random(5),
            'email' => Str::random(5) . '@example.com',
            'phone' => '1234567890'
        ]);

        $assetType = AccountType::create(['name' => 'Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $liabilityType = AccountType::create(['name' => 'Liability', 'category' => 'Liability', 'company_id' => $company->id]);
        $equityType = AccountType::create(['name' => 'Equity', 'category' => 'Equity', 'company_id' => $company->id]);
        $revenueType = AccountType::create(['name' => 'Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $expenseType = AccountType::create(['name' => 'Expense', 'category' => 'Expense', 'company_id' => $company->id]);

        $cash = ChartOfAccount::create(['name' => 'Cash', 'code' => '1000', 'account_type_id' => $assetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $ar = ChartOfAccount::create(['name' => 'Accounts Receivable', 'code' => '1200', 'account_type_id' => $assetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $ap = ChartOfAccount::create(['name' => 'Accounts Payable', 'code' => '2000', 'account_type_id' => $liabilityType->id, 'company_id' => $company->id, 'status' => 'active']);
        $capital = ChartOfAccount::create(['name' => 'Capital', 'code' => '3000', 'account_type_id' => $equityType->id, 'company_id' => $company->id, 'status' => 'active']);
        $sales = ChartOfAccount::create(['name' => 'Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $rent = ChartOfAccount::create(['name' => 'Rent Expense', 'code' => '6000', 'account_type_id' => $expenseType->id, 'company_id' => $company->id, 'status' => 'active']);

        $journal = \App\Models\Journal::create([
            'company_id' => $company->id,
            'journal_number' => 'TB-JNL-' . Str::random(5),
            'date' => '2026-03-01',
            'name' => 'Trial Balance Journal',
            'code' => 'TBJ',
            'status' => 'Posted'
        ]);

        $this->info("Seeding double-entry ledger transactions...");
        
        // 1. Initial Investment
        $this->createEntry($company->id, $journal->id, '2026-03-01', [
            ['account' => $cash->id, 'debit' => 100000, 'credit' => 0],
            ['account' => $capital->id, 'debit' => 0, 'credit' => 100000],
        ]);
        // 2. Sales on Credit
        $this->createEntry($company->id, $journal->id, '2026-03-05', [
            ['account' => $ar->id, 'debit' => 20000, 'credit' => 0],
            ['account' => $sales->id, 'debit' => 0, 'credit' => 20000],
        ]);
        // 3. Receive AR Cash
        $this->createEntry($company->id, $journal->id, '2026-03-10', [
            ['account' => $cash->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $ar->id, 'debit' => 0, 'credit' => 10000],
        ]);
        // 4. Rent on AP
        $this->createEntry($company->id, $journal->id, '2026-03-15', [
            ['account' => $rent->id, 'debit' => 5000, 'credit' => 0],
            ['account' => $ap->id, 'debit' => 0, 'credit' => 5000],
        ]);

        $this->info("--------------------------------------------------");
        $this->info("TESTING TRIAL BALANCE");

        $tbData = $service->generateTrialBalance($company->id);

        $accounts = $tbData['accounts'];
        $totalDebits = $tbData['total_debits'];
        $totalCredits = $tbData['total_credits'];

        $this->assert($totalDebits, 135000, "Total Debits Validation");
        $this->assert($totalCredits, 135000, "Total Credits Validation");
        
        $isBalanced = round($totalDebits, 2) === round($totalCredits, 2);
        $this->info("BALANCED? " . ($isBalanced ? "YES (PASS)" : "NO (FAIL)"));

        $this->info("--------------------------------------------------");
        $this->info("Checking Account Balances:");

        foreach ($accounts as $acc) {
            $code = $acc['code'];
            $bal = $acc['balance'];
            
            if ($code === '1000') $this->assert($bal, 110000, "Cash Balance (100k + 10k)");
            if ($code === '1200') $this->assert($bal, 10000, "AR Balance (20k - 10k)");
            if ($code === '2000') $this->assert($bal, 5000, "AP Balance (5k)");
            if ($code === '3000') $this->assert($bal, 100000, "Capital Balance (100k)");
            if ($code === '4000') $this->assert($bal, 20000, "Sales Balance (20k)");
            if ($code === '6000') $this->assert($bal, 5000, "Rent Expense Balance (5k)");
        }

        $this->info("--------------------------------------------------");
        $this->info("Cleaning up...");
        GeneralLedger::where('company_id', $company->id)->delete();
        ChartOfAccount::where('company_id', $company->id)->delete();
        AccountType::where('company_id', $company->id)->delete();
        $company->delete();
        $this->info("Done.");
    }

    private function createEntry($companyId, $journalId, $date, $lines)
    {
        $je = \App\Models\JournalEntry::create([
            'company_id' => $companyId,
            'journal_id' => $journalId,
            'chart_of_account_id' => $lines[0]['account'],
            'entry_number' => 'JE-' . Str::random(5),
            'date' => $date,
            'reference' => 'TEST-' . $date,
            'description' => 'Double Entry Test',
            'debit' => 0,
            'credit' => 0,
            'status' => 'Posted'
        ]);

        foreach ($lines as $line) {
            GeneralLedger::create([
                'company_id' => $companyId,
                'journal_entry_id' => $je->id,
                'chart_of_account_id' => $line['account'],
                'date' => $date,
                'reference' => 'TEST-REF',
                'description' => 'Test Line',
                'debit' => $line['debit'],
                'credit' => $line['credit'],
            ]);
        }
    }

    private function assert($actual, $expected, $label)
    {
        $actualRound = round($actual, 2);
        $expectedRound = round($expected, 2);
        $status = ($actualRound == $expectedRound) ? "PASS" : "FAIL";
        $this->info("$label: Expected $expectedRound | Actual $actualRound -> $status");
    }
}
