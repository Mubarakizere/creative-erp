<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Journal;
use App\Models\GeneralLedger;
use App\Models\AccountingPeriod;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "\n--- Accounting Security & Permissions Tests ---\n\n";

// 1. Setup Roles and Permissions
$permissions = ['journal.create', 'journal.post', 'ledger.view', 'period.manage', 'report.export'];
foreach ($permissions as $p) {
    Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
}

$financeManagerRole = Role::firstOrCreate(['name' => 'Finance Manager', 'guard_name' => 'web']);
$financeManagerRole->syncPermissions($permissions);

$accountantRole = Role::firstOrCreate(['name' => 'Accountant', 'guard_name' => 'web']);
$accountantRole->syncPermissions(['journal.create', 'ledger.view']);

$employeeRole = Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
$employeeRole->syncPermissions([]); // No accounting permissions

// 2. Setup Test Data
$companyId = 1;
$journal = Journal::where('company_id', $companyId)->first();
$ledger = GeneralLedger::where('company_id', $companyId)->first();
$period = AccountingPeriod::where('company_id', $companyId)->first();

// Helper to test
function checkPolicy($user, $action, $modelClass, $modelInstance = null) {
    auth()->login($user);
    if ($modelInstance) {
        return auth()->user()->can($action, $modelInstance);
    }
    return auth()->user()->can($action, $modelClass);
}

// 3. Test Unauthorized User
$guest = User::factory()->create(['company_id' => $companyId]);
echo "Testing Guest (Unauthorized)...\n";
echo checkPolicy($guest, 'create', Journal::class) ? "[FAIL] Guest can create journal\n" : "   [PASS] Cannot create journal\n";
echo checkPolicy($guest, 'post', Journal::class, $journal) ? "[FAIL] Guest can post journal\n" : "   [PASS] Cannot post journal\n";
echo checkPolicy($guest, 'view', GeneralLedger::class, $ledger) ? "[FAIL] Guest can view ledger\n" : "   [PASS] Cannot view ledger\n";
echo checkPolicy($guest, 'update', AccountingPeriod::class, $period) ? "[FAIL] Guest can close period\n" : "   [PASS] Cannot close period\n";

// 4. Test Accountant
$accountant = User::factory()->create(['company_id' => $companyId]);
$accountant->assignRole('Accountant');
echo "\nTesting Accountant...\n";
echo checkPolicy($accountant, 'create', Journal::class) ? "   [PASS] Can create journal\n" : "[FAIL] Cannot create journal\n";
echo checkPolicy($accountant, 'post', Journal::class, $journal) ? "[FAIL] Accountant can post journal\n" : "   [PASS] Cannot post journal\n";
echo checkPolicy($accountant, 'view', GeneralLedger::class, $ledger) ? "   [PASS] Can view ledger\n" : "[FAIL] Cannot view ledger\n";
echo checkPolicy($accountant, 'update', AccountingPeriod::class, $period) ? "[FAIL] Accountant can close period\n" : "   [PASS] Cannot close period\n";

// 5. Test Finance Manager
$financeManager = User::factory()->create(['company_id' => $companyId]);
$financeManager->assignRole('Finance Manager');
echo "\nTesting Finance Manager...\n";
echo checkPolicy($financeManager, 'create', Journal::class) ? "   [PASS] Can create journal\n" : "[FAIL] Cannot create journal\n";
echo checkPolicy($financeManager, 'post', Journal::class, $journal) ? "   [PASS] Can post journal\n" : "[FAIL] Cannot post journal\n";
echo checkPolicy($financeManager, 'view', GeneralLedger::class, $ledger) ? "   [PASS] Can view ledger\n" : "[FAIL] Cannot view ledger\n";
echo checkPolicy($financeManager, 'update', AccountingPeriod::class, $period) ? "   [PASS] Can close period\n" : "[FAIL] Cannot close period\n";

// Cleanup
$guest->delete();
$accountant->delete();
$financeManager->delete();

echo "\nDone.\n";
