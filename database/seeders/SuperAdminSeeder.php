<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default super admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@hrm-pro.com'],
            [
                'tenant_id' => null,
                'name' => 'Super Administrator',
                'email' => 'superadmin@hrm-pro.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super admin created successfully!');
        $this->command->info('Email: superadmin@hrm-pro.com');
        $this->command->info('Password: password');
        $this->command->warn('IMPORTANT: Change this password immediately in production!');
    }
}
