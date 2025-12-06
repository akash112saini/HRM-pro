<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Trial',
                'slug' => 'trial',
                'description' => 'Perfect for trying out HRM-Pro with limited features',
                'price' => 0.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Up to 10 employees',
                    'Basic attendance tracking',
                    'Leave management',
                    'Basic reports',
                    '30-day trial period'
                ],
                'max_employees' => 10,
                'max_users' => 3,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'description' => 'Essential HR features for small teams',
                'price' => 49.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Up to 50 employees',
                    'Attendance tracking',
                    'Leave management',
                    'Basic payroll',
                    'Email support',
                    'Standard reports'
                ],
                'max_employees' => 50,
                'max_users' => 5,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Advanced features for growing companies',
                'price' => 99.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Up to 200 employees',
                    'Advanced attendance & biometric',
                    'Complete payroll with tax',
                    'Recruitment (ATS)',
                    'Performance management',
                    'Asset management',
                    'Priority support',
                    'Advanced analytics'
                ],
                'max_employees' => 200,
                'max_users' => 15,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'Complete solution for large organizations',
                'price' => 249.99,
                'billing_cycle' => 'monthly',
                'features' => [
                    'Unlimited employees',
                    'All Premium features',
                    'Custom branding',
                    'API access',
                    'Dedicated account manager',
                    'Custom integrations',
                    'SLA guarantee',
                    'Training & onboarding'
                ],
                'max_employees' => null,
                'max_users' => 50,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }

        $this->command->info('Subscription plans seeded successfully!');
    }
}
