<?php

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Services\Asset\AssetAccountingService;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();
try {
    $company = Company::first();
    $accountingService = app(AssetAccountingService::class);
    
    // We will just mock the asset properties to match the test scenario without needing to save everything perfectly
    $asset = Asset::where('name', 'Dell Laptop (Depreciation Test)')->first();
    if (!$asset) {
        echo "Asset not found.\n";
        exit;
    }

    echo "--- DISPOSAL TEST 1: GAIN SCENARIO ---\n";
    echo "Original Cost: 1,200,000\n";
    echo "Accumulated Dep: 700,000\n";
    echo "Sale Price: 600,000\n";
    
    // Ensure Gain/Loss account exists
    $gainLossAccount = ChartOfAccount::firstOrCreate(
        ['company_id' => $asset->company_id, 'name' => 'Gain/Loss on Disposal'],
        [
            'account_type_id' => \App\Models\AccountType::where('category', 'Revenue')->first()->id,
            'code' => '4100',
            'is_active' => true,
        ]
    );

    // Ensure Cash/Receivable account exists
    $cashAccount = ChartOfAccount::firstOrCreate(
        ['company_id' => $asset->company_id, 'name' => 'Accounts Receivable'],
        [
            'account_type_id' => \App\Models\AccountType::where('category', 'Asset')->first()->id,
            'code' => '1200',
            'is_active' => true,
        ]
    );

    // Override the asset properties for the test
    $asset->purchase_cost = 1200000;
    $asset->accumulated_depreciation = 700000;
    $asset->current_book_value = 500000;
    
    // Create the disposal record
    $disposalGain = new AssetDisposal([
        'asset_id' => $asset->id,
        'type' => 'Sold',
        'date' => now(),
        'reason' => 'Test Gain',
        'sale_price' => 600000,
        'disposal_costs' => 0,
    ]);
    
    // Link relations manually for the service
    $disposalGain->setRelation('asset', $asset);
    
    $journalGain = $accountingService->postDisposal($disposalGain);
    
    echo "Resulting Gain/Loss Value Stored in DB: " . number_format($disposalGain->gain_loss) . " (Positive = Gain)\n";
    echo "\nAccounting Entries:\n";
    foreach ($journalGain->entries as $entry) {
        $account = ChartOfAccount::find($entry->chart_of_account_id);
        echo "Account: {$account->name} | Debit: " . number_format($entry->debit) . " | Credit: " . number_format($entry->credit) . "\n";
    }


    echo "\n\n--- DISPOSAL TEST 2: LOSS SCENARIO ---\n";
    echo "Original Cost: 1,200,000\n";
    echo "Accumulated Dep: 700,000\n";
    echo "Sale Price: 400,000\n";
    
    $disposalLoss = new AssetDisposal([
        'asset_id' => $asset->id,
        'type' => 'Sold',
        'date' => now(),
        'reason' => 'Test Loss',
        'sale_price' => 400000,
        'disposal_costs' => 0,
    ]);
    
    $disposalLoss->setRelation('asset', $asset);
    
    $journalLoss = $accountingService->postDisposal($disposalLoss);
    
    echo "Resulting Gain/Loss Value Stored in DB: " . number_format($disposalLoss->gain_loss) . " (Negative = Loss)\n";
    echo "\nAccounting Entries:\n";
    foreach ($journalLoss->entries as $entry) {
        $account = ChartOfAccount::find($entry->chart_of_account_id);
        echo "Account: {$account->name} | Debit: " . number_format($entry->debit) . " | Credit: " . number_format($entry->credit) . "\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
DB::rollBack();
