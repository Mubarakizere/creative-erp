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

class TestProfitAndLoss extends Command
{
    protected $signature = 'test:pnl';
    protected $description = 'Test Profit and Loss logic against General Ledger';

    public function handle(FinancialStatementService $service)
    {
        $this->info("Creating test environment...");

        // Create a dummy company
        $company = Company::create([
            'name' => 'Test P&L Company ' . Str::random(5),
            'email' => Str::random(5) . '@example.com',
            'phone' => '1234567890'
        ]);

        // Create account types
        $revenueType = AccountType::create(['name' => 'Sales Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $cogsType = AccountType::create(['name' => 'Cost of Sales', 'category' => 'Expense', 'company_id' => $company->id]);
        $operatingExpenseType = AccountType::create(['name' => 'Operating Expense', 'category' => 'Expense', 'company_id' => $company->id]);
        $otherIncomeType = AccountType::create(['name' => 'Other Income', 'category' => 'Revenue', 'company_id' => $company->id]);
        $otherExpenseType = AccountType::create(['name' => 'Other Expense', 'category' => 'Expense', 'company_id' => $company->id]);

        // Create charts of accounts
        $revenueAccount = ChartOfAccount::create(['name' => 'Product Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $cogsAccount = ChartOfAccount::create(['name' => 'Direct Labor', 'code' => '5000', 'account_type_id' => $cogsType->id, 'company_id' => $company->id, 'status' => 'active']);
        $operatingExpenseAccount = ChartOfAccount::create(['name' => 'Rent Expense', 'code' => '6000', 'account_type_id' => $operatingExpenseType->id, 'company_id' => $company->id, 'status' => 'active']);
        
        $dates = [
            '2026-01-15' => ['rev' => 10000, 'cogs' => 3000, 'opex' => 2000], // Q1
            '2026-02-15' => ['rev' => 15000, 'cogs' => 4000, 'opex' => 2000], // Q1
            '2026-03-15' => ['rev' => 12000, 'cogs' => 3500, 'opex' => 2000], // Q1
            '2026-05-15' => ['rev' => 18000, 'cogs' => 5000, 'opex' => 2500], // Q2
            '2026-10-15' => ['rev' => 20000, 'cogs' => 6000, 'opex' => 3000], // Q4
        ];

        $journal = \App\Models\Journal::create([
            'company_id' => $company->id,
            'journal_number' => 'JNL-' . Str::random(5),
            'date' => '2026-01-01',
            'name' => 'General Journal',
            'code' => 'GJ',
            'status' => 'Posted'
        ]);

        $this->info("Seeding general ledger entries...");
        foreach ($dates as $date => $amounts) {
            
            // Revenue (Credit)
            $jeRev = \App\Models\JournalEntry::create([
                'company_id' => $company->id,
                'journal_id' => $journal->id,
                'chart_of_account_id' => $revenueAccount->id,
                'entry_number' => 'JE-' . Str::random(5),
                'date' => $date,
                'reference' => 'TEST-' . $date,
                'description' => 'Test Sales',
                'debit' => 0,
                'credit' => $amounts['rev'],
                'status' => 'Posted'
            ]);
            GeneralLedger::create([
                'company_id' => $company->id,
                'journal_entry_id' => $jeRev->id,
                'chart_of_account_id' => $revenueAccount->id,
                'date' => $date,
                'reference' => 'TEST-REV',
                'description' => 'Test Sales',
                'debit' => 0,
                'credit' => $amounts['rev'],
            ]);
            
            // COGS (Debit)
            $jeCogs = \App\Models\JournalEntry::create([
                'company_id' => $company->id,
                'journal_id' => $journal->id,
                'chart_of_account_id' => $cogsAccount->id,
                'entry_number' => 'JE-' . Str::random(5),
                'date' => $date,
                'reference' => 'TEST-' . $date,
                'description' => 'Test COGS',
                'debit' => $amounts['cogs'],
                'credit' => 0,
                'status' => 'Posted'
            ]);
            GeneralLedger::create([
                'company_id' => $company->id,
                'journal_entry_id' => $jeCogs->id,
                'chart_of_account_id' => $cogsAccount->id,
                'date' => $date,
                'reference' => 'TEST-COGS',
                'description' => 'Test COGS',
                'debit' => $amounts['cogs'],
                'credit' => 0,
            ]);
            
            // OPEX (Debit)
            $jeOpex = \App\Models\JournalEntry::create([
                'company_id' => $company->id,
                'journal_id' => $journal->id,
                'chart_of_account_id' => $operatingExpenseAccount->id,
                'entry_number' => 'JE-' . Str::random(5),
                'date' => $date,
                'reference' => 'TEST-' . $date,
                'description' => 'Test OPEX',
                'debit' => $amounts['opex'],
                'credit' => 0,
                'status' => 'Posted'
            ]);
            GeneralLedger::create([
                'company_id' => $company->id,
                'journal_entry_id' => $jeOpex->id,
                'chart_of_account_id' => $operatingExpenseAccount->id,
                'date' => $date,
                'reference' => 'TEST-OPEX',
                'description' => 'Test OPEX',
                'debit' => $amounts['opex'],
                'credit' => 0,
            ]);
        }

        $this->info("--------------------------------------------------");
        $this->info("TEST: MONTHLY REPORT (February 2026)");
        $this->runTestPeriod($service, $company->id, '2026-02-01', '2026-02-28', 15000, 4000, 2000);

        $this->info("--------------------------------------------------");
        $this->info("TEST: QUARTERLY REPORT (Q1 2026)");
        $this->runTestPeriod($service, $company->id, '2026-01-01', '2026-03-31', 37000, 10500, 6000);

        $this->info("--------------------------------------------------");
        $this->info("TEST: YEARLY REPORT (2026)");
        $this->runTestPeriod($service, $company->id, '2026-01-01', '2026-12-31', 75000, 21500, 11500);

        $this->info("--------------------------------------------------");
        $this->info("Cleaning up test data...");
        GeneralLedger::where('company_id', $company->id)->delete();
        ChartOfAccount::where('company_id', $company->id)->delete();
        AccountType::where('company_id', $company->id)->delete();
        $company->delete();
        $this->info("Done.");
    }

    private function runTestPeriod($service, $companyId, $start, $end, $expectedRev, $expectedCogs, $expectedOpex)
    {
        $pnl = $service->generateProfitAndLoss($companyId, $start, $end);

        $actualRev = $pnl['revenue']['total'];
        $actualCogs = $pnl['cost_of_sales']['total'];
        $actualGrossProfit = $pnl['gross_profit'];
        $actualOpex = $pnl['operating_expenses']['total'];
        $actualNetProfit = $pnl['net_profit'];

        $expectedGrossProfit = $expectedRev - $expectedCogs;
        $expectedNetProfit = $expectedGrossProfit - $expectedOpex;

        $this->info("Revenue: Expected $expectedRev | Actual $actualRev -> " . ($expectedRev == $actualRev ? 'PASS' : 'FAIL'));
        $this->info("Cost of Sales: Expected $expectedCogs | Actual $actualCogs -> " . ($expectedCogs == $actualCogs ? 'PASS' : 'FAIL'));
        $this->info("Gross Profit: Expected $expectedGrossProfit | Actual $actualGrossProfit -> " . ($expectedGrossProfit == $actualGrossProfit ? 'PASS' : 'FAIL'));
        $this->info("Operating Expenses: Expected $expectedOpex | Actual $actualOpex -> " . ($expectedOpex == $actualOpex ? 'PASS' : 'FAIL'));
        $this->info("Net Profit: Expected $expectedNetProfit | Actual $actualNetProfit -> " . ($expectedNetProfit == $actualNetProfit ? 'PASS' : 'FAIL'));
    }
}
