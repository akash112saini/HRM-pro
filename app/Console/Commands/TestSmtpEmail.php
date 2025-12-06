<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

class TestSmtpEmail extends Command
{
    protected $signature = 'email:test {recipient}';
    protected $description = 'Test SMTP email configuration';

    public function handle()
    {
        $recipient = $this->argument('recipient');

        try {
            Mail::raw('This is a test email from HRM-Pro Super Admin panel. If you receive this, your SMTP configuration is working correctly!', function ($message) use ($recipient) {
                $message->to($recipient)
                    ->subject('SMTP Test - HRM-Pro')
                    ->from(Setting::get('company_email', config('mail.from.address')));
            });

            $this->info("✅ Test email sent successfully to {$recipient}");
            $this->info('Check your inbox to confirm delivery.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
