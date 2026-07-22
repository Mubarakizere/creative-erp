<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:analytics';
    protected $description = 'Verify comprehensive analytics trends and profitability rankings';

    public function handle(\App\Services\Metrics\ChartService $chartService, \App\Services\Metrics\ProjectMetrics $projectMetrics, \App\Services\Metrics\ClientMetrics $clientMetrics, \App\Services\Metrics\PaymentMetrics $paymentMetrics)
    {
        $this->info("Creating Analytics test environment...");

        $company = \App\Models\Company::create(['name' => 'Analytics Test Co ' . \Illuminate\Support\Str::random(5), 'email' => 'ana' . \Illuminate\Support\Str::random(5) . '@test.com', 'phone' => '0000']);
        
        $assetType = \App\Models\AccountType::create(['name' => 'Current Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $revenueType = \App\Models\AccountType::create(['name' => 'Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $expenseType = \App\Models\AccountType::create(['name' => 'Expense', 'category' => 'Expense', 'company_id' => $company->id]);

        $cash = \App\Models\ChartOfAccount::create(['name' => 'Cash', 'code' => '1000', 'account_type_id' => $assetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $sales = \App\Models\ChartOfAccount::create(['name' => 'Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $rent = \App\Models\ChartOfAccount::create(['name' => 'Rent Expense', 'code' => '6000', 'account_type_id' => $expenseType->id, 'company_id' => $company->id, 'status' => 'active']);

        $branchA = \App\Models\Branch::create(['company_id' => $company->id, 'name' => 'Branch A', 'code' => 'BA-' . \Illuminate\Support\Str::random(3)]);
        $branchB = \App\Models\Branch::create(['company_id' => $company->id, 'name' => 'Branch B', 'code' => 'BB-' . \Illuminate\Support\Str::random(3)]);
        
        $deptA = \App\Models\Department::create(['company_id' => $company->id, 'branch_id' => $branchA->id, 'name' => 'Dept A', 'code' => 'DA-' . \Illuminate\Support\Str::random(3)]);
        $deptB = \App\Models\Department::create(['company_id' => $company->id, 'branch_id' => $branchB->id, 'name' => 'Dept B', 'code' => 'DB-' . \Illuminate\Support\Str::random(3)]);

        $clientA = \App\Models\Client::factory()->create(['company_id' => $company->id, 'branch_id' => $branchA->id, 'display_name' => 'Client A', 'email' => 'a' . \Illuminate\Support\Str::random(3) . '@a.com']);
        $clientB = \App\Models\Client::factory()->create(['company_id' => $company->id, 'branch_id' => $branchB->id, 'display_name' => 'Client B', 'email' => 'b' . \Illuminate\Support\Str::random(3) . '@b.com']);

        $projectA = \App\Models\Project::factory()->create(['company_id' => $company->id, 'branch_id' => $branchA->id, 'client_id' => $clientA->id, 'name' => 'Project A']);
        $projectB = \App\Models\Project::factory()->create(['company_id' => $company->id, 'branch_id' => $branchB->id, 'client_id' => $clientB->id, 'name' => 'Project B']);

        $this->info("Seeding 6 months of historical data...");

        $m1 = now()->subMonths(1)->startOfMonth();
        $m2 = now()->subMonths(2)->startOfMonth();

        // Month -1: 
        $journalA = \App\Models\Journal::create(['company_id' => $company->id, 'branch_id' => $branchA->id, 'department_id' => $deptA->id, 'journal_number' => 'ANA-JNL-' . \Illuminate\Support\Str::random(5), 'date' => $m1->toDateString(), 'name' => 'Ana JNL A', 'code' => 'ANA-' . \Illuminate\Support\Str::random(3), 'status' => 'Posted']);
        $this->createEntry($company->id, $journalA->id, $m1->toDateString(), [
            ['account' => $cash->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $sales->id, 'debit' => 0, 'credit' => 10000],
            ['account' => $rent->id, 'debit' => 2000, 'credit' => 0],
            ['account' => $cash->id, 'debit' => 0, 'credit' => 2000],
        ]);
        \App\Models\Invoice::create(['company_id' => $company->id, 'branch_id' => $branchA->id, 'client_id' => $clientA->id, 'project_id' => $projectA->id, 'invoice_number' => 'INVA1-'.\Illuminate\Support\Str::random(4), 'issue_date' => $m1->toDateString(), 'due_date' => clone $m1, 'total_amount' => 10000, 'balance_due' => 0, 'status' => 'Paid']);
        \App\Models\Payment::create(['company_id' => $company->id, 'client_id' => $clientA->id, 'payment_number' => 'PAY-'.\Illuminate\Support\Str::random(5), 'amount' => 10000, 'payment_date' => $m1->toDateString(), 'payment_method_id' => 1, 'status' => 'Completed']);

        // Month -2: 
        $journalB = \App\Models\Journal::create(['company_id' => $company->id, 'branch_id' => $branchB->id, 'department_id' => $deptB->id, 'journal_number' => 'ANA-JNL-' . \Illuminate\Support\Str::random(5), 'date' => $m2->toDateString(), 'name' => 'Ana JNL B', 'code' => 'ANA-' . \Illuminate\Support\Str::random(3), 'status' => 'Posted']);
        $this->createEntry($company->id, $journalB->id, $m2->toDateString(), [
            ['account' => $cash->id, 'debit' => 5000, 'credit' => 0],
            ['account' => $sales->id, 'debit' => 0, 'credit' => 5000],
            ['account' => $rent->id, 'debit' => 3000, 'credit' => 0],
            ['account' => $cash->id, 'debit' => 0, 'credit' => 3000],
        ]);
        \App\Models\Invoice::create(['company_id' => $company->id, 'branch_id' => $branchB->id, 'client_id' => $clientB->id, 'project_id' => $projectB->id, 'invoice_number' => 'INVB1-'.\Illuminate\Support\Str::random(4), 'issue_date' => $m2->toDateString(), 'due_date' => clone $m2, 'total_amount' => 5000, 'balance_due' => 0, 'status' => 'Paid']);
        \App\Models\Payment::create(['company_id' => $company->id, 'client_id' => $clientB->id, 'payment_number' => 'PAY-'.\Illuminate\Support\Str::random(5), 'amount' => 5000, 'payment_date' => $m2->toDateString(), 'payment_method_id' => 1, 'status' => 'Completed']);

        $this->info("--------------------------------------------------");
        $this->info("PHASE 1: Verify General Ledger Trend Arrays");
        
        $filters = ['company_id' => $company->id];
        
        // $chartService trends return 6-element arrays (Month -5 to Month 0)
        // Month -1 is index 4. Month -2 is index 3.
        $chartData = $chartService->getChartData($filters);
        $rev = $chartData['revenueTrends'];
        $exp = $chartData['expenseTrends'];
        $prof = $chartData['profitTrends'];

        $this->assert($rev[4] == 10000, "M-1 Revenue", 10000, $rev[4]);
        $this->assert($rev[3] == 5000, "M-2 Revenue", 5000, $rev[3]);
        
        $this->assert($exp[4] == 2000, "M-1 Expense", 2000, $exp[4]);
        $this->assert($exp[3] == 3000, "M-2 Expense", 3000, $exp[3]);

        $this->assert($prof[4] == 8000, "M-1 Profit", 8000, $prof[4]);
        $this->assert($prof[3] == 2000, "M-2 Profit", 2000, $prof[3]);

        $this->info("--------------------------------------------------");
        $this->info("PHASE 2: Verify Performance Rankings (GL)");

        $deptPerf = $chartService->departmentPerformance($filters);
        // Dept A should have 8k profit, Dept B should have 2k profit
        $this->assert($deptPerf[0]['name'] == 'Dept A' && $deptPerf[0]['net_profit'] == 8000, "Top Department", "Dept A (8k)", $deptPerf[0]['name'] . " (" . $deptPerf[0]['net_profit'] . ")");

        $branchPerf = $chartService->branchPerformance($filters);
        // Branch A should have 8k profit
        $this->assert($branchPerf[0]['name'] == 'Branch A' && $branchPerf[0]['net_profit'] == 8000, "Top Branch", "Branch A (8k)", $branchPerf[0]['name'] . " (" . $branchPerf[0]['net_profit'] . ")");

        $this->info("--------------------------------------------------");
        $this->info("PHASE 3: Verify Profitability Rankings (Invoices)");

        $projPerf = $projectMetrics->reports($filters)['projectProfitability'] ?? [];
        $this->assert($projPerf[0]['name'] == 'Project A' && $projPerf[0]['net_profit'] == 10000, "Top Project", "Project A (10k)", $projPerf[0]['name'] . " (" . $projPerf[0]['net_profit'] . ")");

        $clientPerf = $clientMetrics->reports($filters)['customerProfitability'] ?? [];
        $this->assert($clientPerf[0]['name'] == 'Client A' && $clientPerf[0]['net_profit'] == 10000, "Top Customer", "Client A (10k)", $clientPerf[0]['name'] . " (" . $clientPerf[0]['net_profit'] . ")");

        $this->info("--------------------------------------------------");
        $this->info("PHASE 4: Verify Payment Trends");
        
        $payTrends = $paymentMetrics->reports($filters)['paymentTrends'] ?? [];
        // M-1 should be 10k, M-2 should be 5k
        $this->assert($payTrends[4] == 10000, "M-1 Payments", 10000, $payTrends[4]);
        $this->assert($payTrends[3] == 5000, "M-2 Payments", 5000, $payTrends[3]);

        $this->info("--------------------------------------------------");
        $this->info("Cleaning up...");
        \App\Models\Journal::where('company_id', $company->id)->delete();
        $company->forceDelete();
        $this->info("Done.");
    }

    private function createEntry($companyId, $journalId, $date, $lines)
    {
        $je = \App\Models\JournalEntry::create([
            'company_id' => $companyId,
            'journal_id' => $journalId,
            'chart_of_account_id' => $lines[0]['account'],
            'entry_number' => 'JE-' . \Illuminate\Support\Str::random(5),
            'date' => $date,
            'reference' => 'TEST',
            'description' => 'Double Entry Test',
            'debit' => 0,
            'credit' => 0,
            'status' => 'Posted'
        ]);

        foreach ($lines as $line) {
            \App\Models\GeneralLedger::create([
                'company_id' => $companyId,
                'journal_entry_id' => $je->id,
                'chart_of_account_id' => $line['account'],
                'date' => $date,
                'reference' => 'TEST',
                'description' => 'Test Line',
                'debit' => $line['debit'],
                'credit' => $line['credit'],
            ]);
        }
    }

    private function assert($condition, $label, $expected, $actual)
    {
        if ($condition) {
            $this->info("$label: Expected $expected | Actual $actual -> PASS");
        } else {
            $this->error("$label: Expected $expected | Actual $actual -> FAIL");
        }
    }
}
