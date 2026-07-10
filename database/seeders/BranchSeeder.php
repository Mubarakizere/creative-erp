<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Seed default branches for the default company.
     */
    public function run(): void
    {
        $company = Company::where('email', 'info@creative-engineering.com')->first();

        if (! $company) {
            return;
        }

        $branches = [
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'name' => 'Head Office',
                'code' => 'HQ-001',
                'email' => 'hq@creative-engineering.com',
                'phone' => '+971 4 123 4567',
                'manager_name' => 'Ahmed Al Maktoum',
                'country' => 'United Arab Emirates',
                'state' => 'Dubai',
                'city' => 'Dubai',
                'address' => 'Business Bay, Tower A, Floor 15',
                'postal_code' => '00000',
                'latitude' => 25.1860667,
                'longitude' => 55.2628581,
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'name' => 'Abu Dhabi Branch',
                'code' => 'AD-002',
                'email' => 'abudhabi@creative-engineering.com',
                'phone' => '+971 2 456 7890',
                'manager_name' => 'Fatima Al Nahyan',
                'country' => 'United Arab Emirates',
                'state' => 'Abu Dhabi',
                'city' => 'Abu Dhabi',
                'address' => 'Al Reem Island, Sky Tower, Floor 8',
                'postal_code' => '00000',
                'latitude' => 24.4539400,
                'longitude' => 54.3773400,
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'name' => 'Sharjah Branch',
                'code' => 'SH-003',
                'email' => 'sharjah@creative-engineering.com',
                'phone' => '+971 6 789 0123',
                'manager_name' => 'Omar Al Qasimi',
                'country' => 'United Arab Emirates',
                'state' => 'Sharjah',
                'city' => 'Sharjah',
                'address' => 'Al Majaz Waterfront, Office 305',
                'postal_code' => '00000',
                'latitude' => 25.3387200,
                'longitude' => 55.4124600,
                'status' => 'inactive',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::updateOrCreate(
                [
                    'company_id' => $branch['company_id'],
                    'code' => $branch['code'],
                ],
                $branch
            );
        }
    }
}
