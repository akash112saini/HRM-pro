<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Services\PayrollGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyPayroll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $month;
    public int $year;
    public ?int $tenantId;
    public ?array $employeeIds;

    /**
     * Create a new job instance.
     */
    public function __construct(int $month, int $year, ?int $tenantId = null, ?array $employeeIds = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->tenantId = $tenantId;
        $this->employeeIds = $employeeIds;
    }

    /**
     * Execute the job.
     */
    public function handle(PayrollGeneratorService $payrollService): void
    {
        Log::info("Starting payroll generation", [
            'month' => $this->month,
            'year' => $this->year,
            'tenant_id' => $this->tenantId,
        ]);

        try {
            // Set tenant context if provided
            if ($this->tenantId) {
                app()->instance('tenant.id', $this->tenantId);
            }

            $results = $payrollService->generateBulkPayroll(
                $this->month,
                $this->year,
                $this->employeeIds
            );

            $successCount = $results->where('status', 'success')->count();
            $failureCount = $results->where('status', 'failed')->count();

            Log::info("Payroll generation completed", [
                'success' => $successCount,
                'failed' => $failureCount,
            ]);

            // Dispatch email jobs for successful payrolls
            foreach ($results->where('status', 'success') as $result) {
                SendPayslipEmail::dispatch($result['payroll_id']);
            }

        } catch (\Exception $e) {
            Log::error("Payroll generation failed: " . $e->getMessage(), [
                'month' => $this->month,
                'year' => $this->year,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
