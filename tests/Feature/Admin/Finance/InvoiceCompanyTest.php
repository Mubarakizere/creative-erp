<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Project;
use App\Models\Invoice;
use Spatie\Permission\Models\Role;

class InvoiceCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $companyA;
    protected Company $companyB;
    protected Client $clientA;
    protected Client $clientB;
    protected Project $projectA;
    protected Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::factory()->create(['name' => 'Apex Construction Co', 'status' => 'active']);
        $this->companyB = Company::factory()->create(['name' => 'Zenith Building Ltd', 'status' => 'active']);

        $branchA = Branch::factory()->create(['company_id' => $this->companyA->id]);

        $this->user = User::factory()->create([
            'company_id' => $this->companyA->id,
            'branch_id' => $branchA->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);

        $this->clientA = Client::factory()->create([
            'company_id' => $this->companyA->id,
            'company_name' => 'Client Alpha LLC',
        ]);

        $this->clientB = Client::factory()->create([
            'company_id' => $this->companyB->id,
            'company_name' => 'Client Beta Corp',
        ]);

        $this->projectA = Project::factory()->create([
            'company_id' => $this->companyA->id,
            'client_id' => $this->clientA->id,
            'name' => 'Alpha Commercial Plaza',
            'project_code' => 'PRJ-ALPHA',
        ]);

        $this->projectB = Project::factory()->create([
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'name' => 'Beta Residence Towers',
            'project_code' => 'PRJ-BETA',
        ]);
    }

    public function test_user_can_view_invoice_create_page_with_companies()
    {
        $response = $this->actingAs($this->user)->get(route('admin.finance.invoices.create'));

        $response->assertOk();
        $response->assertViewHas('companies');
        $response->assertViewHas('projectsData');
        $response->assertSee('Apex Construction Co');
        $response->assertSee('Zenith Building Ltd');
        $response->assertSee('Company');
    }

    public function test_user_can_create_invoice_with_explicit_company()
    {
        $payload = [
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'invoice_number' => 'INV-ZENITH-001',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'items' => [
                [
                    'description' => 'Architectural Drafting Phase 1',
                    'quantity' => 1,
                    'unit_price' => 5000,
                ],
                [
                    'description' => 'Structural Engineering Review',
                    'quantity' => 2,
                    'unit_price' => 2500,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.finance.invoices.store'), $payload);

        $invoice = Invoice::where('invoice_number', 'INV-ZENITH-001')->first();
        $this->assertNotNull($invoice);

        $response->assertRedirect(route('admin.finance.invoices.show', $invoice));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'invoice_number' => 'INV-ZENITH-001',
            'total_amount' => 10000,
        ]);
    }

    public function test_user_can_view_invoice_edit_page_with_company_selected()
    {
        $invoice = Invoice::create([
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'invoice_number' => 'INV-EDIT-001',
            'status' => 'Draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 5000,
            'balance_due' => 5000,
        ]);

        $invoice->items()->create([
            'description' => 'Initial Assessment',
            'quantity' => 1,
            'unit_price' => 5000,
            'total_amount' => 5000,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.invoices.edit', $invoice));

        $response->assertOk();
        $response->assertViewHas('companies');
        $response->assertSee('Zenith Building Ltd');
        $response->assertSee('INV-EDIT-001');
    }

    public function test_user_can_update_invoice_company()
    {
        $invoice = Invoice::create([
            'company_id' => $this->companyA->id,
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'invoice_number' => 'INV-UPDATE-001',
            'status' => 'Draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 4000,
            'balance_due' => 4000,
        ]);

        $invoice->items()->create([
            'description' => 'Phase 1 Works',
            'quantity' => 1,
            'unit_price' => 4000,
            'total_amount' => 4000,
        ]);

        $updatePayload = [
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(45)->format('Y-m-d'),
            'items' => [
                [
                    'description' => 'Phase 1 Updated Works',
                    'quantity' => 2,
                    'unit_price' => 3000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->put(route('admin.finance.invoices.update', $invoice), $updatePayload);

        $response->assertRedirect(route('admin.finance.invoices.show', $invoice));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'total_amount' => 6000,
        ]);
    }

    public function test_invoice_index_displays_company_and_filters_by_company()
    {
        Invoice::create([
            'company_id' => $this->companyA->id,
            'client_id' => $this->clientA->id,
            'invoice_number' => 'INV-APEX-101',
            'status' => 'Draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 1500,
            'balance_due' => 1500,
        ]);

        Invoice::create([
            'company_id' => $this->companyB->id,
            'client_id' => $this->clientB->id,
            'invoice_number' => 'INV-ZENITH-202',
            'status' => 'Draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => 2500,
            'balance_due' => 2500,
        ]);

        // General index
        $response = $this->actingAs($this->user)->get(route('admin.finance.invoices.index'));
        $response->assertOk();
        $response->assertSee('INV-APEX-101');
        $response->assertSee('INV-ZENITH-202');
        $response->assertSee('Apex Construction Co');
        $response->assertSee('Zenith Building Ltd');

        // Filter by Company A
        $responseCompanyA = $this->actingAs($this->user)->get(route('admin.finance.invoices.index', [
            'company_id' => $this->companyA->id
        ]));
        $responseCompanyA->assertOk();
        $responseCompanyA->assertSee('INV-APEX-101');
        $responseCompanyA->assertDontSee('INV-ZENITH-202');
    }
}
