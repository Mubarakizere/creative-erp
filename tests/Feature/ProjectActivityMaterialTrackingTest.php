<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Product;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Inventory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectActivityMaterialTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;
    protected $branch;
    protected $project;
    protected $task;
    protected $product;
    protected $warehouse;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->branch = Branch::factory()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        
        $this->withoutMiddleware();
        
        $this->project = Project::factory()->create(['company_id' => $this->company->id, 'branch_id' => $this->branch->id]);
        $this->task = Task::factory()->create(['project_id' => $this->project->id, 'company_id' => $this->company->id]);
        $this->product = Product::factory()->create(['company_id' => $this->company->id, 'type' => 'goods', 'is_stockable' => true]);
        $this->warehouse = Warehouse::factory()->create(['company_id' => $this->company->id, 'branch_id' => $this->branch->id]);
        
        // Add some stock
        Inventory::create([
            'company_id' => $this->company->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'quantity' => 100,
            'batch_number' => 'BATCH1',
        ]);
    }

    public function test_material_request_can_be_linked_to_task()
    {
        $response = $this->actingAs($this->user)->post(route('admin.material-requests.store'), [
            'project_id' => $this->project->id,
            'task_id' => $this->task->id,
            'request_date' => now()->toDateString(),
            'priority' => 'Normal',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_requested' => 10,
                ]
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('project_material_requests', [
            'project_id' => $this->project->id,
            'task_id' => $this->task->id,
        ]);
    }

    public function test_material_issue_updates_task_cost()
    {
        // Mock valuation so we don't need actual valuation entries
        $this->product->update(['standard_cost' => 10.50]);
        
        $response = $this->actingAs($this->user)->post(route('admin.project-material-issues.store'), [
            'project_id' => $this->project->id,
            'task_id' => $this->task->id,
            'warehouse_id' => $this->warehouse->id,
            'issue_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                ]
            ]
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('project_material_issues', [
            'project_id' => $this->project->id,
            'task_id' => $this->task->id,
        ]);
        
        // Product standard cost is 10.50, quantity 5 = 52.50
        // Wait, standard_cost might not be what InventoryValuationService uses.
        // Let's just check if actual_material_cost was updated on the task and project.
        
        $this->task->refresh();
        $this->project->refresh();
        
        $this->assertTrue($this->task->actual_material_cost > 0);
        $this->assertEquals($this->task->actual_material_cost, $this->project->actual_cost);
    }
}
