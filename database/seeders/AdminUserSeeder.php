<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin user.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'info@creativecenturyengineering.com'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'info@creativecenturyengineering.com',
                'password' => bcrypt('cce@2026'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole('Super Admin')) {
            $user->assignRole('Super Admin');
        }
    }
}
