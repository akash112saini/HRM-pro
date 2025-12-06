<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process daily attendance (mark absent for no-shows)
        $schedule->call(function () {
            \App\Services\AttendanceCalculatorService::class;
            $service = app(\App\Services\AttendanceCalculatorService::class);
            $service->batchProcessAttendance(\Carbon\Carbon::yesterday());
        })->dailyAt('01:00')->name('process-daily-attendance');

        // Accrue monthly leaves (1st of every month)
        $schedule->job(new \App\Jobs\ProcessLeaveAccrual('monthly'))
            ->monthlyOn(1, '02:00')
            ->name('accrue-monthly-leaves');

        // Accrue yearly leaves (January 1st)
        $schedule->job(new \App\Jobs\ProcessLeaveAccrual('yearly'))
            ->yearlyOn(1, 1, '03:00')
            ->name('accrue-yearly-leaves');

        // Process leave carry forward (January 1st)
        $schedule->call(function () {
            $service = app(\App\Services\LeaveAccrualService::class);
            $service->processCarryForward(now()->subYear()->year);
        })->yearlyOn(1, 1, '04:00')->name('process-leave-carry-forward');

        // Optional: Auto-generate payroll on 1st of month
        // Uncomment if you want automatic payroll generation
        // $schedule->job(new \App\Jobs\GenerateMonthlyPayroll(
        //     now()->subMonth()->month,
        //     now()->subMonth()->year
        // ))->monthlyOn(1, '05:00')->name('generate-monthly-payroll');

        // Auto-suspend expired subscriptions (daily at 2 AM)
        $schedule->call(function () {
            if (Setting::get('auto_suspend_expired', true)) {
                $gracePeriodDays = (int) Setting::get('grace_period_days', 7);
                $expiredDate = now()->subDays($gracePeriodDays);

                \App\Models\Tenant::where('is_active', true)
                    ->whereNotNull('subscription_expires_at')
                    ->where('subscription_expires_at', '<', $expiredDate)
                    ->update(['is_active' => false]);

                \Log::info('Auto-suspended expired subscriptions', [
                    'grace_period_days' => $gracePeriodDays,
                    'checked_date' => $expiredDate->toDateString()
                ]);
            }
        }) - \u003edailyAt('02:00')->name('auto-suspend-expired-subscriptions');

        // Send subscription renewal reminders (daily at 9 AM)
        $schedule->command('reminders:renewal --days=7')
            ->dailyAt('09:00')
            ->name('send-renewal-reminders');

        // Clean up old notifications (older than 90 days)
        $schedule->command('notifications:clean')
            ->weekly()
            ->name('clean-old-notifications');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
