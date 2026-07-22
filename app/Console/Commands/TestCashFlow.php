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

class TestCashFlow extends Command
{
    protected $signature = 'test:cf';
    protected $description = 'Test Cash Flow Statement logic against General Ledger';

    public function handle(FinancialStatementService $service)
    {
        $this->info("Creating Cash Flow test environment...");

        $company = Company::create([
            'name' => 'Test CF Company ' . Str::random(5),
            'email' => Str::random(5) . '@example.com',
            'phone' => '1234567890'
        ]);

        $currAssetType = AccountType::create(['name' => 'Current Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $fixedAssetType = AccountType::create(['name' => 'Fixed Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $currLiabType = AccountType::create(['name' => 'Current Liability', 'category' => 'Liability', 'company_id' => $company->id]);
        $ltLiabType = AccountType::create(['name' => 'Long Term Liability', 'category' => 'Liability', 'company_id' => $company->id]);
        $equityType = AccountType::create(['name' => 'Equity', 'category' => 'Equity', 'company_id' => $company->id]);
        $revenueType = AccountType::create(['name' => 'Sales Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $expenseType = AccountType::create(['name' => 'Operating Expense', 'category' => 'Expense', 'company_id' => $company->id]);

        $cashAccount = ChartOfAccount::create(['name' => 'Cash in Bank', 'code' => '1000', 'account_type_id' => $currAssetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $arAccount = ChartOfAccount::create(['name' => 'Accounts Receivable', 'code' => '1200', 'account_type_id' => $currAssetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $equipmentAccount = ChartOfAccount::create(['name' => 'Equipment', 'code' => '1500', 'account_type_id' => $fixedAssetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $loanAccount = ChartOfAccount::create(['name' => 'Bank Loan', 'code' => '2500', 'account_type_id' => $ltLiabType->id, 'company_id' => $company->id, 'status' => 'active']);
        $capitalAccount = ChartOfAccount::create(['name' => 'Owner Capital', 'code' => '3000', 'account_type_id' => $equityType->id, 'company_id' => $company->id, 'status' => 'active']);
        $revenueAccount = ChartOfAccount::create(['name' => 'Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $expenseAccount = ChartOfAccount::create(['name' => 'Utilities', 'code' => '6000', 'account_type_id' => $expenseType->id, 'company_id' => $company->id, 'status' => 'active']);

        $journal = \App\Models\Journal::create([
            'company_id' => $company->id,
            'journal_number' => 'CF-JNL-' . Str::random(5),
            'date' => '2026-01-01',
            'name' => 'Cash Flow Journal',
            'code' => 'CFJ',
            'status' => 'Posted'
        ]);

        $this->info("Seeding Month 1 (January) - Establishing Opening Balances...");
        
        $this->createEntry($company->id, $journal->id, '2026-01-01', [
            ['account' => $cashAccount->id, 'debit' => 50000, 'credit' => 0],
            ['account' => $capitalAccount->id, 'debit' => 0, 'credit' => 50000],
        ]);
        $this->createEntry($company->id, $journal->id, '2026-01-10', [
            ['account' => $equipmentAccount->id, 'debit' => 20000, 'credit' => 0],
            ['account' => $cashAccount->id, 'debit' => 0, 'credit' => 20000],
        ]);
        $this->createEntry($company->id, $journal->id, '2026-01-20', [
            ['account' => $cashAccount->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $revenueAccount->id, 'debit' => 0, 'credit' => 10000],
        ]);

        $this->info("Seeding Month 2 (February) - Cash Flow Period...");
        
        // Sales on credit (no cash effect, but increases NI and AR)
        $this->createEntry($company->id, $journal->id, '2026-02-05', [
            ['account' => $arAccount->id, 'debit' => 15000, 'credit' => 0],
            ['account' => $revenueAccount->id, 'debit' => 0, 'credit' => 15000],
        ]);
        // Collect some AR (Cash up, AR down)
        $this->createEntry($company->id, $journal->id, '2026-02-10', [
            ['account' => $cashAccount->id, 'debit' => 5000, 'credit' => 0],
            ['account' => $arAccount->id, 'debit' => 0, 'credit' => 5000],
        ]);
        // Pay utilities in cash
        $this->createEntry($company->id, $journal->id, '2026-02-15', [
            ['account' => $expenseAccount->id, 'debit' => 2000, 'credit' => 0],
            ['account' => $cashAccount->id, 'debit' => 0, 'credit' => 2000],
        ]);
        // Buy more equipment in cash
        $this->createEntry($company->id, $journal->id, '2026-02-20', [
            ['account' => $equipmentAccount->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $cashAccount->id, 'debit' => 0, 'credit' => 10000],
        ]);
        // Get a bank loan in cash
        $this->createEntry($company->id, $journal->id, '2026-02-25', [
            ['account' => $cashAccount->id, 'debit' => 30000, 'credit' => 0],
            ['account' => $loanAccount->id, 'debit' => 0, 'credit' => 30000],
        ]);

        $this->info("--------------------------------------------------");
        $this->info("TESTING CASH FLOW (FEBRUARY 2026)");

        $cf = $service->generateCashFlowStatement($company->id, '2026-02-01', '2026-02-28');

        $this->assert($cf['opening_cash'], 40000, "Opening Cash (End of Jan)");
        $this->assert($cf['operating_activities']['net_income'], 13000, "Operating: Net Income (Feb)");
        $this->assert($cf['operating_activities']['changes_in_working_capital'], -10000, "Operating: Changes in Working Capital (AR Increase)");
        $this->assert($cf['operating_activities']['total'], 3000, "Operating: Total Cash Flow");
        
        $this->assert($cf['investing_activities']['total'], -10000, "Investing: Total Cash Flow");
        $this->assert($cf['financing_activities']['total'], 30000, "Financing: Total Cash Flow");
        
        $this->assert($cf['net_cash_flow'], 23000, "Net Cash Flow (Feb)");
        $this->assert($cf['closing_cash'], 63000, "Closing Cash (End of Feb)");

        $isReconciled = $cf['is_reconciled'];
        $this->info("RECONCILED? " . ($isReconciled ? "YES (PASS)" : "NO (FAIL)"));

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
        // Add rounding tolerance to prevent floating point issues breaking the visual pass
        $actualRound = round($actual, 2);
        $expectedRound = round($expected, 2);
        $status = ($actualRound == $expectedRound) ? "PASS" : "FAIL";
        $this->info("$label: Expected $expectedRound | Actual $actualRound -> $status");
    }
}
