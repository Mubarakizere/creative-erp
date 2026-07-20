<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Company $company;

    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);
        $this->user->assignRole('Super Admin');

        $this->company = Company::factory()->create();

        $this->branch = Branch::factory()->create([
            'company_id' => $this->company->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_departments(): void
    {
        $response = $this->get(route('admin.departments.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_departments(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.departments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.index');
    }

    /*
    |--------------------------------------------------------------------------
    | List Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_list_shows_departments(): void
    {
        Department::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('departments');
    }

    public function test_department_list_search_works(): void
    {
        Department::factory()->create([
            'name' => 'Engineering Department',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        Department::factory()->create([
            'name' => 'Finance Department',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.index', [
            'search' => 'Engineering',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Engineering Department');
    }

    public function test_department_list_status_filter_works(): void
    {
        Department::factory()->create([
            'name' => 'Active Dept',
            'status' => 'active',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        Department::factory()->create([
            'name' => 'Inactive Dept',
            'status' => 'inactive',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.index', [
            'status' => 'active',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Dept');
        $response->assertDontSee('Inactive Dept');
    }

    public function test_department_list_company_filter_works(): void
    {
        $otherCompany = Company::factory()->create();
        $otherBranch = Branch::factory()->create(['company_id' => $otherCompany->id]);

        Department::factory()->create([
            'name' => 'My Department',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        Department::factory()->create([
            'name' => 'Other Department',
            'company_id' => $otherCompany->id,
            'branch_id' => $otherBranch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.index', [
            'company_id' => $this->company->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('My Department');
        $response->assertDontSee('Other Department');
    }

    public function test_department_list_branch_filter_works(): void
    {
        $otherBranch = Branch::factory()->create(['company_id' => $this->company->id]);

        Department::factory()->create([
            'name' => 'HQ Department',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        Department::factory()->create([
            'name' => 'Branch Department',
            'company_id' => $this->company->id,
            'branch_id' => $otherBranch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.index', [
            'branch_id' => $this->branch->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('HQ Department');
        $response->assertDontSee('Branch Department');
    }

    /*
    |--------------------------------------------------------------------------
    | Create Tests
    |--------------------------------------------------------------------------
    */

    public function test_create_department_form_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.departments.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.create');
    }

    public function test_department_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Test Department',
            'code' => 'DEPT-TEST',
            'email' => 'test@department.com',
            'phone' => '+971 4 123 4567',
            'manager_name' => 'John Doe',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('departments', [
            'name' => 'Test Department',
            'code' => 'DEPT-TEST',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_department_uuid_is_generated_automatically(): void
    {
        $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'UUID Test Department',
            'code' => 'DEPT-UUID',
        ]);

        $department = Department::where('code', 'DEPT-UUID')->first();

        $this->assertNotNull($department->uuid);
        $this->assertTrue(strlen($department->uuid) === 36);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_department_code_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Test Department',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_department_company_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'branch_id' => $this->branch->id,
            'name' => 'Test Department',
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionHasErrors('company_id');
    }

    public function test_department_branch_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'name' => 'Test Department',
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionHasErrors('branch_id');
    }

    public function test_department_name_must_be_unique_within_branch(): void
    {
        Department::factory()->create([
            'name' => 'Unique Department',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Unique Department',
            'code' => 'DEPT-NEW',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_department_code_must_be_unique_within_company(): void
    {
        Department::factory()->create([
            'code' => 'DEPT-001',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'New Department',
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_department_name_can_duplicate_across_branches(): void
    {
        $otherBranch = Branch::factory()->create(['company_id' => $this->company->id]);

        Department::factory()->create([
            'name' => 'Engineering',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $otherBranch->id,
            'name' => 'Engineering',
            'code' => 'DEPT-ENG2',
        ]);

        $response->assertSessionDoesntHaveErrors('name');
    }

    public function test_department_code_can_duplicate_across_companies(): void
    {
        $otherCompany = Company::factory()->create();
        $otherBranch = Branch::factory()->create(['company_id' => $otherCompany->id]);

        Department::factory()->create([
            'code' => 'DEPT-001',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $otherCompany->id,
            'branch_id' => $otherBranch->id,
            'name' => 'New Department',
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionDoesntHaveErrors('code');
    }

    public function test_department_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Test Department',
            'code' => 'DEPT-001',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_department_description_max_length(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.departments.store'), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Test Department',
            'code' => 'DEPT-001',
            'description' => str_repeat('a', 1001),
        ]);

        $response->assertSessionHasErrors('description');
    }

    /*
    |--------------------------------------------------------------------------
    | Update Tests
    |--------------------------------------------------------------------------
    */

    public function test_edit_department_form_is_displayed(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.edit', $department));

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.edit');
    }

    public function test_department_can_be_updated(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.departments.update', $department), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'Updated Department Name',
            'code' => 'DEPT-UPD',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertEquals('Updated Department Name', $department->name);
        $this->assertEquals('DEPT-UPD', $department->code);
    }

    public function test_department_unique_validation_ignores_self_on_update(): void
    {
        $department = Department::factory()->create([
            'name' => 'My Department',
            'code' => 'DEPT-001',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.departments.update', $department), [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'name' => 'My Department',
            'code' => 'DEPT-001',
        ]);

        $response->assertSessionDoesntHaveErrors(['name', 'code']);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_soft_deleted(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.departments.destroy', $department));

        $response->assertRedirect(route('admin.departments.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('departments', ['id' => $department->id]);
    }

    public function test_soft_deleted_department_still_exists_in_database(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->actingAs($this->user)->delete(route('admin.departments.destroy', $department));

        $this->assertDatabaseHas('departments', ['id' => $department->id]);
        $this->assertNotNull(Department::withTrashed()->find($department->id)->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_restored(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);
        $department->delete();

        $response = $this->actingAs($this->user)->patch(route('admin.departments.restore', $department));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertNull($department->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_can_be_activated(): void
    {
        $department = Department::factory()->create([
            'status' => 'inactive',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('admin.departments.activate', $department));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertEquals('active', $department->status);
    }

    public function test_department_can_be_deactivated(): void
    {
        $department = Department::factory()->create([
            'status' => 'active',
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->patch(route('admin.departments.deactivate', $department));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertEquals('inactive', $department->status);
    }

    /*
    |--------------------------------------------------------------------------
    | View Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_details_can_be_viewed(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.departments.show', $department));

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.show');
        $response->assertSee($department->name);
    }

    /*
    |--------------------------------------------------------------------------
    | Relationship Tests
    |--------------------------------------------------------------------------
    */

    public function test_department_belongs_to_company(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->assertInstanceOf(Company::class, $department->company);
        $this->assertEquals($this->company->id, $department->company->id);
    }

    public function test_department_belongs_to_branch(): void
    {
        $department = Department::factory()->create([
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
        ]);

        $this->assertInstanceOf(Branch::class, $department->branch);
        $this->assertEquals($this->branch->id, $department->branch->id);
    }

    /*
    |--------------------------------------------------------------------------
    | AJAX Tests
    |--------------------------------------------------------------------------
    */

    public function test_get_branches_for_company(): void
    {
        Branch::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user)->getJson(route('admin.departments.branches', $this->company));

        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }
}
