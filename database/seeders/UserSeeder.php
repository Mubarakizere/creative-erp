<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $employeeRole = Role::where('name', 'Employee')->first();

        if ($companies->isEmpty()) {
            return; // Needs companies to exist first
        }

        foreach ($companies as $company) {
            $branches = Branch::where('company_id', $company->id)->get();
            
            if ($branches->isEmpty()) {
                continue;
            }

            foreach ($branches as $branch) {
                $departments = Department::where('branch_id', $branch->id)->get();

                if ($departments->isEmpty()) {
                    continue;
                }

                // Create a couple of users per department
                foreach ($departments as $department) {
                    for ($i = 1; $i <= 2; $i++) {
                        $user = User::create([
                            'company_id' => $company->id,
                            'branch_id' => $branch->id,
                            'department_id' => $department->id,
                            'first_name' => 'Test',
                            'last_name' => "User {$company->id}{$branch->id}{$department->id}{$i}",
                            'email' => "user{$company->id}{$branch->id}{$department->id}{$i}@example.com",
                            'password' => Hash::make('password'),
                            'status' => 'active',
                            'job_title' => 'Software Engineer',
                            'email_verified_at' => now(),
                        ]);

                        if ($employeeRole) {
                            $user->assignRole($employeeRole);
                        }
                    }
                }
            }
        }
    }
}
