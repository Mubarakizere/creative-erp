<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Client;
use App\Models\Company;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::with('branches')->get();
        
        if ($companies->isEmpty()) {
            $this->command->warn('No companies found. Skipping Client Seeder.');
            return;
        }

        foreach ($companies as $company) {
            $branches = $company->branches;
            
            if ($branches->isEmpty()) {
                continue;
            }

            foreach ($branches as $branch) {
                // Create some Company Clients
                Client::factory()->count(3)->create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'client_type' => 'Company',
                    'first_name' => null,
                    'last_name' => null,
                ]);

                // Create some Individual Clients
                Client::factory()->count(3)->create([
                    'company_id' => $company->id,
                    'branch_id' => $branch->id,
                    'client_type' => 'Individual',
                    'company_name' => null,
                    'tax_number' => null,
                    'registration_number' => null,
                    'website' => null,
                ]);
            }
        }
    }
}
