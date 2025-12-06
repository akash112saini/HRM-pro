<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\RenewalReminderLog;
use App\Mail\SubscriptionRenewalReminder;
use Illuminate\Support\Facades\Mail;

class SendRenewalReminders extends Command
{
    protected $signature = 'reminders:renewal {--days=7 : Days before expiry to send reminder}';
    protected $description = 'Send renewal reminder emails to tenants with expiring subscriptions';

    public function handle()
    {
        $daysBefore = (int) $this->option('days');
        $targetDate = now()->addDays($daysBefore)->startOfDay();

        $this->info("Looking for subscriptions expiring on: " . $targetDate->format('Y-m-d'));

        // Find tenants expiring in X days
        $expiringTenants = Tenant::where('is_active', true)
            ->whereNotNull('subscription_expires_at')
            ->whereDate('subscription_expires_at', '=', $targetDate)
            ->get();

        if ($expiringTenants->isEmpty()) {
            $this->info('No subscriptions expiring in ' . $daysBefore . ' days.');
            return Command::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($expiringTenants as $tenant) {
            // Check if reminder already sent today
            if (RenewalReminderLog::sentToday($tenant->id)) {
                $this->warn("Skipping {$tenant->company_name} - reminder already sent today");
                $skippedCount++;
                continue;
            }

            try {
                // Get tenant admin email
                $admin = $tenant->users()->where('role', 'company_admin')->first();

                if (!$admin) {
                    $this->error("No admin found for tenant: {$tenant->company_name}");
                    continue;
                }

                // Send email
                Mail::to($admin->email)->send(
                    new SubscriptionRenewalReminder($tenant, $daysBefore)
                );

                // Log the reminder
                RenewalReminderLog::create([
                    'tenant_id' => $tenant->id,
                    'sent_date' => today(),
                    'expiry_date' => $tenant->subscription_expires_at,
                    'days_before_expiry' => $daysBefore,
                ]);

                $this->info("✓ Sent reminder to {$tenant->company_name} ({$admin->email})");
                $sentCount++;

            } catch (\Exception $e) {
                $this->error("Failed to send to {$tenant->company_name}: " . $e->getMessage());
            }
        }

        $this->info("\n=== Summary ===");
        $this->info("Found: {$expiringTenants->count()} tenants");
        $this->info("Sent: {$sentCount} reminders");
        $this->info("Skipped: {$skippedCount} (already sent today)");

        return Command::SUCCESS;
    }
}
