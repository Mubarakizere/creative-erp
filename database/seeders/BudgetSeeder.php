<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = \App\Models\Company::first();
        if (!$company) return;

        $fiscalYear = \App\Models\FiscalYear::where('company_id', $company->id)->where('is_closed', false)->first();
        $branch = \App\Models\Branch::where('company_id', $company->id)->first();
        
        $categories = \App\Models\BudgetCategory::where('company_id', $company->id)->get();
        if ($categories->isEmpty()) {
            \App\Models\BudgetCategory::create(['company_id' => $company->id, 'name' => 'Operational Expenses', 'type' => 'Expense', 'description' => 'General OPEX']);
            \App\Models\BudgetCategory::create(['company_id' => $company->id, 'name' => 'Capital Expenditures', 'type' => 'Expense', 'description' => 'CAPEX']);
            \App\Models\BudgetCategory::create(['company_id' => $company->id, 'name' => 'Software & IT', 'type' => 'Expense', 'description' => 'IT software and services']);
            $categories = \App\Models\BudgetCategory::where('company_id', $company->id)->get();
        }

        $accounts = \App\Models\ChartOfAccount::where('company_id', $company->id)
            ->where('status', 'active')
            ->limit(5)
            ->get();

        if (!$fiscalYear) {
            $fiscalYear = \App\Models\FiscalYear::create([
                'company_id' => $company->id,
                'name' => 'FY-'.date('Y'),
                'start_date' => date('Y-01-01'),
                'end_date' => date('Y-12-31'),
                'is_closed' => false
            ]);
        }

        // Create 2 Budgets
        $budget1 = \App\Models\Budget::create([
            'company_id' => $company->id,
            'fiscal_year_id' => $fiscalYear->id,
            'name' => 'Q3 Technology Budget',
            'status' => 'active',
            'total_amount' => 150000.00
        ]);

        if ($accounts->isNotEmpty()) {
            \App\Models\BudgetLine::create([
                'budget_id' => $budget1->id,
                'budget_category_id' => $categories[2]->id ?? null,
                'chart_of_account_id' => $accounts[0]->id ?? null,
                'amount' => 50000.00
            ]);
            \App\Models\BudgetLine::create([
                'budget_id' => $budget1->id,
                'budget_category_id' => $categories[0]->id ?? null,
                'chart_of_account_id' => $accounts[1]->id ?? null,
                'amount' => 100000.00
            ]);
        }

        $budget2 = \App\Models\Budget::create([
            'company_id' => $company->id,
            'fiscal_year_id' => $fiscalYear->id,
            'name' => 'Annual Corporate Marketing',
            'status' => 'active',
            'total_amount' => 250000.00
        ]);

        if ($accounts->count() > 2) {
            \App\Models\BudgetLine::create([
                'budget_id' => $budget2->id,
                'budget_category_id' => $categories[0]->id ?? null,
                'chart_of_account_id' => $accounts[2]->id ?? null,
                'amount' => 250000.00
            ]);
        }
    }
}
