<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Services\Finance\AccountingService;
use App\Services\Finance\JournalService;
use App\Services\Finance\FiscalPeriodService;
use App\Services\Finance\AccountingReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AccountingFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'is_active' => true
        ]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id
        ]);
        
        $this->actingAs($this->user);
    }

    public function test_can_create_account_types_and_chart_of_accounts()
    {
        $accountingService = app(AccountingService::class);
        
        $type = $accountingService->createAccountType([
            'company_id' => $this->company->id,
            'name' => 'Asset',
            'code' => 'AST'
        ]);

        $this->assertDatabaseHas('account_types', ['id' => $type->id]);

        $account = $accountingService->createChartOfAccount([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '1000',
            'name' => 'Cash in Bank',
        ]);

        $this->assertDatabaseHas('chart_of_accounts', ['id' => $account->id]);
    }

    public function test_journal_enforces_double_entry()
    {
        $journalService = app(JournalService::class);
        $accountingService = app(AccountingService::class);
        $type = $accountingService->createAccountType([
            'company_id' => $this->company->id,
            'name' => 'Asset',
            'code' => 'AST'
        ]);
        $cashAccount = $accountingService->createChartOfAccount([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '1000',
            'name' => 'Cash',
        ]);
        $revenueAccount = $accountingService->createChartOfAccount([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '4000',
            'name' => 'Revenue',
        ]);

        // Expect Exception for Unbalanced Journal
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Journal entries must be balanced.');

        $journalService->createManualJournal([
            'company_id' => $this->company->id,
            'date' => now(),
            'memo' => 'Test',
        ], [
            [
                'chart_of_account_id' => $cashAccount->id,
                'debit' => 100,
                'credit' => 0,
            ],
            [
                'chart_of_account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => 50, // Unbalanced
            ]
        ]);
    }

    public function test_can_create_and_post_balanced_journal()
    {
        $journalService = app(JournalService::class);
        $accountingService = app(AccountingService::class);
        $type = $accountingService->createAccountType([
            'company_id' => $this->company->id,
            'name' => 'Asset',
            'code' => 'AST'
        ]);
        $cashAccount = $accountingService->createChartOfAccount([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '1000',
            'name' => 'Cash',
        ]);
        $revenueAccount = $accountingService->createChartOfAccount([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '4000',
            'name' => 'Revenue',
        ]);

        $journal = $journalService->createManualJournal([
            'company_id' => $this->company->id,
            'date' => now(),
            'memo' => 'Test',
        ], [
            [
                'chart_of_account_id' => $cashAccount->id,
                'debit' => 100,
                'credit' => 0,
            ],
            [
                'chart_of_account_id' => $revenueAccount->id,
                'debit' => 0,
                'credit' => 100, // Balanced
            ]
        ]);

        $this->assertDatabaseHas('journals', ['id' => $journal->id, 'status' => 'Draft']);

        $journalService->postJournal($journal);

        $this->assertDatabaseHas('journals', ['id' => $journal->id, 'status' => 'Posted']);
        
        // Ensure Ledger was updated
        $this->assertDatabaseHas('general_ledgers', [
            'journal_entry_id' => $journal->entries->first()->id,
            'debit' => 100
        ]);
    }

    public function test_can_create_fiscal_periods()
    {
        $fiscalService = app(FiscalPeriodService::class);
        
        $year = $fiscalService->createFiscalYear([
            'company_id' => $this->company->id,
            'name' => 'FY2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31'
        ]);

        $this->assertDatabaseHas('fiscal_years', ['id' => $year->id]);

        $period = $fiscalService->createAccountingPeriod([
            'company_id' => $this->company->id,
            'fiscal_year_id' => $year->id,
            'name' => 'Jan 2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-31'
        ]);

        $this->assertDatabaseHas('accounting_periods', ['id' => $period->id]);
        
        $fiscalService->closeAccountingPeriod($period);
        $this->assertDatabaseHas('accounting_periods', ['id' => $period->id, 'status' => 'Closed']);
    }
}
