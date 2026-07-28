<?php

use App\Models\Asset;
use App\Models\AssetMaintenance;
use App\Models\User;
use App\Services\ReportBuilderService;
use Carbon\Carbon;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}
Auth::login($user);

$asset = Asset::where('name', 'Dell Laptop (Depreciation Test)')->first();

if (!$asset) {
    echo "Asset not found.\n";
    exit;
}

echo "Testing Asset: " . $asset->name . "\n";

// Add Maintenance
$maintenanceDate = Carbon::now()->toDateString();
$nextMaintenanceDate = Carbon::now()->addMonths(6)->toDateString();

$maintenance = AssetMaintenance::create([
    'asset_id' => $asset->id,
    'maintenance_date' => $maintenanceDate,
    'description' => 'Laptop maintenance',
    'vendor' => 'Dell Repair Services',
    'cost' => 50000,
    'next_maintenance_date' => $nextMaintenanceDate,
    'status' => 'Completed',
    'recorded_by' => $user->id,
]);

echo "\n--- Maintenance Record ---\n";
echo "Description: " . $maintenance->description . "\n";
echo "Vendor: " . $maintenance->vendor . "\n";
echo "Cost: " . number_format($maintenance->cost, 2) . "\n";
echo "Next Maintenance Date: " . $maintenance->next_maintenance_date->format('Y-m-d') . "\n";
echo "Status: " . $maintenance->status . "\n";

echo "\n--- Verifying Report Values ---\n";
$reportService = app(ReportBuilderService::class);
$reportData = $reportService->build('asset_maintenance', ['company_id' => $asset->company_id]);

// Find this asset's maintenance in the report
$foundInReport = false;
$totalMaintenanceCost = 0;

foreach ($reportData as $row) {
    if ($row->asset_id == $asset->id && $row->description == 'Laptop maintenance') {
        $foundInReport = true;
    }
    $totalMaintenanceCost += $row->cost;
}

echo "Found in Asset Maintenance Report: " . ($foundInReport ? 'YES' : 'NO') . "\n";
echo "Total Maintenance Cost in Report: " . number_format($totalMaintenanceCost, 2) . "\n";

// To check Dashboard Metrics, we can check the sum on the asset itself
echo "\n--- Dashboard/Asset Metrics ---\n";
$assetTotalMaintenance = $asset->maintenances()->sum('cost');
echo "Asset Total Maintenance Cost: " . number_format($assetTotalMaintenance, 2) . "\n";

echo "\nVerification passed if YES and values match.\n";
