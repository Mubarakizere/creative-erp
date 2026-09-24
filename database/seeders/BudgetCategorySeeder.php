<?php

namespace Database\Seeders;

use App\Models\BudgetCategory;
use App\Models\Company;
use Illuminate\Database\Seeder;

class BudgetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        if ($companies->isEmpty()) {
            return;
        }

        $defaultCategories = [
            [
                'name' => 'Direct Materials',
                'type' => 'expense',
                'description' => 'Raw materials, construction supplies, components, and site consumables.',
            ],
            [
                'name' => 'Direct Labor & Subcontractors',
                'type' => 'expense',
                'description' => 'Site engineers, technicians, specialized subcontractors, and hired labor.',
            ],
            [
                'name' => 'Equipment & Machinery',
                'type' => 'expense',
                'description' => 'Heavy machinery rental, plant tools, equipment fuel, and maintenance.',
            ],
            [
                'name' => 'Logistics & Transportation',
                'type' => 'expense',
                'description' => 'Freight, cargo shipping, site material delivery, and transit logistics.',
            ],
            [
                'name' => 'Operational Expenses (OPEX)',
                'type' => 'expense',
                'description' => 'Site office operations, utilities, safety gear, communications, and administrative costs.',
            ],
            [
                'name' => 'Capital Expenditures (CAPEX)',
                'type' => 'expense',
                'description' => 'Long-term machinery acquisition, infrastructure, and heavy plant assets.',
            ],
            [
                'name' => 'Software & Engineering IT',
                'type' => 'expense',
                'description' => 'CAD/BIM licenses, structural analysis tools, ERP subscriptions, and IT services.',
            ],
            [
                'name' => 'Permits, Regulatory & Testing',
                'type' => 'expense',
                'description' => 'Municipal permits, quality certifications, lab tests, and safety compliance audits.',
            ],
            [
                'name' => 'Contingency & Miscellaneous',
                'type' => 'expense',
                'description' => 'Emergency risk reserves, unexpected site costs, and price inflation buffers.',
            ],
        ];

        foreach ($companies as $company) {
            foreach ($defaultCategories as $cat) {
                BudgetCategory::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'name' => $cat['name'],
                    ],
                    [
                        'type' => $cat['type'],
                        'description' => $cat['description'],
                    ]
                );
            }
        }
    }
}
