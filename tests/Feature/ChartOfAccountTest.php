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
    }
}
