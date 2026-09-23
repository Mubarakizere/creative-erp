<?php

namespace Tests\Feature;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\AccountTypeSeeder;
use Database\Seeders\ChartOfAccountSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartOfAccountTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->company = Company::create([
            'name' => 'Test Engineering LLC',
            'email' => 'test@testengineering.com',
        ]);

        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);
        $this->user->assignRole('Super Admin');
        $this->actingAs($this->user);
    }

    public function test_account_type_seeder_populates_types(): void
    {
        $this->seed(AccountTypeSeeder::class);

        $this->assertDatabaseHas('account_types', [
            'company_id' => $this->company->id,
            'name' => 'Current Asset',
            'category' => 'Asset',
        ]);

        $this->assertDatabaseHas('account_types', [
            'company_id' => $this->company->id,
            'name' => 'Operating Revenue',
            'category' => 'Revenue',
        ]);
    }

    public function test_chart_of_account_seeder_populates_accounts(): void
    {
        $this->seed(ChartOfAccountSeeder::class);

        $this->assertDatabaseHas('chart_of_accounts', [
            'company_id' => $this->company->id,
            'code' => '1000',
            'name' => 'Cash on Hand',
        ]);

        $this->assertDatabaseHas('chart_of_accounts', [
            'company_id' => $this->company->id,
            'code' => '4000',
            'name' => 'Sales Revenue',
        ]);
    }

    public function test_create_page_renders_with_account_types(): void
    {
        $response = $this->get(route('admin.finance.accounting.chart-of-accounts.create'));

        $response->assertStatus(200);
        $response->assertSee('Account Type');
        $response->assertSee('Current Asset');
        $response->assertSee('Operating Revenue');
        $response->assertSee('+ Quick Add');
    }

    public function test_administrator_can_view_account_types_index(): void
    {
        $this->seed(AccountTypeSeeder::class);

        $adminUser = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);
        $adminUser->assignRole('Administrator');
        $this->actingAs($adminUser);

        $response = $this->get(route('admin.account-types.index'));

        $response->assertStatus(200);
        $response->assertSee('Account Types');
        $response->assertSee('Current Asset');
        $response->assertSee('Operating Revenue');
        $response->assertSee('New Account Type');
    }

    public function test_administrator_can_create_account_type(): void
    {
        $adminUser = User::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'active',
        ]);
        $adminUser->assignRole('Administrator');
        $this->actingAs($adminUser);

        $response = $this->post(route('admin.account-types.store'), [
            'name' => 'Short-Term Investments',
            'category' => 'Asset',
            'code' => 'STI',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.account-types.index'));
        $this->assertDatabaseHas('account_types', [
            'company_id' => $this->company->id,
            'name' => 'Short-Term Investments',
            'category' => 'Asset',
            'code' => 'STI',
        ]);
    }

    public function test_ajax_quick_add_account_type_returns_json(): void
    {
        $response = $this->postJson(route('admin.account-types.store'), [
            'name' => 'Warranty Reserve',
            'category' => 'Liability',
            'code' => 'WR',
            'is_active' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'account_type' => [
                    'name' => 'Warranty Reserve',
                    'category' => 'Liability',
                ],
            ]);

        $this->assertDatabaseHas('account_types', [
            'company_id' => $this->company->id,
            'name' => 'Warranty Reserve',
        ]);
    }

    public function test_administrator_can_update_account_type(): void
    {
        $type = AccountType::create([
            'company_id' => $this->company->id,
            'name' => 'Old Type Name',
            'category' => 'Expense',
            'code' => 'OTN',
            'is_active' => true,
        ]);

        $response = $this->put(route('admin.account-types.update', $type), [
            'name' => 'Updated Type Name',
            'category' => 'Expense',
            'code' => 'UTN',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.account-types.index'));
        $this->assertDatabaseHas('account_types', [
            'id' => $type->id,
            'name' => 'Updated Type Name',
            'code' => 'UTN',
        ]);
    }

    public function test_administrator_can_delete_unused_account_type(): void
    {
        $type = AccountType::create([
            'company_id' => $this->company->id,
            'name' => 'Temporary Type',
            'category' => 'Asset',
            'code' => 'TEMP',
            'is_active' => true,
        ]);

        $response = $this->delete(route('admin.account-types.destroy', $type));

        $response->assertRedirect(route('admin.account-types.index'));
        $this->assertSoftDeleted('account_types', [
            'id' => $type->id,
        ]);
    }

    public function test_administrator_cannot_delete_account_type_in_use(): void
    {
        $type = AccountType::create([
            'company_id' => $this->company->id,
            'name' => 'In-Use Type',
            'category' => 'Asset',
            'code' => 'IUT',
            'is_active' => true,
        ]);

        ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $type->id,
            'code' => '1999',
            'name' => 'Sample In-Use Account',
            'is_active' => true,
            'is_system' => false,
        ]);

        $response = $this->delete(route('admin.account-types.destroy', $type));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('account_types', [
            'id' => $type->id,
            'deleted_at' => null,
        ]);
    }
}
