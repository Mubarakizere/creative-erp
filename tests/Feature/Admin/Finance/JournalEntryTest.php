<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use Spatie\Permission\Models\Role;

class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Project $project;
    protected AccountType $accountType;
    protected ChartOfAccount $accountCash;
    protected ChartOfAccount $accountExpense;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create(['name' => 'Creative Engineering Co']);
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);

        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Kigali Skyway Overpass',
            'project_code' => 'PRJ-SKY-01',
            'status' => 'In Progress',
        ]);

        $this->accountType = AccountType::create([
            'company_id' => $this->company->id,
            'name' => 'Current Assets',
            'code' => 'ASSET',
            'is_active' => true,
        ]);

        $this->accountCash = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $this->accountType->id,
            'code' => '1010',
            'name' => 'Main Cash Account',
            'is_active' => true,
        ]);

        $this->accountExpense = ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $this->accountType->id,
            'code' => '5010',
            'name' => 'Site Operating Expenses',
            'is_active' => true,
        ]);
    }

    public function test_user_can_view_journal_create_page_with_company_and_project_options(): void
    {
        $client = \App\Models\Client::factory()->create([
            'company_id' => $this->company->id,
            'display_name' => 'Acme Corporation',
        ]);
        $this->project->update(['client_id' => $client->id]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.accounting.journals.create'));

        $response->assertStatus(200);
        $response->assertSee('New Journal Entry');
        $response->assertSee('Company');
        $response->assertSee('Creative Engineering Co');
        $response->assertSee('Project');
        $response->assertSee('Kigali Skyway Overpass');
        $response->assertSee('PRJ-SKY-01');
        $response->assertSee('RWF');
        // Fiscal year field should not be present
        $response->assertDontSee('name="fiscal_year_id"', false);
    }

    public function test_user_can_create_journal_entry_with_company_and_project(): void
    {
        $payload = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'journal_number' => 'JRN-2026-TEST-001',
            'date' => now()->format('Y-m-d'),
            'reference_number' => 'VOUCH-888',
            'memo' => 'Project materials site expense adjustment',
            'entries' => [
                [
                    'chart_of_account_id' => $this->accountExpense->id,
                    'description' => 'Site material adjustment',
                    'debit' => 250000,
                    'credit' => 0,
                ],
                [
                    'chart_of_account_id' => $this->accountCash->id,
                    'description' => 'Cash disbursement for site materials',
                    'debit' => 0,
                    'credit' => 250000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.finance.accounting.journals.store'), $payload);

        $response->assertRedirect(route('admin.finance.accounting.journals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('journals', [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'journal_number' => 'JRN-2026-TEST-001',
            'reference_number' => 'VOUCH-888',
            'total_debit' => 250000,
            'total_credit' => 250000,
            'status' => 'Draft',
        ]);

        $journal = Journal::where('journal_number', 'JRN-2026-TEST-001')->first();
        $this->assertNotNull($journal);
        $this->assertEquals($this->project->id, $journal->project_id);
        $this->assertEquals('Kigali Skyway Overpass', $journal->project->name);

        // Check show page displays project and company
        $showResponse = $this->actingAs($this->user)->get(route('admin.finance.accounting.journals.show', $journal));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Kigali Skyway Overpass');
        $showResponse->assertSee('PRJ-SKY-01');
        $showResponse->assertSee('Creative Engineering Co');
        $showResponse->assertSee('RWF 250,000.00');
    }

    public function test_journal_entry_fails_if_unbalanced(): void
    {
        $payload = [
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'journal_number' => 'JRN-UNBALANCED-01',
            'date' => now()->format('Y-m-d'),
            'memo' => 'Unbalanced entry test',
            'entries' => [
                [
                    'chart_of_account_id' => $this->accountExpense->id,
                    'description' => 'Line 1',
                    'debit' => 500000,
                    'credit' => 0,
                ],
                [
                    'chart_of_account_id' => $this->accountCash->id,
                    'description' => 'Line 2',
                    'debit' => 0,
                    'credit' => 300000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.finance.accounting.journals.store'), $payload);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('journals', [
            'journal_number' => 'JRN-UNBALANCED-01',
        ]);
    }

    public function test_journals_index_displays_project_and_company_and_filters(): void
    {
        $otherProject = Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Nyabugogo Bus Terminal',
            'project_code' => 'PRJ-NYA-02',
        ]);

        $j1 = Journal::create([
            'company_id' => $this->company->id,
            'project_id' => $this->project->id,
            'journal_number' => 'JRN-ALPHA',
            'date' => now()->format('Y-m-d'),
            'memo' => 'Alpha skyway project journal',
            'status' => 'Draft',
            'total_debit' => 100000,
            'total_credit' => 100000,
        ]);

        $j2 = Journal::create([
            'company_id' => $this->company->id,
            'project_id' => $otherProject->id,
            'journal_number' => 'JRN-BETA',
            'date' => now()->format('Y-m-d'),
            'memo' => 'Beta bus terminal journal',
            'status' => 'Draft',
            'total_debit' => 200000,
            'total_credit' => 200000,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.accounting.journals.index'));
        $response->assertStatus(200);
        $response->assertSee('JRN-ALPHA');
        $response->assertSee('Kigali Skyway Overpass');
        $response->assertSee('PRJ-SKY-01');
        $response->assertSee('JRN-BETA');
        $response->assertSee('Nyabugogo Bus Terminal');

        // Filter by project_id
        $filterResponse = $this->actingAs($this->user)->get(route('admin.finance.accounting.journals.index', [
            'project_id' => $this->project->id,
        ]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('JRN-ALPHA');
        $filterResponse->assertDontSee('JRN-BETA');
    }
}
