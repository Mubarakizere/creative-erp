<?php

namespace Tests\Feature\Admin\Procurement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\Product;
use App\Models\PurchaseRequisition;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PurchaseRequisitionProjectCompanyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company1;
    protected Company $company2;
    protected Project $projectAlpha;
    protected Project $projectBeta;
    protected Product $product;

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

        $permissions = [
            'procurement.view',
            'procurement.create',
            'procurement.update',
            'procurement.delete',
            'procurement.approve',
            'purchase_requisition.view',
            'purchase_requisition.create',
            'purchase_requisition.update',
            'purchase_requisition.delete',
            'purchase_requisition.approve',
        ];

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
    }

    public function test_user_can_view_create_page_with_companies_and_projects()
    {
        $response = $this->actingAs($this->user)->get(route('admin.procurement.requisitions.create'));

        $response->assertOk();
        $response->assertViewHas('companies');
        $response->assertViewHas('projects');
        $response->assertViewHas('projectsData');
        $response->assertViewHas('products');

        $response->assertSee('Acme Corporation');
        $response->assertSee('Apex Build Co');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Industrial Steel Pipe 4-inch');
    }

    public function test_user_can_create_requisition_with_project_and_company()
    {
        $payload = [
            'code' => 'PR-2026-TEST-001',
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'status' => 'submitted',
            'priority' => 'urgent',
            'required_date' => '2026-11-20',
            'notes' => 'Critical structural pipes needed on site immediately.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 25.5,
                    'description' => 'Grade A galvanized pipes',
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.procurement.requisitions.store'), $payload);

        $response->assertRedirect(route('admin.procurement.requisitions.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_requisitions', [
            'code' => 'PR-2026-TEST-001',
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'status' => 'submitted',
            'priority' => 'urgent',
            'required_date' => '2026-11-20 00:00:00',
            'notes' => 'Critical structural pipes needed on site immediately.',
            'requested_by' => $this->user->id,
        ]);

        $this->assertDatabaseHas('purchase_requisition_items', [
            'product_id' => $this->product->id,
            'quantity' => 25.5,
            'description' => 'Grade A galvanized pipes',
        ]);
    }

    public function test_index_displays_project_and_company_and_filters_by_project()
    {
        $prAlpha = PurchaseRequisition::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'code' => 'PR-ALPHA-999',
            'status' => 'draft',
            'priority' => 'normal',
            'requested_by' => $this->user->id,
        ]);
        $prAlpha->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        $prBeta = PurchaseRequisition::create([
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'code' => 'PR-BETA-888',
            'status' => 'submitted',
            'priority' => 'high',
            'requested_by' => $this->user->id,
        ]);
        $prBeta->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        // General view
        $response = $this->actingAs($this->user)->get(route('admin.procurement.requisitions.index'));
        $response->assertOk();
        $response->assertSee('PR-ALPHA-999');
        $response->assertSee('Alpha Tower Project');
        $response->assertSee('PRJ-ALPHA');
        $response->assertSee('Acme Corporation');

        // Filter by Project Alpha
        $responseFilter = $this->actingAs($this->user)->get(route('admin.procurement.requisitions.index', [
            'project_id' => $this->projectAlpha->id
        ]));
        $responseFilter->assertOk();
        $responseFilter->assertSee('PR-ALPHA-999');
        $responseFilter->assertDontSee('PR-BETA-888');
    }

    public function test_user_can_update_requisition_project_and_company()
    {
        $pr = PurchaseRequisition::create([
            'company_id' => $this->company1->id,
            'project_id' => $this->projectAlpha->id,
            'code' => 'PR-UPDATE-001',
            'status' => 'draft',
            'priority' => 'low',
            'requested_by' => $this->user->id,
        ]);
        $pr->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        $editResponse = $this->actingAs($this->user)->get(route('admin.procurement.requisitions.edit', $pr->id));
        $editResponse->assertOk();
        $editResponse->assertSee('PR-UPDATE-001');

        $updatePayload = [
            'code' => 'PR-UPDATE-001',
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'status' => 'submitted',
            'priority' => 'high',
            'required_date' => '2026-12-01',
            'notes' => 'Shifted scope to Beta project.',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 50,
                    'description' => 'Updated item description',
                ]
            ],
        ];

        $response = $this->actingAs($this->user)->put(route('admin.procurement.requisitions.update', $pr->id), $updatePayload);
        $response->assertRedirect(route('admin.procurement.requisitions.show', $pr->id));

        $this->assertDatabaseHas('purchase_requisitions', [
            'id' => $pr->id,
            'company_id' => $this->company2->id,
            'project_id' => $this->projectBeta->id,
            'status' => 'submitted',
            'priority' => 'high',
            'notes' => 'Shifted scope to Beta project.',
        ]);

        $this->assertDatabaseHas('purchase_requisition_items', [
            'purchase_requisition_id' => $pr->id,
            'quantity' => 50,
            'description' => 'Updated item description',
        ]);
    }
}
