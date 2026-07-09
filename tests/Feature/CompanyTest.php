<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'status' => 'active',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization Tests
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_companies(): void
    {
        $response = $this->get(route('admin.companies.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_companies(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.index');
    }

    /*
    |--------------------------------------------------------------------------
    | List Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_list_shows_companies(): void
    {
        Company::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewHas('companies');
    }

    public function test_company_list_search_works(): void
    {
        Company::factory()->create(['name' => 'Alpha Construction']);
        Company::factory()->create(['name' => 'Beta Engineering']);

        $response = $this->actingAs($this->user)->get(route('admin.companies.index', [
            'search' => 'Alpha',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Alpha Construction');
    }

    public function test_company_list_status_filter_works(): void
    {
        Company::factory()->create(['name' => 'Active Corp', 'status' => 'active']);
        Company::factory()->create(['name' => 'Inactive Corp', 'status' => 'inactive']);

        $response = $this->actingAs($this->user)->get(route('admin.companies.index', [
            'status' => 'active',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Active Corp');
        $response->assertDontSee('Inactive Corp');
    }

    /*
    |--------------------------------------------------------------------------
    | Create Tests
    |--------------------------------------------------------------------------
    */

    public function test_create_company_form_is_displayed(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.companies.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.create');
    }

    public function test_company_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'phone' => '+1 234 567 890',
            'country' => 'United States',
            'currency' => 'USD',
            'timezone' => 'America/New_York',
            'language' => 'en',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'slug' => 'test-company',
        ]);
    }

    public function test_company_slug_is_generated_automatically(): void
    {
        $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'My Great Company',
            'email' => 'great@company.com',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'My Great Company',
            'slug' => 'my-great-company',
        ]);
    }

    public function test_company_uuid_is_generated_automatically(): void
    {
        $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'UUID Test Company',
            'email' => 'uuid@company.com',
        ]);

        $company = Company::where('email', 'uuid@company.com')->first();

        $this->assertNotNull($company->uuid);
        $this->assertTrue(strlen($company->uuid) === 36);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'email' => 'test@company.com',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_company_email_is_required(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_company_name_must_be_unique(): void
    {
        Company::factory()->create(['name' => 'Unique Company']);

        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Unique Company',
            'email' => 'new@company.com',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_company_email_must_be_unique(): void
    {
        Company::factory()->create(['email' => 'taken@company.com']);

        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'New Company',
            'email' => 'taken@company.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_company_email_must_be_valid(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_company_website_must_be_valid_url(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'website' => 'not-a-url',
        ]);

        $response->assertSessionHasErrors('website');
    }

    public function test_company_logo_must_be_image(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    public function test_company_logo_max_size(): void
    {
        $file = UploadedFile::fake()->image('logo.jpg')->size(3000); // 3MB

        $response = $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'email' => 'test@company.com',
            'logo' => $file,
        ]);

        $response->assertSessionHasErrors('logo');
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_logo_can_be_uploaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('logo.jpg', 200, 200)->size(500);

        $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Logo Test Company',
            'email' => 'logo@company.com',
            'logo' => $file,
        ]);

        $company = Company::where('email', 'logo@company.com')->first();

        $this->assertNotNull($company->logo);
        Storage::disk('public')->assertExists($company->logo);
    }

    public function test_company_favicon_can_be_uploaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('favicon.png', 32, 32)->size(100);

        $this->actingAs($this->user)->post(route('admin.companies.store'), [
            'name' => 'Favicon Test Company',
            'email' => 'favicon@company.com',
            'favicon' => $file,
        ]);

        $company = Company::where('email', 'favicon@company.com')->first();

        $this->assertNotNull($company->favicon);
        Storage::disk('public')->assertExists($company->favicon);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Tests
    |--------------------------------------------------------------------------
    */

    public function test_edit_company_form_is_displayed(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.companies.edit', $company));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.edit');
    }

    public function test_company_can_be_updated(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->put(route('admin.companies.update', $company), [
            'name' => 'Updated Name',
            'email' => 'updated@company.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertEquals('Updated Name', $company->name);
        $this->assertEquals('updated@company.com', $company->email);
    }

    public function test_company_unique_validation_ignores_self_on_update(): void
    {
        $company = Company::factory()->create([
            'name' => 'My Company',
            'email' => 'my@company.com',
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.companies.update', $company), [
            'name' => 'My Company',
            'email' => 'my@company.com',
        ]);

        $response->assertSessionDoesntHaveErrors(['name', 'email']);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_soft_deleted(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.companies.destroy', $company));

        $response->assertRedirect(route('admin.companies.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }

    public function test_soft_deleted_company_still_exists_in_database(): void
    {
        $company = Company::factory()->create();

        $this->actingAs($this->user)->delete(route('admin.companies.destroy', $company));

        $this->assertDatabaseHas('companies', ['id' => $company->id]);
        $this->assertNotNull(Company::withTrashed()->find($company->id)->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Restore Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_restored(): void
    {
        $company = Company::factory()->create();
        $company->delete();

        $response = $this->actingAs($this->user)->patch(route('admin.companies.restore', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertNull($company->deleted_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_can_be_activated(): void
    {
        $company = Company::factory()->create(['status' => 'inactive']);

        $response = $this->actingAs($this->user)->patch(route('admin.companies.activate', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertEquals('active', $company->status);
    }

    public function test_company_can_be_deactivated(): void
    {
        $company = Company::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->user)->patch(route('admin.companies.deactivate', $company));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $company->refresh();
        $this->assertEquals('inactive', $company->status);
    }

    /*
    |--------------------------------------------------------------------------
    | View Tests
    |--------------------------------------------------------------------------
    */

    public function test_company_details_can_be_viewed(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.companies.show', $company));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.show');
        $response->assertSee($company->name);
    }

    public function test_company_settings_page_is_displayed(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.companies.settings', $company));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.settings');
    }
}
