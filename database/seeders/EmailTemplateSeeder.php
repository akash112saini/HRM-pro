<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        EmailTemplate::updateOrCreate(
            ['key' => 'subscription_renewal_reminder'],
            [
                'name' => 'Subscription Renewal Reminder',
                'subject' => 'Subscription Renewal Reminder - {{company_name}}',
                'body' => '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; }
        .alert-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; }
        .info-table { width: 100%; margin: 20px 0; }
        .info-table th { text-align: left; padding: 10px; background: #f3f4f6; }
        .info-table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .button { display: inline-block; background: #6366f1; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Renewal Notice</h1>
        </div>
        <div class="content">
            <h2>Dear {{company_name}},</h2>
            
            <div class="alert-box">
                <strong>⏰ Reminder:</strong> Your subscription will expire in <strong>{{days_remaining}} days</strong>.
            </div>
            
            <p>This is a friendly reminder that your HRM-Pro subscription is approaching its expiration date.</p>
            
            <table class="info-table">
                <tr>
                    <th>Company Name:</th>
                    <td>{{company_name}}</td>
                </tr>
                <tr>
                    <th>Current Plan:</th>
                    <td>{{plan_name}}</td>
                </tr>
                <tr>
                    <th>Expiration Date:</th>
                    <td>{{expiry_date}}</td>
                </tr>
                <tr>
                    <th>Days Remaining:</th>
                    <td>{{days_remaining}} days</td>
                </tr>
            </table>
            
            <p><strong>To ensure uninterrupted service, please renew your subscription before the expiration date.</strong></p>
            
            <p>Benefits of timely renewal:</p>
            <ul>
                <li>✓ No service interruption</li>
                <li>✓ Continue accessing all features</li>
                <li>✓ Maintain your employee data and records</li>
                <li>✓ Keep your team productive</li>
            </ul>
            
            <center>
                <a href="{{renewal_url}}" class="button">Renew Subscription Now</a>
            </center>
            
            <p style="margin-top: 30px;">If you have any questions or need assistance, please don\'t hesitate to contact our support team.</p>
            
            <p>Thank you for choosing HRM-Pro!</p>
        </div>
        <div class="footer">
            <p>This is an automated reminder. Please do not reply to this email.</p>
            <p>&copy; {{current_year}} HRM-Pro. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
                ',
                'variables' => [
                    'company_name' => 'Company name',
                    'plan_name' => 'Subscription plan name',
                    'expiry_date' => 'Subscription expiration date',
                    'days_remaining' => 'Days until expiration',
                    'renewal_url' => 'URL to renew subscription',
                    'current_year' => 'Current year',
                ],
                'is_active' => true,
            ]
        );
    }
}
