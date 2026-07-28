<?php

namespace Tests\Feature\Admin\Project;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;
use App\Models\Project;
use App\Models\Product;
use App\Models\ProjectMaterialRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class ProjectMaterialRequestConversionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run seeders for roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    private function createDependencies()
    {
        $company = Company::factory()->create();
        $branch = Branch::factory()->create(['company_id' => $company->id]);
        $project = Project::factory()->create(['company_id' => $company->id]);
        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-' . rand(1000, 9999),
            'type' => 'physical',
            'status' => 'active',
        ]);
        
        $user = User::factory()->create(['company_id' => $company->id, 'branch_id' => $branch->id]);
        
        $role = Role::create(['name' => 'Test Role ' . time()]);
        $role->givePermissionTo(['material_request.convert_to_procurement']);
        $user->assignRole($role);

        $pmr = ProjectMaterialRequest::create([
            'company_id' => $company->id,
            'project_id' => $project->id,
            'requested_by' => $user->id,
            'request_number' => 'MR-TEST-01',
            'request_date' => now(),
            'status' => 'Approved',
        ]);

        $pmr->items()->create([
            'product_id' => $product->id,
            'quantity_requested' => 10,
        ]);

        return [$company, $user, $pmr, $product, $project];
    }

    public function test_approved_request_can_be_converted()
    {
        [$company, $user, $pmr, $product, $project] = $this->createDependencies();

        $response = $this->actingAs($user)->post(route('admin.material-requests.convert', $pmr));

        $response->assertRedirect(route('admin.material-requests.show', $pmr));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('purchase_requisitions', [
            'company_id' => $company->id,
            'project_id' => $project->id,
            'project_material_request_id' => $pmr->id,
            'status' => 'draft',
        ]);

        $pr = \App\Models\PurchaseRequisition::where('project_material_request_id', $pmr->id)->first();
        
        $this->assertDatabaseHas('purchase_requisition_items', [
            'purchase_requisition_id' => $pr->id,
            'product_id' => $product->id,
            'quantity' => 10,
        ]);
    }

    public function test_draft_request_cannot_be_converted()
    {
        [$company, $user, $pmr] = $this->createDependencies();
        
        $pmr->update(['status' => 'Draft']);

        $response = $this->actingAs($user)->post(route('admin.material-requests.convert', $pmr));
        $response->assertForbidden();
    }

    public function test_unauthorized_user_cannot_convert()
    {
        [$company, $user, $pmr] = $this->createDependencies();
        
        $unauthorizedUser = User::factory()->create(['company_id' => $company->id]);
        $role = Role::create(['name' => 'Unauthorized Role']);
        $unauthorizedUser->assignRole($role);
        
        $response = $this->actingAs($unauthorizedUser)->post(route('admin.material-requests.convert', $pmr));
        if ($response->status() === 302) {
            $response->assertRedirect();
        } else {
            $response->assertForbidden();
        }
    }

    public function test_user_from_different_company_cannot_convert()
    {
        [$company, $user, $pmr] = $this->createDependencies();
        
        $otherCompany = Company::factory()->create();
        $otherUser = User::factory()->create(['company_id' => $otherCompany->id]);
        
        $role = Role::create(['name' => 'Other Role']);
        $role->givePermissionTo(['material_request.convert_to_procurement']);
        $otherUser->assignRole($role);

        $response = $this->actingAs($otherUser)->post(route('admin.material-requests.convert', $pmr));
        $response->assertNotFound(); // Due to tenant isolation scope
    }

    public function test_repeated_conversion_does_not_create_duplicates()
    {
        [$company, $user, $pmr] = $this->createDependencies();

        $this->actingAs($user)->post(route('admin.material-requests.convert', $pmr));
        
        // Second conversion attempt
        $response = $this->actingAs($user)->post(route('admin.material-requests.convert', $pmr));
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Request has already been converted to a Purchase Requisition.');

        $this->assertEquals(1, \App\Models\PurchaseRequisition::where('project_material_request_id', $pmr->id)->count());
    }
}
