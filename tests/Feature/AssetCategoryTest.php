<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\ChartOfAccount;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AssetCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        
        // Setup User with permissions
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        $role = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'asset.manage']);
        $role->givePermissionTo($permission);
        $this->user->assignRole($role);
    }

    public function test_can_view_create_asset_category_page()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.asset-categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.assets.categories.create');
    }

    public function test_can_create_asset_category()
    {
        // Setup Accounts
        $assetAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'is_active' => true]);
        $accDepAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'is_active' => true]);
        $depExpAccount = ChartOfAccount::factory()->create(['company_id' => $this->company->id, 'is_active' => true]);

        $categoryData = [
            'name' => 'Computer Equipment',
            'useful_life' => 36, // 3 years in months
            'depreciation_method' => 'straight_line',
            'asset_account_id' => $assetAccount->id,
            'accumulated_depreciation_account_id' => $accDepAccount->id,
            'depreciation_expense_account_id' => $depExpAccount->id,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('admin.asset-categories.store'), $categoryData);

        $response->assertRedirect(route('admin.asset-categories.index'));
        $response->assertSessionHas('success', 'Category created successfully.');

        $this->assertDatabaseHas('asset_categories', [
            'name' => 'Computer Equipment',
            'company_id' => $this->company->id,
            'useful_life' => 36,
            'depreciation_method' => 'straight_line',
        ]);
    }
}
