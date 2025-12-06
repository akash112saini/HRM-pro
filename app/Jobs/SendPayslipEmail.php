<?php

namespace App\Jobs;

use App\Models\Payroll;
use App\Notifications\PayslipGenerated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPayslipEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $payrollId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $payrollId)
    {
        $this->payrollId = $payrollId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payroll = Payroll::with('employee.user')->findOrFail($this->payrollId);

            if ($payroll->employee->user) {
                $payroll->employee->user->notify(new PayslipGenerated($payroll));

                Log::info("Payslip email sent", [
                    'payroll_id' => $this->payrollId,
                    'employee_id' => $payroll->employee_id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to send payslip email: " . $e->getMessage(), [
                'payroll_id' => $this->payrollId,
            ]);

            throw $e;
        }
    }
}
