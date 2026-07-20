<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);
        $this->user->assignRole('Super Admin');

        $this->company = Company::factory()->create();
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_branches(): void
    {
        $response = $this->get(route('admin.branches.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_branches(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.branches.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.branches.index');
    }

    /*
    |--------------------------------------------------------------------------
    | List Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_list_shows_branches(): void
    {
        Branch::factory()->count(3)->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.index'));

        $response->assertStatus(200);
        $response->assertViewHas('branches');
    }

    public function test_branch_list_search_works(): void
    {
        Branch::factory()->create(['name' => 'Alpha Office', 'company_id' => $this->company->id]);
        Branch::factory()->create(['name' => 'Beta Office', 'company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.index', [
            'search' => 'Alpha',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Office');
    }

    public function test_branch_list_status_filter_works(): void
    {
        Branch::factory()->create(['name' => 'Active Branch', 'status' => 'active', 'company_id' => $this->company->id]);
        Branch::factory()->create(['name' => 'Inactive Branch', 'status' => 'inactive', 'company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.index', [
            'status' => 'active',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Branch');
        $response->assertDontSee('Inactive Branch');
    }

    public function test_branch_list_company_filter_works(): void
    {
        $otherCompany = Company::factory()->create();

        Branch::factory()->create(['name' => 'My Branch', 'company_id' => $this->company->id]);
        Branch::factory()->create(['name' => 'Other Branch', 'company_id' => $otherCompany->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.index', [
            'company_id' => $this->company->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('My Branch');
        $response->assertDontSee('Other Branch');
    }

    /*
    |--------------------------------------------------------------------------
    | Create Tests
    |--------------------------------------------------------------------------
    */

    public function test_create_branch_form_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.branches.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.branches.create');
    }

    public function test_branch_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'code' => 'TB-001',
            'email' => 'test@branch.com',
            'phone' => '+971 4 123 4567',
            'manager_name' => 'John Doe',
            'country' => 'United Arab Emirates',
            'city' => 'Dubai',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('branches', [
            'name' => 'Test Branch',
            'code' => 'TB-001',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_branch_uuid_is_generated_automatically(): void
    {
        $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'UUID Test Branch',
            'code' => 'UT-001',
        ]);

        $branch = Branch::where('code', 'UT-001')->first();

        $this->assertNotNull($branch->uuid);
        $this->assertTrue(strlen($branch->uuid) === 36);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'code' => 'BR-001',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_branch_code_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_branch_company_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'name' => 'Test Branch',
            'code' => 'BR-001',
        ]);

        $response->assertSessionHasErrors('company_id');
    }

    public function test_branch_name_must_be_unique_within_company(): void
    {
        Branch::factory()->create([
            'name' => 'Unique Branch',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Unique Branch',
            'code' => 'BR-NEW',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_branch_code_must_be_unique_within_company(): void
    {
        Branch::factory()->create([
            'code' => 'BR-001',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'New Branch',
            'code' => 'BR-001',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_branch_name_can_duplicate_across_companies(): void
    {
        $otherCompany = Company::factory()->create();

        Branch::factory()->create([
            'name' => 'Head Office',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $otherCompany->id,
            'name' => 'Head Office',
            'code' => 'HQ-002',
        ]);

        $response->assertSessionDoesntHaveErrors('name');
    }

    public function test_branch_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'code' => 'BR-001',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_branch_latitude_must_be_numeric(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'code' => 'BR-001',
            'latitude' => 'invalid',
        ]);

        $response->assertSessionHasErrors('latitude');
    }

    public function test_branch_longitude_must_be_numeric(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.branches.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Branch',
            'code' => 'BR-001',
            'longitude' => 'invalid',
        ]);

        $response->assertSessionHasErrors('longitude');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Tests
    |--------------------------------------------------------------------------
    */

    public function test_edit_branch_form_is_displayed(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.edit', $branch));

        $response->assertStatus(200);
        $response->assertViewIs('admin.branches.edit');
    }

    public function test_branch_can_be_updated(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->put(route('admin.branches.update', $branch), [
            'company_id' => $this->company->id,
            'name' => 'Updated Branch Name',
            'code' => 'UB-001',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $branch->refresh();
        $this->assertEquals('Updated Branch Name', $branch->name);
        $this->assertEquals('UB-001', $branch->code);
    }

    public function test_branch_unique_validation_ignores_self_on_update(): void
    {
        $branch = Branch::factory()->create([
            'name' => 'My Branch',
            'code' => 'MB-001',
            'company_id' => $this->company->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.branches.update', $branch), [
            'company_id' => $this->company->id,
            'name' => 'My Branch',
            'code' => 'MB-001',
        ]);

        $response->assertSessionDoesntHaveErrors(['name', 'code']);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_soft_deleted(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->delete(route('admin.branches.destroy', $branch));

        $response->assertRedirect(route('admin.branches.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('branches', ['id' => $branch->id]);
    }

    public function test_soft_deleted_branch_still_exists_in_database(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->actingAs($this->user)->delete(route('admin.branches.destroy', $branch));

        $this->assertDatabaseHas('branches', ['id' => $branch->id]);
        $this->assertNotNull(Branch::withTrashed()->find($branch->id)->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_restored(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);
        $branch->delete();

        $response = $this->actingAs($this->user)->patch(route('admin.branches.restore', $branch));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $branch->refresh();
        $this->assertNull($branch->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_can_be_activated(): void
    {
        $branch = Branch::factory()->create(['status' => 'inactive', 'company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('admin.branches.activate', $branch));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $branch->refresh();
        $this->assertEquals('active', $branch->status);
    }

    public function test_branch_can_be_deactivated(): void
    {
        $branch = Branch::factory()->create(['status' => 'active', 'company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->patch(route('admin.branches.deactivate', $branch));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $branch->refresh();
        $this->assertEquals('inactive', $branch->status);
    }

    /*
    |--------------------------------------------------------------------------
    | View Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_details_can_be_viewed(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $response = $this->actingAs($this->user)->get(route('admin.branches.show', $branch));

        $response->assertStatus(200);
        $response->assertViewIs('admin.branches.show');
        $response->assertSee($branch->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship Tests
    |--------------------------------------------------------------------------
    */

    public function test_branch_belongs_to_company(): void
    {
        $branch = Branch::factory()->create(['company_id' => $this->company->id]);

        $this->assertInstanceOf(Company::class, $branch->company);
        $this->assertEquals($this->company->id, $branch->company->id);
    }
}
