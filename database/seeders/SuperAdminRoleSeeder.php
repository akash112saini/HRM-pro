<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SuperAdminRole;

class SuperAdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super-administrator',
                'description' => 'Full access to all features and settings',
                'permissions' => [
                    'manage_tenants',
                    'view_tenants',
                    'manage_subscriptions',
                    'view_subscriptions',
                    'manage_subscription_plans',
                    'view_revenue',
                    'manage_payments',
                    'manage_passwords',
                    'manage_super_admins',
                    'manage_roles',
                    'manage_settings',
                    'view_activity_logs'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Can manage tenants and subscriptions',
                'permissions' => [
                    'manage_tenants',
                    'view_tenants',
                    'manage_subscriptions',
                    'view_subscriptions',
                    'view_revenue',
                    'manage_passwords'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Support',
                'slug' => 'support',
                'description' => 'Read-only access and password management',
                'permissions' => [
                    'view_tenants',
                    'view_subscriptions',
                    'manage_passwords'
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'slug' => 'finance',
                'description' => 'Revenue and payment management',
                'permissions' => [
                    'view_tenants',
                    'view_subscriptions',
                    'view_revenue',
                    'manage_payments'
                ],
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            SuperAdminRole::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
