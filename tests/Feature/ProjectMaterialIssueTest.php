<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\ChartOfAccount;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProjectMaterialIssueTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $project;
    protected $warehouse;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        $permission = Permission::firstOrCreate(['name' => 'create_project_material_issue', 'guard_name' => 'web']);
        $role = Role::firstOrCreate(['name' => 'admin', 'company_id' => $this->company->id, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
        $this->user->assignRole($role);

        $this->project = Project::factory()->create([
            'company_id' => $this->company->id,
            'actual_cost' => 0
        ]);

        $this->warehouse = app(Warehouse::class)->create([
            'company_id' => $this->company->id,
            'name' => 'Test Warehouse',
            'code' => 'WH01',
            'status' => 'Active'
        ]);
        
        $this->product = app(Product::class)->create([
            'company_id' => $this->company->id,
            'name' => 'Test Product',
            'sku' => 'PRD-01',
            'product_type' => 'Goods',
            'valuation_method' => 'Standard Cost',
            'cost_price' => 10,
            'allow_negative_stock' => false
        ]);

        // Setup Accounts
        ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '5100', 'type' => 'expense']);
        ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'code' => '1200', 'type' => 'asset']);
    }

    public function test_authorized_user_can_create_material_issue()
    {
        // Add Stock
        $inventoryEngine = app(\App\Services\Inventory\InventoryEngine::class);
        $inventoryEngine->stockIn($this->product, $this->warehouse, 100, 'initial_stock', null, null, null, $this->user->id, 10);

        $this->actingAs($this->user);

        $response = $this->post(route('admin.project-material-issues.store'), [
            'project_id' => $this->project->id,
            'warehouse_id' => $this->warehouse->id,
            'issue_number' => 'PMI-001',
            'issue_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 30
                ]
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('project_material_issues', [
            'issue_number' => 'PMI-001',
            'project_id' => $this->project->id,
            'warehouse_id' => $this->warehouse->id
        ]);

        $this->assertDatabaseHas('project_material_issue_items', [
            'product_id' => $this->product->id,
            'quantity' => 30,
            'unit_cost' => 10,
            'total_cost' => 300
        ]);

        $this->assertEquals(70, Inventory::where('product_id', $this->product->id)->first()->available_quantity);
        $this->assertEquals(300, $this->project->fresh()->actual_cost);
    }

    public function test_cannot_issue_more_than_available_stock()
    {
        $inventoryEngine = app(\App\Services\Inventory\InventoryEngine::class);
        $inventoryEngine->stockIn($this->product, $this->warehouse, 20, 'initial_stock', null, null, null, $this->user->id, 10);

        $this->actingAs($this->user);

        $response = $this->post(route('admin.project-material-issues.store'), [
            'project_id' => $this->project->id,
            'warehouse_id' => $this->warehouse->id,
            'issue_number' => 'PMI-002',
            'issue_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 30
                ]
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(20, Inventory::where('product_id', $this->product->id)->first()->available_quantity);
    }

    public function test_cross_company_isolation()
    {
        $otherCompany = Company::factory()->create();
        $otherProject = Project::factory()->create(['company_id' => $otherCompany->id]);
        
        $inventoryEngine = app(\App\Services\Inventory\InventoryEngine::class);
        $inventoryEngine->stockIn($this->product, $this->warehouse, 100, 'initial_stock', null, null, null, $this->user->id, 10);

        $this->actingAs($this->user);

        $response = $this->post(route('admin.project-material-issues.store'), [
            'project_id' => $otherProject->id,
            'warehouse_id' => $this->warehouse->id,
            'issue_number' => 'PMI-003',
            'issue_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 30
                ]
            ]
        ]);

        $response->assertSessionHasErrors(['project_id']);
    }
}
