<?php

namespace Tests\Feature\Admin\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseRequisition;
use App\Models\SupplierQuotation;
use Spatie\Permission\Models\Role;

class SupplierQuotationProjectCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company1;
    protected Company $company2;
    protected Project $projectAlpha;
    protected Project $projectBeta;
    protected Product $product;
    protected Supplier $supplier;
    protected PurchaseRequisition $requisition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company1 = Company::factory()->create(['name' => 'Acme Corporation', 'status' => 'active']);
        $this->company2 = Company::factory()->create(['name' => 'Apex Build Co', 'status' => 'active']);

        $branch = Branch::factory()->create(['company_id' => $this->company1->id]);
        $this->user = User::factory()->create([
            'company_id' => $this->company1->id,
            'branch_id' => $branch->id,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->user->assignRole($superAdmin);

        $this->projectAlpha = Project::factory()->create([
            'company_id' => $this->company1->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Alpha Tower Project',
            'project_code' => 'PRJ-ALPHA',
            'status' => 'In Progress',
        ]);

        $this->projectBeta = Project::factory()->create([
            'company_id' => $this->company2->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Beta Stadium Project',
            'project_code' => 'PRJ-BETA',
            'status' => 'In Progress',
        ]);

        $this->product = Product::create([
            'company_id' => $this->company1->id,
            'name' => 'Industrial Steel Pipe 4-inch',
            'sku' => 'PIPE-ST-004',
            'type' => 'physical',
            'cost_price' => 150,
            'status' => 'active',
            'created_by' => $this->user->id,
        ]);

        $this->supplier = Supplier::create([
            'company_id' => $this->company1->id,
            'name' => 'Global Tech Supplies',
            'code' => 'SUP-001',
            'email' => 'contact@globaltech.com',
            'is_preferred' => 1,
            'status' => 'active',
        ]);

        $this->requisition = PurchaseRequisition::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'code' => 'PR-2026-ALPHA-01',
            'status' => 'approved',
            'priority' => 'high',
            'requested_by' => $this->user->id,
        ]);

        $this->requisition->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 20,
            'unit_price' => 150,
            'total_price' => 3000,
        ]);
    }

    public function test_user_can_view_create_page_with_companies_and_projects()
    {
        $response = $this->actingAs($this->user)->get(route('admin.procurement.rfqs.create'));

        $response->assertOk();
        $response->assertViewHas('companies');
        $response->assertViewHas('projects');
        $response->assertViewHas('projectsData');
        $response->assertViewHas('requisitionsData');
        $response->assertViewHas('suppliers');
        $response->assertViewHas('products');

        $response->assertSee('Acme Corporation');
        $response->assertSee('Apex Build Co');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Global Tech Supplies');
    }

    public function test_user_can_create_direct_rfq_with_project_and_company()
    {
        $payload = [
            'code' => 'RFQ-2026-DIRECT-001',
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
            'issue_date' => '2026-09-23',
            'valid_until' => '2026-10-23',
            'lead_time_days' => 14,
            'status' => 'draft',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15,
                    'unit_price' => 200,
                    'discount' => 50,
                    'tax' => 25,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.procurement.rfqs.store'), $payload);

        $response->assertRedirect(route('admin.procurement.rfqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('supplier_quotations', [
            'code' => 'RFQ-2026-DIRECT-001',
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
            'lead_time_days' => 14,
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('supplier_quotation_items', [
            'product_id' => $this->product->id,
            'quantity' => 15,
            'unit_price' => 200,
            'discount' => 50,
            'tax' => 25,
            'total' => 2975, // (15 * 200) - 50 + 25 = 2975
        ]);
    }

    public function test_user_can_create_rfq_from_purchase_requisition_inheriting_project_and_company()
    {
        $payload = [
            'code' => 'RFQ-2026-PR-001',
            'purchase_requisition_id' => $this->requisition->id,
            'supplier_id' => $this->supplier->id,
            'issue_date' => '2026-09-23',
            'valid_until' => '2026-10-30',
            'status' => 'draft',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 20,
                    'unit_price' => 145,
                    'discount' => 0,
                    'tax' => 0,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.procurement.rfqs.store'), $payload);

        $response->assertRedirect(route('admin.procurement.rfqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('supplier_quotations', [
            'code' => 'RFQ-2026-PR-001',
            'purchase_requisition_id' => $this->requisition->id,
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
        ]);
    }

    public function test_index_displays_project_and_company_and_filters_by_project()
    {
        $rfqAlpha = SupplierQuotation::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
            'code' => 'RFQ-ALPHA-111',
            'status' => 'draft',
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
        ]);
        $rfqAlpha->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_price' => 100,
            'total' => 1000,
        ]);

        $rfqBeta = SupplierQuotation::create([
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'supplier_id' => $this->supplier->id,
            'code' => 'RFQ-BETA-222',
            'status' => 'submitted',
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
        ]);
        $rfqBeta->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 200,
            'total' => 1000,
        ]);

        // General index view
        $response = $this->actingAs($this->user)->get(route('admin.procurement.rfqs.index'));
        $response->assertOk();
        $response->assertSee('RFQ-ALPHA-111');
        $response->assertSee('RFQ-BETA-222');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Acme Corporation');
        $response->assertSee('Beta Stadium Project');
        $response->assertSee('PRJ-BETA');
        $response->assertSee('Apex Build Co');

        // Filter by Project Alpha
        $responseFiltered = $this->actingAs($this->user)->get(route('admin.procurement.rfqs.index', [
            'project_id' => $this->projectAlpha->id,
        ]));
        $responseFiltered->assertOk();
        $responseFiltered->assertSee('RFQ-ALPHA-111');
        $responseFiltered->assertDontSee('RFQ-BETA-222');
    }

    public function test_show_displays_project_and_company_cards()
    {
        $rfq = SupplierQuotation::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
            'code' => 'RFQ-SHOW-001',
            'status' => 'draft',
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
        ]);
        $rfq->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_price' => 100,
            'total' => 1000,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.procurement.rfqs.show', $rfq->id));
        $response->assertOk();
        $response->assertSee('RFQ-SHOW-001');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Acme Corporation');
    }

    public function test_user_can_update_rfq_project_and_company()
    {
        $rfq = SupplierQuotation::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'supplier_id' => $this->supplier->id,
            'code' => 'RFQ-EDIT-001',
            'status' => 'draft',
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
        ]);
        $rfq->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
            'unit_price' => 100,
            'total' => 1000,
        ]);

        $editResponse = $this->actingAs($this->user)->get(route('admin.procurement.rfqs.edit', $rfq->id));
        $editResponse->assertOk();
        $editResponse->assertSee('RFQ-EDIT-001');

        $updatePayload = [
            'code' => 'RFQ-EDIT-001',
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'supplier_id' => $this->supplier->id,
            'status' => 'submitted',
            'issue_date' => '2026-09-25',
            'valid_until' => '2026-10-25',
            'lead_time_days' => 10,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 30,
                    'unit_price' => 180,
                    'discount' => 100,
                    'tax' => 50,
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->put(route('admin.procurement.rfqs.update', $rfq->id), $updatePayload);
        $response->assertRedirect(route('admin.procurement.rfqs.show', $rfq->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('supplier_quotations', [
            'id' => $rfq->id,
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'status' => 'submitted',
            'lead_time_days' => 10,
        ]);

        $this->assertDatabaseHas('supplier_quotation_items', [
            'supplier_quotation_id' => $rfq->id,
            'quantity' => 30,
            'unit_price' => 180,
            'discount' => 100,
            'tax' => 50,
            'total' => 5350, // (30 * 180) - 100 + 50 = 5350
        ]);
    }
}
