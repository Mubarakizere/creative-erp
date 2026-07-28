<?php

use App\Models\Asset;
use App\Models\User;
use App\Models\Department;
use App\Models\AssetAssignment;
use App\Services\Asset\AssetService;
use Carbon\Carbon;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit;
}
Auth::login($user); // Login to test authorization and 'user who performed it'

$asset = Asset::with(['department', 'assignments', 'transfers'])->where('name', 'Dell Laptop (Depreciation Test)')->first();

if (!$asset) {
    echo "Asset not found.\n";
    exit;
}

$oldDepartment = $asset->department;
echo "Current Department: " . ($oldDepartment->name ?? 'None') . "\n";

// Fix assignment history if it doesn't exist
if ($asset->assignments->isEmpty()) {
    $asset->assignments()->create([
        'department_id' => $oldDepartment->id,
        'branch_id' => $asset->branch_id,
        'notes' => 'Initial assignment',
        'assigned_by' => $user->id,
    ]);
}

// Create Marketing Department
$marketingDept = Department::firstOrCreate(
    ['company_id' => $asset->company_id, 'name' => 'Marketing Department'],
    ['code' => 'MKT', 'branch_id' => $asset->branch_id, 'is_active' => true, 'created_by' => $user->id]
);

$transferData = [
    'to_department_id' => $marketingDept->id,
    'transfer_date' => Carbon::now()->toDateString(),
    'reason' => 'Reassigned to Marketing team for a new campaign.',
];

// Verify Authorization
if (!$user->can('update', $asset)) {
    echo "Authorization Failed! User cannot transfer.\n";
    exit;
}
echo "Authorization works: User has permission to transfer/update the asset.\n";

$assetService = app(AssetService::class);

// Initiate Transfer
echo "\nInitiating Transfer...\n";
$transfer = $assetService->initiateTransfer($asset, $transferData);

// If the status is Pending Approval, approve it
if ($transfer->status === 'Pending Approval') {
    echo "Transfer requires approval. Approving now...\n";
    $assetService->executeTransfer($transfer);
}

// Refresh asset
$asset = $asset->fresh(['department', 'assignments', 'transfers.requestedBy', 'transfers.approvedBy']);
$transfer = $transfer->fresh();

echo "\n--- TRANSFER RESULTS ---\n";
echo "1. Current department changes: " . ($asset->department->name === 'Marketing Department' ? 'YES' : 'NO') . " (" . $asset->department->name . ")\n";

// History Check
$previousAssignment = $asset->assignments()->where('department_id', $oldDepartment->id)->first();
echo "2. Previous department remains in history: " . ($previousAssignment && $previousAssignment->returned_at !== null ? 'YES' : 'NO') . "\n";

echo "3. Transfer date recorded: " . ($transfer->transfer_date !== null ? 'YES (' . $transfer->transfer_date->toDateString() . ')' : 'NO') . "\n";
echo "4. User who performed it recorded: " . ($transfer->requestedBy ? 'YES (' . $transfer->requestedBy->first_name . ' ' . $transfer->requestedBy->last_name . ')' : 'NO') . "\n";

// Additional info
echo "\nTransfer Details:\n";
echo "From Dept: " . ($transfer->fromDepartment->name ?? 'None') . "\n";
echo "To Dept: " . ($transfer->toDepartment->name ?? 'None') . "\n";
echo "Status: " . $transfer->status . "\n";
