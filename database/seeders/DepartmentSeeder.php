<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartmentSeeder extends Seeder
{
    /**
     * Seed default departments for the default company branches.
     */
    public function run(): void
    {
        $company = Company::where('email', 'info@creative-engineering.com')->first();

        if (! $company) {
            return;
        }

        $hqBranch = Branch::where('company_id', $company->id)->where('code', 'HQ-001')->first();
        $adBranch = Branch::where('company_id', $company->id)->where('code', 'AD-002')->first();

        if (! $hqBranch) {
            return;
        }

        $hqDepartments = [
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'branch_id' => $hqBranch->id,
                'name' => 'Engineering',
                'code' => 'DEPT-ENG',
                'manager_name' => 'Khalid Al Rashid',
                'email' => 'engineering@creative-engineering.com',
                'phone' => '+971 4 123 4570',
                'description' => 'Core engineering department responsible for all technical projects.',
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'branch_id' => $hqBranch->id,
                'name' => 'Finance',
                'code' => 'DEPT-FIN',
                'manager_name' => 'Sara Al Mahmoud',
                'email' => 'finance@creative-engineering.com',
                'phone' => '+971 4 123 4571',
                'description' => 'Financial operations, budgeting, and accounting.',
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'branch_id' => $hqBranch->id,
                'name' => 'Human Resources',
                'code' => 'DEPT-HR',
                'manager_name' => 'Layla Ibrahim',
                'email' => 'hr@creative-engineering.com',
                'phone' => '+971 4 123 4572',
                'description' => 'Employee management, recruitment, and HR operations.',
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'branch_id' => $hqBranch->id,
                'name' => 'Procurement',
                'code' => 'DEPT-PROC',
                'manager_name' => 'Youssef Al Khatib',
                'email' => 'procurement@creative-engineering.com',
                'phone' => '+971 4 123 4573',
                'description' => 'Purchasing, vendor management, and supply chain.',
                'status' => 'active',
            ],
            [
                'uuid' => (string) Str::uuid(),
                'company_id' => $company->id,
                'branch_id' => $hqBranch->id,
                'name' => 'IT Department',
                'code' => 'DEPT-IT',
                'manager_name' => 'Nasser Al Fahim',
                'email' => 'it@creative-engineering.com',
                'phone' => '+971 4 123 4574',
                'description' => 'Information technology, systems, and digital infrastructure.',
                'status' => 'inactive',
            ],
        ];

        foreach ($hqDepartments as $dept) {
            Department::updateOrCreate(
                [
                    'company_id' => $dept['company_id'],
                    'code' => $dept['code'],
                ],
                $dept
            );
        }

        // Add departments for Abu Dhabi branch if it exists
        if ($adBranch) {
            $adDepartments = [
                [
                    'uuid' => (string) Str::uuid(),
                    'company_id' => $company->id,
                    'branch_id' => $adBranch->id,
                    'name' => 'Operations',
                    'code' => 'DEPT-OPS',
                    'manager_name' => 'Mohammed Al Zaabi',
                    'email' => 'operations@creative-engineering.com',
                    'phone' => '+971 2 456 7891',
                    'description' => 'Field operations and project execution management.',
                    'status' => 'active',
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'company_id' => $company->id,
                    'branch_id' => $adBranch->id,
                    'name' => 'Quality Assurance',
                    'code' => 'DEPT-QA',
                    'manager_name' => 'Huda Al Mansoori',
                    'email' => 'qa@creative-engineering.com',
                    'phone' => '+971 2 456 7892',
                    'description' => 'Quality control, inspections, and compliance.',
                    'status' => 'active',
                ],
            ];

            foreach ($adDepartments as $dept) {
                Department::updateOrCreate(
                    [
                        'company_id' => $dept['company_id'],
                        'code' => $dept['code'],
                    ],
                    $dept
                );
            }
        }
    }
}
