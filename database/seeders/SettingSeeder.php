<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'HRM-Pro', 'type' => 'string', 'group' => 'general', 'description' => 'Application name'],
            ['key' => 'company_email', 'value' => 'admin@hrm-pro.com', 'type' => 'string', 'group' => 'general', 'description' => 'Company contact email'],
            ['key' => 'company_phone', 'value' => '', 'type' => 'string', 'group' => 'general', 'description' => 'Company contact phone'],
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'general', 'description' => 'Enable maintenance mode'],

            // Email Settings
            ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'type' => 'string', 'group' => 'email', 'description' => 'SMTP server host'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'integer', 'group' => 'email', 'description' => 'SMTP server port'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'string', 'group' => 'email', 'description' => 'SMTP username'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'string', 'group' => 'email', 'description' => 'SMTP password'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'type' => 'string', 'group' => 'email', 'description' => 'SMTP encryption (tls/ssl)'],

            // Payment Settings
            ['key' => 'payment_gateway', 'value' => 'stripe', 'type' => 'string', 'group' => 'payment', 'description' => 'Default payment gateway'],
            ['key' => 'stripe_public_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Stripe publishable key'],
            ['key' => 'stripe_secret_key', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'Stripe secret key'],
            ['key' => 'paypal_client_id', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'PayPal client ID'],
            ['key' => 'paypal_secret', 'value' => '', 'type' => 'string', 'group' => 'payment', 'description' => 'PayPal secret key'],

            // Subscription Settings
            ['key' => 'default_trial_days', 'value' => '14', 'type' => 'integer', 'group' => 'subscription', 'description' => 'Default trial period in days'],
            ['key' => 'auto_suspend_expired', 'value' => '1', 'type' => 'boolean', 'group' => 'subscription', 'description' => 'Auto-suspend expired subscriptions'],
            ['key' => 'grace_period_days', 'value' => '7', 'type' => 'integer', 'group' => 'subscription', 'description' => 'Grace period before suspension'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
