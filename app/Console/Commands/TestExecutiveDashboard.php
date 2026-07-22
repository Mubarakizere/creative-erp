<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Company;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\GeneralLedger;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\Budget;
use App\Services\Metrics\ExecutiveMetrics;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TestExecutiveDashboard extends Command
{
    protected $signature = 'test:dashboard';
    protected $description = 'Test Executive Dashboard KPI aggregation';

    public function handle(ExecutiveMetrics $service)
    {
        $this->info("Creating Executive Dashboard test environment...");

        $company = Company::create(['name' => 'Dashboard Test Co ' . Str::random(5), 'email' => 'dash' . Str::random(5) . '@test.com', 'phone' => '0000']);
        
        $assetType = AccountType::create(['name' => 'Current Asset', 'category' => 'Asset', 'company_id' => $company->id]);
        $revenueType = AccountType::create(['name' => 'Revenue', 'category' => 'Revenue', 'company_id' => $company->id]);
        $expenseType = AccountType::create(['name' => 'Expense', 'category' => 'Expense', 'company_id' => $company->id]);
        
        $cash = ChartOfAccount::create(['name' => 'Cash', 'code' => '1000', 'account_type_id' => $assetType->id, 'company_id' => $company->id, 'status' => 'active']);
        $sales = ChartOfAccount::create(['name' => 'Sales', 'code' => '4000', 'account_type_id' => $revenueType->id, 'company_id' => $company->id, 'status' => 'active']);
        $rent = ChartOfAccount::create(['name' => 'Rent Expense', 'code' => '6000', 'account_type_id' => $expenseType->id, 'company_id' => $company->id, 'status' => 'active']);

        $branch = \App\Models\Branch::create(['company_id' => $company->id, 'name' => 'HQ', 'code' => 'HQ-' . Str::random(3)]);

        $clientA = Client::factory()->create(['company_id' => $company->id, 'branch_id' => $branch->id, 'display_name' => 'Client Alpha', 'email' => 'a' . Str::random(3) . '@a.com', 'status' => 'active']);
        $clientB = Client::factory()->create(['company_id' => $company->id, 'branch_id' => $branch->id, 'display_name' => 'Client Beta', 'email' => 'b' . Str::random(3) . '@b.com', 'status' => 'active']);

        $projectX = Project::factory()->create(['company_id' => $company->id, 'branch_id' => $branch->id, 'client_id' => $clientA->id, 'name' => 'Project X', 'status' => 'in_progress']);
        
        $fiscalYear = \App\Models\FiscalYear::create([
            'company_id' => $company->id,
            'name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'status' => 'active'
        ]);

        $budget = Budget::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'fiscal_year_id' => $fiscalYear->id,
            'name' => 'Annual Budget',
            'total_amount' => 50000,
            'status' => 'active'
        ]);

        $journal = \App\Models\Journal::create(['company_id' => $company->id, 'journal_number' => 'DB-JNL-' . Str::random(5), 'date' => '2026-05-01', 'name' => 'Dashboard JNL', 'code' => 'DBJ-' . Str::random(3), 'status' => 'Posted']);

        $this->info("Seeding initial data...");

        // Initial Cash Sales 30k
        $this->createEntry($company->id, $journal->id, '2026-05-01', [
            ['account' => $cash->id, 'debit' => 30000, 'credit' => 0],
            ['account' => $sales->id, 'debit' => 0, 'credit' => 30000],
        ]);
        // Initial Expenses 10k
        $this->createEntry($company->id, $journal->id, '2026-05-02', [
            ['account' => $rent->id, 'debit' => 10000, 'credit' => 0],
            ['account' => $cash->id, 'debit' => 0, 'credit' => 10000],
        ]);

        // Invoice 1: 15k unpaid (Client A, Proj X)
        $inv1 = Invoice::create([
            'company_id' => $company->id, 'branch_id' => $branch->id, 'client_id' => $clientA->id, 'project_id' => $projectX->id,
            'invoice_number' => 'INV-01-' . Str::random(4), 'issue_date' => '2026-05-05', 'due_date' => '2026-06-05',
            'total_amount' => 15000, 'balance_due' => 15000, 'status' => 'Sent'
        ]);
        
        // Invoice 2: 5k unpaid (Client B, no proj)
        $inv2 = Invoice::create([
            'company_id' => $company->id, 'branch_id' => $branch->id, 'client_id' => $clientB->id, 'project_id' => null,
            'invoice_number' => 'INV-02-' . Str::random(4), 'issue_date' => '2026-05-06', 'due_date' => '2026-06-06',
            'total_amount' => 5000, 'balance_due' => 5000, 'status' => 'Sent'
        ]);

        $filters = ['company_id' => $company->id];

        $this->info("--------------------------------------------------");
        $this->info("PHASE 1: Verify Initial Dashboard State");
        
        $cards = collect($service->cards($filters))->pluck('value', 'title')->toArray();
        $widgets = $service->widgets($filters);

        // Revenue = 30k (from ledger, invoices aren't posted to ledger in this simple test model yet, let's just test what is there)
        $this->assertFloat($cards['Total Revenue'] ?? 0, 30000, "Initial Revenue");
        $this->assertFloat($cards['Total Expenses'] ?? 0, 10000, "Initial Expenses");
        $this->assertFloat($cards['Net Profit'] ?? 0, 20000, "Initial Net Profit");
        $this->assertFloat($cards['Cash Position'] ?? 0, 20000, "Initial Cash Position");
        $this->assertFloat($cards['Outstanding Receivables'] ?? 0, 20000, "Initial Outstanding Receivables (15k + 5k)");
        // Budget Var = 50k - 10k = 40k
        $this->assertFloat(floatval(str_replace([' Variance', ','], '', $cards['Budget vs Actual'] ?? 0)), 40000, "Initial Budget Variance");
        
        $topClientA = collect($widgets['top_customers'])->where('name', 'Client Alpha')->first()['revenue'] ?? 0;
        $this->assertFloat($topClientA, 15000, "Top Customer: Client Alpha Revenue");
        
        $topProjX = collect($widgets['top_projects'])->where('name', 'Project X')->first()['revenue'] ?? 0;
        $this->assertFloat($topProjX, 15000, "Top Project: Project X Revenue");

        $this->info("--------------------------------------------------");
        $this->info("PHASE 2: Adding New Financial Data");
        $this->info("-> Paying 5k of Client A Invoice.");
        $inv1->update(['balance_due' => 10000, 'status' => 'Partially Paid']);
        
        $this->info("-> Logging 15k new Sales Revenue in Ledger.");
        $this->createEntry($company->id, $journal->id, '2026-05-10', [
            ['account' => $cash->id, 'debit' => 15000, 'credit' => 0],
            ['account' => $sales->id, 'debit' => 0, 'credit' => 15000],
        ]);
        
        $this->info("-> Logging 20k new Expenses in Ledger.");
        $this->createEntry($company->id, $journal->id, '2026-05-11', [
            ['account' => $rent->id, 'debit' => 20000, 'credit' => 0],
            ['account' => $cash->id, 'debit' => 0, 'credit' => 20000],
        ]);

        $this->info("--------------------------------------------------");
        $this->info("PHASE 3: Verify Dynamic Updates");
        
        $cards2 = collect($service->cards($filters))->pluck('value', 'title')->toArray();

        $this->assertFloat($cards2['Total Revenue'] ?? 0, 45000, "Updated Revenue (30k + 15k)");
        $this->assertFloat($cards2['Total Expenses'] ?? 0, 30000, "Updated Expenses (10k + 20k)");
        $this->assertFloat($cards2['Net Profit'] ?? 0, 15000, "Updated Net Profit (45k - 30k)");
        $this->assertFloat($cards2['Cash Position'] ?? 0, 15000, "Updated Cash Position (20k + 15k - 20k)");
        $this->assertFloat($cards2['Outstanding Receivables'] ?? 0, 15000, "Updated Outstanding Receivables (20k - 5k)");
        $this->assertFloat(floatval(str_replace([' Variance', ','], '', $cards2['Budget vs Actual'] ?? 0)), 20000, "Updated Budget Variance (50k - 30k)");

        $this->info("--------------------------------------------------");
        $this->info("Cleaning up...");
        Invoice::where('company_id', $company->id)->delete();
        Project::where('company_id', $company->id)->delete();
        Client::where('company_id', $company->id)->delete();
        Budget::where('company_id', $company->id)->delete();
        GeneralLedger::where('company_id', $company->id)->delete();
        ChartOfAccount::where('company_id', $company->id)->delete();
        AccountType::where('company_id', $company->id)->delete();
        \App\Models\Branch::where('company_id', $company->id)->delete();
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
            'reference' => 'TEST',
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
                'reference' => 'TEST',
                'description' => 'Test Line',
                'debit' => $line['debit'],
                'credit' => $line['credit'],
            ]);
        }
    }

    private function assertFloat($actualRaw, $expected, $label)
    {
        $actual = floatval(str_replace(',', '', (string)$actualRaw));
        $status = (round($actual, 2) == round($expected, 2)) ? "PASS" : "FAIL";
        $this->info("$label: Expected $expected | Actual $actual -> $status");
    }
}
