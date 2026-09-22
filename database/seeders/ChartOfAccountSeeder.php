<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\ChartOfAccount;
use App\Models\Company;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->call(AccountTypeSeeder::class);
            $companies = Company::all();
        }

        foreach ($companies as $company) {
            AccountTypeSeeder::seedForCompany($company->id);

            $types = AccountType::where('company_id', $company->id)->get()->keyBy('name');

            $defaultAccounts = [
                ['code' => '1000', 'name' => 'Cash on Hand', 'type' => 'Current Asset', 'is_system' => true],
                ['code' => '1010', 'name' => 'Main Bank Account', 'type' => 'Current Asset', 'is_system' => false],
                ['code' => '1200', 'name' => 'Accounts Receivable', 'type' => 'Current Asset', 'is_system' => true],
                ['code' => '1500', 'name' => 'Property, Plant & Equipment', 'type' => 'Fixed Asset', 'is_system' => true],
                ['code' => '2000', 'name' => 'Accounts Payable', 'type' => 'Current Liability', 'is_system' => true],
                ['code' => '2100', 'name' => 'VAT / Tax Payable', 'type' => 'Current Liability', 'is_system' => true],
                ['code' => '3000', 'name' => 'Owner Equity', 'type' => 'Owner Equity', 'is_system' => true],
                ['code' => '3100', 'name' => 'Retained Earnings', 'type' => 'Equity', 'is_system' => true],
                ['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'Operating Revenue', 'is_system' => true],
                ['code' => '5000', 'name' => 'Cost of Goods Sold', 'type' => 'Cost of Sales', 'is_system' => true],
                ['code' => '6000', 'name' => 'General & Administrative Expenses', 'type' => 'Operating Expense', 'is_system' => false],
                ['code' => '6100', 'name' => 'Salaries & Wages', 'type' => 'Operating Expense', 'is_system' => false],
                ['code' => '6200', 'name' => 'Rent Expense', 'type' => 'Operating Expense', 'is_system' => false],
            ];

            foreach ($defaultAccounts as $account) {
                $accountTypeId = $types[$account['type']]->id ?? $types->first()?->id;
                if ($accountTypeId) {
                    ChartOfAccount::firstOrCreate(
                        [
                            'company_id' => $company->id,
                            'code' => $account['code'],
                        ],
                        [
                            'name' => $account['name'],
                            'account_type_id' => $accountTypeId,
                            'is_system' => $account['is_system'],
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
}
