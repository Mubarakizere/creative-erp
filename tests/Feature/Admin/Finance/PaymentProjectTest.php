<?php

namespace Tests\Feature\Admin\Finance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\BankAccount;
use Spatie\Permission\Models\Role;

class PaymentProjectTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Client $client;
    protected Project $project;
    protected PaymentMethod $paymentMethod;
    protected BankAccount $bankAccount;
    protected Invoice $invoice;

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

        $this->client = Client::factory()->create([
            'company_id' => $this->company->id,
            'client_type' => 'Company',
            'company_name' => 'Kigali Heights Ltd',
            'display_name' => 'Kigali Heights Ltd',
        ]);

        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Commercial Mall Phase 1',
            'project_code' => 'PRJ-MALL-01',
            'status' => 'In Progress',
        ]);

        $this->paymentMethod = PaymentMethod::create([
            'company_id' => $this->company->id,
            'name' => 'Bank Wire',
            'code' => 'WIRE',
            'is_active' => true,
        ]);

        $this->bankAccount = BankAccount::create([
            'company_id' => $this->company->id,
            'bank_name' => 'Bank of Kigali',
            'account_number' => '000123456789',
            'account_name' => 'Main Operating Account',
            'currency' => 'RWF',
            'is_active' => true,
        ]);

        $this->invoice = Invoice::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2026-0001',
            'issue_date' => now()->subDays(5)->format('Y-m-d'),
            'due_date' => now()->addDays(25)->format('Y-m-d'),
            'subtotal' => 1500000,
            'total_amount' => 1500000,
            'paid_amount' => 0,
            'balance_due' => 1500000,
            'status' => 'Issued',
            'currency_code' => 'RWF',
        ]);
    }

    public function test_can_view_payment_create_with_project_options(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.finance.payments.create'));

        $response->assertStatus(200);
        $response->assertSee('Record Payment');
        $response->assertSee('Project');
        $response->assertSee('Commercial Mall Phase 1');
        $response->assertSee('PRJ-MALL-01');
        $response->assertSee('RWF');
    }

    public function test_can_record_payment_associated_with_project(): void
    {
        $payload = [
            'payment_number' => 'PAY-2026-9999',
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'payment_date' => now()->format('Y-m-d'),
            'amount' => 500000,
            'payment_method_id' => $this->paymentMethod->id,
            'bank_account_id' => $this->bankAccount->id,
            'reference_number' => 'TXN-BK-9999',
            'notes' => 'Advance milestone payment for foundation work',
            'allocations' => [
                [
                    'invoice_id' => $this->invoice->id,
                    'amount' => 500000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.finance.payments.store'), $payload);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('payments', [
            'payment_number' => 'PAY-2026-9999',
            'project_id' => $this->project->id,
            'client_id' => $this->client->id,
            'amount' => 500000,
            'reference_number' => 'TXN-BK-9999',
        ]);

        $payment = Payment::where('payment_number', 'PAY-2026-9999')->first();
        $this->assertNotNull($payment);
        $this->assertEquals($this->project->id, $payment->project_id);
        $this->assertEquals('Commercial Mall Phase 1', $payment->project->name);

        // Check show page
        $showResponse = $this->actingAs($this->user)->get(route('admin.finance.payments.show', $payment));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Commercial Mall Phase 1');
        $showResponse->assertSee('PRJ-MALL-01');
        $showResponse->assertSee('RWF 500,000.00');
    }

    public function test_payments_index_displays_kpis_search_and_rwf_currency(): void
    {
        Payment::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'payment_method_id' => $this->paymentMethod->id,
            'bank_account_id' => $this->bankAccount->id,
            'payment_number' => 'PAY-TEST-001',
            'reference_number' => 'REF-TEST-001',
            'amount' => 750000,
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'Completed',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.finance.payments.index'));

        $response->assertStatus(200);
        $response->assertSee('Payments');
        $response->assertSee('Total Collected (RWF)');
        $response->assertSee('RWF 750,000.00');
        $response->assertSee('Commercial Mall Phase 1');
        $response->assertSee('PRJ-MALL-01');
        $response->assertSee('REF-TEST-001');
        $response->assertSee('Search Keyword');
    }

    public function test_payments_index_search_and_project_filter(): void
    {
        $otherProject = Project::factory()->create([
            'company_id' => $this->company->id,
            'name' => 'Zenith Office Park',
            'project_code' => 'PRJ-ZENITH',
        ]);

        $p1 = Payment::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'payment_method_id' => $this->paymentMethod->id,
            'payment_number' => 'PAY-ALPHA-100',
            'reference_number' => 'REF-ALPHA-100',
            'amount' => 100000,
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'Completed',
        ]);

        $p2 = Payment::create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'project_id' => $otherProject->id,
            'payment_method_id' => $this->paymentMethod->id,
            'payment_number' => 'PAY-BETA-200',
            'reference_number' => 'REF-BETA-200',
            'amount' => 200000,
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'Completed',
        ]);

        // Search by keyword "Commercial Mall"
        $searchResponse = $this->actingAs($this->user)->get(route('admin.finance.payments.index', ['search' => 'Commercial Mall']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('PAY-ALPHA-100');
        $searchResponse->assertDontSee('PAY-BETA-200');

        // Filter by project_id
        $filterResponse = $this->actingAs($this->user)->get(route('admin.finance.payments.index', ['project_id' => $otherProject->id]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('PAY-BETA-200');
        $filterResponse->assertDontSee('PAY-ALPHA-100');
    }
}
