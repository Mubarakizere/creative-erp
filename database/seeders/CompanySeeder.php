<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Seed the default company.
     */
    public function run(): void
    {
        Company::updateOrCreate(
            ['email' => 'info@creative-engineering.com'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Creative Engineering',
                'legal_name' => 'Creative Engineering & Construction LLC',
                'slug' => 'creative-engineering',
                'email' => 'info@creative-engineering.com',
                'phone' => '+971 4 123 4567',
                'website' => 'https://creative-engineering.com',
                'registration_number' => 'CR-001234',
                'tax_number' => 'TAX-00123456',
                'country' => 'United Arab Emirates',
                'state' => 'Dubai',
                'city' => 'Dubai',
                'address' => 'Business Bay, Tower A, Floor 15',
                'postal_code' => '00000',
                'currency' => 'AED',
                'timezone' => 'Asia/Dubai',
                'language' => 'en',
                'working_days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                'working_hours_start' => '08:00',
                'working_hours_end' => '17:00',
                'status' => 'active',
            ]
        );
    }
}
