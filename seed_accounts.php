<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ChartOfAccount;
use App\Models\AccountType;
use App\Models\Company;

$company = Company::first();

$types = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
$typeModels = [];
foreach ($types as $type) {
    $typeModels[$type] = AccountType::firstOrCreate(
        ['name' => $type, 'company_id' => $company->id],
        ['category' => $type]
    );
}

$accounts = [
    ['code' => '1000', 'name' => 'Cash', 'type' => 'Asset', 'is_system' => true],
    ['code' => '1010', 'name' => 'Bank', 'type' => 'Asset', 'is_system' => false],
    ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'Asset', 'is_system' => true],
    ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Liability', 'is_system' => true],
    ['code' => '3000', 'name' => 'Owner Equity', 'type' => 'Equity', 'is_system' => true],
    ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Revenue', 'is_system' => true],
    ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Expense', 'is_system' => true],
    ['code' => '6000', 'name' => 'Operating Expenses', 'type' => 'Expense', 'is_system' => false],
];

foreach ($accounts as $accountData) {
    ChartOfAccount::firstOrCreate(
        ['company_id' => $company->id, 'code' => $accountData['code']],
        [
            'name' => $accountData['name'],
            'is_system' => $accountData['is_system'],
            'is_active' => true,
            'account_type_id' => $typeModels[$accountData['type']]->id
        ]
    );
}

echo "Accounts seeded successfully.\n";
