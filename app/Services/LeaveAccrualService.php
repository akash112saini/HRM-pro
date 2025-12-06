<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveAccrualService
{
    /**
     * Accrue monthly leaves for all employees.
     */
    public function accrueMonthlyLeaves(): void
    {
        $currentYear = now()->year;

        $leaveTypes = LeaveType::where('accrual_type', 'monthly')
            ->where('annual_quota', '>', 0)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $employees = Employee::where('tenant_id', $leaveType->tenant_id)
                ->where('employment_status', '!=', 'terminated')
                ->get();

            $monthlyAccrual = $leaveType->annual_quota / 12;

            foreach ($employees as $employee) {
                $this->accrueLeaveForEmployee($employee, $leaveType, $currentYear, $monthlyAccrual);
            }
        }
    }

    /**
     * Accrue yearly leaves for all employees (typically run on Jan 1).
     */
    public function accrueYearlyLeaves(): void
    {
        $currentYear = now()->year;

        $leaveTypes = LeaveType::where('accrual_type', 'yearly')
            ->where('annual_quota', '>', 0)
            ->get();

        foreach ($leaveTypes as $leaveType) {
            $employees = Employee::where('tenant_id', $leaveType->tenant_id)
                ->where('employment_status', '!=', 'terminated')
                ->get();

            foreach ($employees as $employee) {
                $this->accrueLeaveForEmployee($employee, $leaveType, $currentYear, $leaveType->annual_quota);
            }
        }
    }

    /**
     * Accrue leave for a specific employee.
     */
    protected function accrueLeaveForEmployee(Employee $employee, LeaveType $leaveType, int $year, float $amount): void
    {
        DB::transaction(function () use ($employee, $leaveType, $year, $amount) {
            $balance = LeaveBalance::firstOrCreate(
                [
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ],
                [
                    'opening_balance' => 0,
                    'accrued' => 0,
                    'used' => 0,
                    'carry_forward' => 0,
                    'closing_balance' => 0,
                ]
            );

            $balance->accrued += $amount;
            $balance->closing_balance = $balance->opening_balance + $balance->accrued + $balance->carry_forward - $balance->used;
            $balance->save();
        });
    }

    /**
     * Process carry forward of unused leaves to next year.
     */
    public function processCarryForward(int $fromYear): void
    {
        $toYear = $fromYear + 1;

        $leaveTypes = LeaveType::where('max_carry_forward', '>', 0)->get();

        foreach ($leaveTypes as $leaveType) {
            $balances = LeaveBalance::where('tenant_id', $leaveType->tenant_id)
                ->where('leave_type_id', $leaveType->id)
                ->where('year', $fromYear)
                ->get();

            foreach ($balances as $balance) {
                $carryForwardAmount = min(
                    $balance->closing_balance,
                    $leaveType->max_carry_forward
                );

                if ($carryForwardAmount > 0) {
                    $newBalance = LeaveBalance::firstOrCreate(
                        [
                            'tenant_id' => $balance->tenant_id,
                            'employee_id' => $balance->employee_id,
                            'leave_type_id' => $leaveType->id,
                            'year' => $toYear,
                        ],
                        [
                            'opening_balance' => 0,
                            'accrued' => 0,
                            'used' => 0,
                            'carry_forward' => 0,
                            'closing_balance' => 0,
                        ]
                    );

                    $newBalance->carry_forward = $carryForwardAmount;
                    $newBalance->closing_balance = $newBalance->opening_balance + $newBalance->accrued + $newBalance->carry_forward - $newBalance->used;
                    $newBalance->save();
                }
            }
        }
    }

    /**
     * Deduct leave from balance when leave request is approved.
     */
    public function deductLeave(LeaveRequest $leaveRequest): void
    {
        $year = Carbon::parse($leaveRequest->start_date)->year;

        DB::transaction(function () use ($leaveRequest, $year) {
            $balance = LeaveBalance::where('tenant_id', $leaveRequest->tenant_id)
                ->where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $year)
                ->first();

            if (!$balance) {
                throw new \Exception("Leave balance not found for employee");
            }

            if ($balance->closing_balance < $leaveRequest->total_days) {
                throw new \Exception("Insufficient leave balance");
            }

            $balance->used += $leaveRequest->total_days;
            $balance->closing_balance = $balance->opening_balance + $balance->accrued + $balance->carry_forward - $balance->used;
            $balance->save();
        });
    }

    /**
     * Restore leave balance when leave request is cancelled/rejected.
     */
    public function restoreLeave(LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->status !== 'approved') {
            return; // Only restore if it was previously approved
        }

        $year = Carbon::parse($leaveRequest->start_date)->year;

        DB::transaction(function () use ($leaveRequest, $year) {
            $balance = LeaveBalance::where('tenant_id', $leaveRequest->tenant_id)
                ->where('employee_id', $leaveRequest->employee_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', $year)
                ->first();

            if ($balance) {
                $balance->used -= $leaveRequest->total_days;
                $balance->closing_balance = $balance->opening_balance + $balance->accrued + $balance->carry_forward - $balance->used;
                $balance->save();
            }
        });
    }

    /**
     * Get available leave balance for an employee.
     */
    public function getAvailableBalance(Employee $employee, LeaveType $leaveType, ?int $year = null): float
    {
        $year = $year ?? now()->year;

        $balance = LeaveBalance::where('tenant_id', $employee->tenant_id)
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $year)
            ->first();

        return $balance ? $balance->closing_balance : 0;
    }
}
