<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectMaterialRequest;
use App\Models\User;
use App\Models\Client;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProjectMaterialRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->company = Company::factory()->create();
        $this->user->update(['company_id' => $this->company->id]);
        
        $this->user->assignRole('Super Admin');
    }

    protected function createDependencies()
    {
        $branch = \App\Models\Branch::factory()->create(['company_id' => $this->company->id]);
        
        $client = Client::create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id,
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'client_type' => 'Company',
            'phone' => '1234567890',
        ]);

        $project = Project::create([
            'company_id' => $this->company->id,
            'branch_id' => $branch->id,
            'client_id' => $client->id,
            'project_manager_id' => $this->user->id,
            'name' => 'Test Project',
            'project_code' => 'PRJ-001',
            'status' => 'In Progress',
            'start_date' => now(),
        ]);

        $category = ProductCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Test Category',
            'type' => 'Goods',
        ]);

        $product = Product::create([
            'company_id' => $this->company->id,
            'product_category_id' => $category->id,
            'name' => 'Test Product',
            'sku' => 'PRD-001',
            'type' => 'Goods',
            'status' => 'active',
        ]);

        return [$project, $product];
    }

    public function test_can_view_material_requests_index()
    {
        $response = $this->actingAs($this->user)->get(route('admin.material-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.material-requests.index');
    }

    public function test_can_create_material_request()
    {
        [$project, $product] = $this->createDependencies();

        $data = [
            'project_id' => $project->id,
            'request_date' => now()->toDateString(),
            'priority' => 'High',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_requested' => 10,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('admin.material-requests.store'), $data);

        $request = ProjectMaterialRequest::first();
        $response->assertRedirect(route('admin.material-requests.show', $request));
        
        $this->assertDatabaseHas('project_material_requests', [
            'project_id' => $project->id,
            'company_id' => $this->company->id,
            'priority' => 'High',
            'status' => 'Draft'
        ]);

        $this->assertDatabaseHas('project_material_request_items', [
            'project_material_request_id' => $request->id,
            'product_id' => $product->id,
            'quantity_requested' => 10
        ]);
    }

    public function test_can_update_material_request()
    {
        [$project, $product] = $this->createDependencies();

        $request = ProjectMaterialRequest::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'requested_by' => $this->user->id,
            'request_number' => 'MR-TEST-01',
            'request_date' => now()->toDateString(),
            'status' => 'Draft'
        ]);

        $request->items()->create([
            'product_id' => $product->id,
            'quantity_requested' => 5
        ]);

        $updateData = [
            'project_id' => $project->id,
            'request_date' => now()->toDateString(),
            'priority' => 'Urgent',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_requested' => 15,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->put(route('admin.material-requests.update', $request), $updateData);

        $response->assertRedirect(route('admin.material-requests.show', $request));

        $this->assertDatabaseHas('project_material_requests', [
            'id' => $request->id,
            'priority' => 'Urgent'
        ]);

        $this->assertDatabaseHas('project_material_request_items', [
            'project_material_request_id' => $request->id,
            'product_id' => $product->id,
            'quantity_requested' => 15
        ]);
    }

    public function test_can_submit_material_request()
    {
        [$project, $product] = $this->createDependencies();
        
        $request = ProjectMaterialRequest::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'requested_by' => $this->user->id,
            'request_number' => 'MR-TEST-02',
            'request_date' => now()->toDateString(),
            'status' => 'Draft'
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.material-requests.submit', $request));
        
        $response->assertRedirect(route('admin.material-requests.show', $request));
        
        $this->assertDatabaseHas('project_material_requests', [
            'id' => $request->id,
            'status' => 'Submitted'
        ]);
    }

    public function test_can_approve_material_request()
    {
        [$project, $product] = $this->createDependencies();
        
        $request = ProjectMaterialRequest::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'requested_by' => $this->user->id,
            'request_number' => 'MR-TEST-03',
            'request_date' => now()->toDateString(),
            'status' => 'Submitted'
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.material-requests.approve', $request));
        
        $response->assertRedirect(route('admin.material-requests.show', $request));
        
        $this->assertDatabaseHas('project_material_requests', [
            'id' => $request->id,
            'status' => 'Approved'
        ]);
    }

    public function test_can_view_create_page_with_companies_and_projects_data()
    {
        [$project, $product] = $this->createDependencies();

        $response = $this->actingAs($this->user)->get(route('admin.material-requests.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.projects.material-requests.create');
        $response->assertViewHas('companies');
        $response->assertViewHas('projectsData');
        $response->assertSee('Select a Company');
        $response->assertSee($this->company->name);
    }

    public function test_can_create_material_request_with_explicit_company_id()
    {
        [$project, $product] = $this->createDependencies();
        $otherCompany = Company::factory()->create(['name' => 'Secondary Enterprise']);

        $data = [
            'company_id' => $otherCompany->id,
            'project_id' => $project->id,
            'request_date' => now()->toDateString(),
            'priority' => 'High',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_requested' => 20,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('admin.material-requests.store'), $data);

        $request = ProjectMaterialRequest::where('company_id', $otherCompany->id)->first();
        $this->assertNotNull($request);
        $response->assertRedirect(route('admin.material-requests.show', $request));
        
        $this->assertDatabaseHas('project_material_requests', [
            'id' => $request->id,
            'project_id' => $project->id,
            'company_id' => $otherCompany->id,
        ]);
    }

    public function test_can_update_material_request_company()
    {
        [$project, $product] = $this->createDependencies();
        $otherCompany = Company::factory()->create(['name' => 'Third Enterprise']);

        $request = ProjectMaterialRequest::create([
            'company_id' => $this->company->id,
            'project_id' => $project->id,
            'requested_by' => $this->user->id,
            'request_number' => 'MR-TEST-04',
            'request_date' => now()->toDateString(),
            'status' => 'Draft'
        ]);

        $updateData = [
            'company_id' => $otherCompany->id,
            'project_id' => $project->id,
            'request_date' => now()->toDateString(),
            'priority' => 'Normal',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity_requested' => 8,
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->put(route('admin.material-requests.update', $request), $updateData);

        $response->assertRedirect(route('admin.material-requests.show', $request));

        $this->assertDatabaseHas('project_material_requests', [
            'id' => $request->id,
            'company_id' => $otherCompany->id,
        ]);
    }
}
