<?php

namespace Database\Seeders;

use App\Models\AccountType;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Standard default account types and their high-level financial statement categories.
     */
    public const DEFAULT_TYPES = [
        ['name' => 'Current Asset', 'category' => 'Asset', 'code' => 'CA'],
        ['name' => 'Fixed Asset', 'category' => 'Asset', 'code' => 'FA'],
        ['name' => 'Non-Current Asset', 'category' => 'Asset', 'code' => 'NCA'],
        ['name' => 'Current Liability', 'category' => 'Liability', 'code' => 'CL'],
        ['name' => 'Long-Term Liability', 'category' => 'Liability', 'code' => 'LTL'],
        ['name' => 'Equity', 'category' => 'Equity', 'code' => 'EQ'],
        ['name' => 'Owner Equity', 'category' => 'Equity', 'code' => 'OEQ'],
        ['name' => 'Operating Revenue', 'category' => 'Revenue', 'code' => 'REV'],
        ['name' => 'Other Revenue', 'category' => 'Revenue', 'code' => 'OREV'],
        ['name' => 'Cost of Sales', 'category' => 'Expense', 'code' => 'COS'],
        ['name' => 'Operating Expense', 'category' => 'Expense', 'code' => 'EXP'],
        ['name' => 'Other Expense', 'category' => 'Expense', 'code' => 'OEXP'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $company = Company::firstOrCreate(
                ['id' => 1],
                [
                    'name' => 'Creative Century Engineering',
                    'email' => 'info@creativecenturyengineering.com',
                ]
            );
            $companies = collect([$company]);
        }

        foreach ($companies as $company) {
            $this->seedForCompany($company->id);
        }
    }

    /**
     * Seed default account types for a specific company.
     */
    public static function seedForCompany(int $companyId): void
    {
        foreach (self::DEFAULT_TYPES as $type) {
            AccountType::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'name' => $type['name'],
                ],
                [
                    'category' => $type['category'],
                    'code' => $type['code'],
                    'is_active' => true,
                ]
            );
        }
    }
}
