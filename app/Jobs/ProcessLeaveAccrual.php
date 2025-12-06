<?php

namespace App\Jobs;

use App\Services\LeaveAccrualService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessLeaveAccrual implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $accrualType; // 'monthly' or 'yearly'

    /**
     * Create a new job instance.
     */
    public function __construct(string $accrualType = 'monthly')
    {
        $this->accrualType = $accrualType;
    }

    /**
     * Execute the job.
     */
    public function handle(LeaveAccrualService $leaveService): void
    {
        Log::info("Starting leave accrual process", [
            'type' => $this->accrualType,
        ]);

        try {
            if ($this->accrualType === 'monthly') {
                $leaveService->accrueMonthlyLeaves();
            } else {
                $leaveService->accrueYearlyLeaves();
            }

            Log::info("Leave accrual completed successfully");

        } catch (\Exception $e) {
            Log::error("Leave accrual failed: " . $e->getMessage(), [
                'type' => $this->accrualType,
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
