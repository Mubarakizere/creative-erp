<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Company;
use App\Models\User;
use App\Services\Asset\DepreciationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetDepreciationTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $user;
    protected $category;
    protected $depreciationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->company = Company::factory()->create();
        $this->user = User::factory()->create(['company_id' => $this->company->id]);
        
        $accountType = \App\Models\AccountType::create([
            'company_id' => $this->company->id,
            'name' => 'Asset',
            'category' => 'Asset',
        ]);
        
        $acc1 = \App\Models\ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $accountType->id,
            'name' => 'Asset Account',
            'code' => '1000'
        ]);
        
        $acc2 = \App\Models\ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $accountType->id,
            'name' => 'Acc Dep',
            'code' => '1001'
        ]);
        
        $acc3 = \App\Models\ChartOfAccount::create([
            'company_id' => $this->company->id,
            'account_type_id' => $accountType->id,
            'name' => 'Dep Expense',
            'code' => '5000'
        ]);
        
        $this->category = AssetCategory::create([
            'company_id' => $this->company->id,
            'name' => 'Test Equipment',
            'asset_account_id' => $acc1->id,
            'accumulated_depreciation_account_id' => $acc2->id,
            'depreciation_expense_account_id' => $acc3->id,
        ]);
        
        $this->depreciationService = app(DepreciationService::class);
    }

    public function test_straight_line_depreciation()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-001',
            'name' => 'Straight Line Asset',
            'purchase_cost' => 12000,
            'residual_value' => 2000,
            'useful_life' => 10, // 10 months for easy math
            'depreciation_method' => 'straight_line',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 12000,
            'status' => 'Active'
        ]);

        // Rate = (12000 - 2000) / 10 = 1000 per month
        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd);

        $this->assertNotNull($depreciation);
        $this->assertEquals(1000.00, $depreciation->amount);
    }

    public function test_declining_balance_depreciation()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-002',
            'name' => 'Declining Balance Asset',
            'purchase_cost' => 10000,
            'residual_value' => 1000,
            'useful_life' => 5, // 20% annual rate
            'depreciation_method' => 'declining_balance',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 10000,
            'status' => 'Active'
        ]);

        // Rate = 1/5 = 20%
        // Annual = 10000 * 20% = 2000
        // Monthly = 2000 / 12 = 166.67
        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd);

        $this->assertNotNull($depreciation);
        $this->assertEquals(166.67, $depreciation->amount);
    }

    public function test_double_declining_balance_depreciation()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-003',
            'name' => 'Double Declining Balance Asset',
            'purchase_cost' => 10000,
            'residual_value' => 1000,
            'useful_life' => 5, // 40% annual rate
            'depreciation_method' => 'double_declining_balance',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 10000,
            'status' => 'Active'
        ]);

        // Rate = 2/5 = 40%
        // Annual = 10000 * 40% = 4000
        // Monthly = 4000 / 12 = 333.33
        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd);

        $this->assertNotNull($depreciation);
        $this->assertEquals(333.33, $depreciation->amount);
    }

    public function test_units_of_production_depreciation()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-004',
            'name' => 'Units of Production Asset',
            'purchase_cost' => 50000,
            'residual_value' => 10000,
            'useful_units' => 100000, // total units expected
            'depreciation_method' => 'units_of_production',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 50000,
            'status' => 'Active'
        ]);

        // Base = 50000 - 10000 = 40000
        // Rate = 40000 / 100000 = 0.4 per unit
        // If we produce 5000 units this month: 5000 * 0.4 = 2000
        
        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd, [
            'units_produced' => 5000
        ]);

        $this->assertNotNull($depreciation);
        $this->assertEquals(2000.00, $depreciation->amount);
    }

    public function test_residual_value_is_respected()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-005',
            'name' => 'Residual Value Asset',
            'purchase_cost' => 12000,
            'residual_value' => 11500, // Book value is 12000, only 500 left to depreciate
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 12000,
            'status' => 'Active'
        ]);

        // Rate = (12000 - 11500) / 10 = 50 per month
        // Wait, standard straight line without capping would be (12000 - 11500) / 10 = 50.
        // Let's force it to try to depreciate MORE than what's left.
        $asset->useful_life = 1; // Rate = 500
        $asset->current_book_value = 11700; // Remaining = 11700 - 11500 = 200
        
        // Base is 12000 - 11500 = 500. Monthly = 500.
        // But current book value is 11700, residual is 11500. It can only depreciate 200.
        
        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd);

        $this->assertNotNull($depreciation);
        $this->assertEquals(200.00, $depreciation->amount);
    }

    public function test_duplicate_period_prevention()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-006',
            'name' => 'Duplicate Prevention Asset',
            'purchase_cost' => 12000,
            'residual_value' => 2000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 12000,
            'status' => 'Active'
        ]);

        $periodEnd = Carbon::now()->endOfMonth();
        
        // First time should work
        $depreciation1 = $this->depreciationService->calculateDepreciation($asset, $periodEnd);
        $this->assertNotNull($depreciation1);
        
        // Save the preview to DB
        $depreciation1->save();

        // Second time should return null because period exists
        $depreciation2 = $this->depreciationService->calculateDepreciation($asset, $periodEnd);
        $this->assertNull($depreciation2);
    }

    public function test_no_depreciation_after_disposal()
    {
        $asset = Asset::create([
            'company_id' => $this->company->id,
            'asset_category_id' => $this->category->id,
            'asset_number' => 'AST-007',
            'name' => 'Disposed Asset',
            'purchase_cost' => 12000,
            'residual_value' => 2000,
            'useful_life' => 10,
            'depreciation_method' => 'straight_line',
            'in_service_date' => Carbon::now()->startOfMonth(),
            'current_book_value' => 0,
            'status' => 'Sold' // Not Active
        ]);

        $periodEnd = Carbon::now()->endOfMonth();
        $depreciation = $this->depreciationService->calculateDepreciation($asset, $periodEnd);

        $this->assertNull($depreciation);
    }
}
