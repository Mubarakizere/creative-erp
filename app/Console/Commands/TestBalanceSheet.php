<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Services\Finance\FinancialStatementService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TestBalanceSheet extends Command
{
    protected $signature = 'test:bs';
    protected $description = 'Test Balance Sheet logic against General Ledger';

    public function handle(FinancialStatementService $service)
    {
        $this->info("Creating Balance Sheet test environment...");

        // Create a dummy company
        $company = Company::create([
            'name' => 'Test BS Company ' . Str::random(5),
            'email' => Str::random(5) . '@example.com',
            'phone' => '1234567890'
        ]);

        // Create account types
        $currAssetType = AccountType::create(['name' => 'Current Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $fixedAssetType = AccountType::create(['name' => 'Fixed Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $currLiabType = AccountType::create(['name' => 'Current Liability', 'category' => 'Liability', 'company_id' => $company->id]);
        $ltLiabType = AccountType::create(['name' => 'Long Term Liability', 'category' => 'Liability', 'company_id' => $company->id]);
        $equityType = AccountType::create(['name' => 'Equity', 'category' => 'Equity', 'company_id' => $company->id]);
        $revenueType = AccountType::create(['name' => 'Sales Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $expenseType = AccountType::create(['name' => 'Operating Expense', 'category' => 'Expense', 'company_id' => $company->id]);

        // Create charts of accounts
        $cashAccount = ChartOfAccount::create(['name' => 'Cash in Bank', 'code' => '1000', 'account_type_id' => $currAssetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $equipmentAccount = ChartOfAccount::create(['name' => 'Equipment', 'code' => '1500', 'account_type_id' => $fixedAssetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $apAccount = ChartOfAccount::create(['name' => 'Accounts Payable', 'code' => '2000', 'account_type_id' => $currLiabType->id, 'company_id' => $company->id, 'status' => 'active']);
        $loanAccount = ChartOfAccount::create(['name' => 'Bank Loan', 'code' => '2500', 'account_type_id' => $ltLiabType->id, 'company_id' => $company->id, 'status' => 'active']);
        $capitalAccount = ChartOfAccount::create(['name' => 'Owner Capital', 'code' => '3000', 'account_type_id' => $equityType->id, 'company_id' => $company->id, 'status' => 'active']);
        $revenueAccount = ChartOfAccount::create(['name' => 'Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $expenseAccount = ChartOfAccount::create(['name' => 'Utilities', 'code' => '6000', 'account_type_id' => $expenseType->id, 'company_id' => $company->id, 'status' => 'active']);

        $journal = \App\Models\Journal::create([
            'company_id' => $company->id,
            'journal_number' => 'BS-JNL-' . Str::random(5),
            'date' => '2026-06-01',
            'name' => 'Balance Sheet Journal',
            'code' => 'BSJ',
            'status' => 'Posted'
        ]);

        $this->info("Seeding double-entry ledger...");

        // Entry 1: Owner invests $100,000 cash
        $this->createEntry($company->id, $journal->id, '2026-06-01', [
            ['account' => $cashAccount->id, 'debit' => 100000, 'credit' => 0],
            ['account' => $capitalAccount->id, 'debit' => 0, 'credit' => 100000],
        ]);

        // Entry 2: Buy equipment for $50,000 ($20,000 cash, $30,000 loan)
        $this->createEntry($company->id, $journal->id, '2026-06-05', [
            ['account' => $equipmentAccount->id, 'debit' => 50000, 'credit' => 0],
            ['account' => $cashAccount->id, 'debit' => 0, 'credit' => 20000],
            ['account' => $loanAccount->id, 'debit' => 0, 'credit' => 30000],
        ]);

        // Entry 3: Make sales $30,000 (Cash)
        $this->createEntry($company->id, $journal->id, '2026-06-15', [
            ['account' => $cashAccount->id, 'debit' => 30000, 'credit' => 0],
            ['account' => $revenueAccount->id, 'debit' => 0, 'credit' => 30000],
        ]);

        // Entry 4: Receive utility bill $10,000 (Accounts Payable)
        $this->createEntry($company->id, $journal->id, '2026-06-20', [
            ['account' => $expenseAccount->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $apAccount->id, 'debit' => 0, 'credit' => 10000],
        ]);

        $this->info("--------------------------------------------------");
        $this->info("TESTING BALANCE SHEET (AS OF 2026-06-30)");

        $bs = $service->generateBalanceSheet($company->id, '2026-06-30');

        $actualCurrentAssets = $bs['assets']['current']['total'];
        $actualFixedAssets = $bs['assets']['fixed']['total'];
        $actualCurrentLiab = $bs['liabilities']['current']['total'];
        $actualLongTermLiab = $bs['liabilities']['long_term']['total'];
        $actualEquity = $bs['equity']['base']['total'];
        $actualRetained = $bs['equity']['retained_earnings'];
        
        $totalAssets = $bs['assets']['total'];
        $totalLiabEquity = $bs['liabilities']['total'] + $bs['equity']['total'];

        $this->assert($actualCurrentAssets, 110000, "Current Assets");
        $this->assert($actualFixedAssets, 50000, "Fixed Assets");
        $this->assert($actualCurrentLiab, 10000, "Current Liabilities");
        $this->assert($actualLongTermLiab, 30000, "Long Term Liabilities");
        $this->assert($actualEquity, 100000, "Base Equity");
        $this->assert($actualRetained, 20000, "Retained Earnings");
        
        $this->info("--------------------------------------------------");
        $this->info("EQUATION: Assets = Liabilities + Equity");
        $this->info("Assets: $" . number_format($totalAssets, 2));
        $this->info("Liab + Equity: $" . number_format($totalLiabEquity, 2));
        $isBalanced = ($totalAssets == $totalLiabEquity) && $bs['is_balanced'];
        $this->info("BALANCED? " . ($isBalanced ? "YES (PASS)" : "NO (FAIL)"));

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
            'chart_of_account_id' => $lines[0]['account'], // required column
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
        $status = ($actual == $expected) ? "PASS" : "FAIL";
        $this->info("$label: Expected $expected | Actual $actual -> $status");
    }
}
