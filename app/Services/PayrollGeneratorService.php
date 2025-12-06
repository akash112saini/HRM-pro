<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use App\Models\TaxSlab;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollGeneratorService
{
    protected float $overtimeMultiplier = 1.5; // 1.5x hourly rate for overtime

    /**
     * Generate payroll for a single employee.
     */
    public function generatePayroll(Employee $employee, int $month, int $year): Payroll
    {
        return DB::transaction(function () use ($employee, $month, $year) {
            // Check if payroll already exists
            $existing = Payroll::where('tenant_id', $employee->tenant_id)
                ->where('employee_id', $employee->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existing && $existing->status !== 'draft') {
                throw new \Exception("Payroll already processed for this period");
            }

            // Get active salary structure
            $salary = $this->getActiveSalary($employee, $month, $year);

            if (!$salary) {
                throw new \Exception("No active salary structure found for employee");
            }

            // Calculate working days and attendance
            $attendanceData = $this->calculateAttendanceData($employee, $month, $year);

            // Calculate earnings
            $earnings = $this->calculateEarnings($employee, $salary, $attendanceData);

            // Calculate deductions
            $deductions = $this->calculateDeductions($employee, $earnings, $month, $year, $attendanceData);

            // Calculate net salary
            $totalEarnings = array_sum($earnings);
            $totalDeductions = array_sum($deductions);
            $netSalary = $totalEarnings - $totalDeductions;

            // Create or update payroll
            $payroll = Payroll::updateOrCreate(
                [
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'salary_structure_id' => $salary->salary_structure_id,
                    'basic_salary' => $salary->basic_salary,
                    'gross_salary' => $salary->gross_salary,
                    'net_salary' => $netSalary,
                    'earnings' => $earnings,
                    'deductions' => $deductions,
                    'total_working_days' => $attendanceData['total_working_days'],
                    'present_days' => $attendanceData['present_days'],
                    'absent_days' => $attendanceData['absent_days'],
                    'leave_days' => $attendanceData['leave_days'],
                    'loss_of_pay_amount' => $deductions['loss_of_pay'] ?? 0,
                    'overtime_amount' => $earnings['overtime'] ?? 0,
                    'overtime_hours' => $attendanceData['overtime_hours'],
                    'tax_deducted' => $deductions['income_tax'] ?? 0,
                    'status' => 'draft',
                    'processed_at' => now(),
                ]
            );

            return $payroll;
        });
    }

    /**
     * Generate payroll for multiple employees.
     */
    public function generateBulkPayroll(int $month, int $year, ?array $employeeIds = null): Collection
    {
        $query = Employee::where('employment_status', '!=', 'terminated');

        if ($employeeIds) {
            $query->whereIn('id', $employeeIds);
        }

        $employees = $query->get();
        $results = collect();

        foreach ($employees as $employee) {
            try {
                $payroll = $this->generatePayroll($employee, $month, $year);
                $results->push([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'status' => 'success',
                    'payroll_id' => $payroll->id,
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to generate payroll for employee {$employee->id}: " . $e->getMessage());
                $results->push([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->first_name . ' ' . $employee->last_name,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Get active salary for the employee.
     */
    protected function getActiveSalary(Employee $employee, int $month, int $year): ?EmployeeSalary
    {
        $date = Carbon::create($year, $month, 1);

        return EmployeeSalary::where('employee_id', $employee->id)
            ->where('is_active', true)
            ->where('effective_from', '<=', $date)
            ->orderBy('effective_from', 'desc')
            ->first();
    }

    /**
     * Calculate attendance data for the month.
     */
    protected function calculateAttendanceData(Employee $employee, int $month, int $year): array
    {
        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Calculate total working days (excluding holidays and week offs)
        $totalDays = $startDate->daysInMonth;

        $holidays = Holiday::where('tenant_id', $employee->tenant_id)
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        // Calculate week offs based on shift
        $weekOffs = $this->calculateWeekOffs($employee, $startDate, $endDate);

        $totalWorkingDays = $totalDays - $holidays - $weekOffs;

        // Get attendance records
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $presentDays = $attendances->whereIn('status', ['present', 'half_day'])->count();
        $absentDays = $attendances->where('status', 'absent')->count();

        // Count paid and unpaid leaves
        $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->with('leaveType')
            ->get();

        $paidLeaveDays = 0;
        $unpaidLeaveDays = 0;

        foreach ($leaveRequests as $leave) {
            $leaveStart = Carbon::parse($leave->start_date)->max($startDate);
            $leaveEnd = Carbon::parse($leave->end_date)->min($endDate);
            $days = $leaveStart->diffInDays($leaveEnd) + 1;

            if ($leave->leaveType && $leave->leaveType->is_paid) {
                $paidLeaveDays += $days;
            } else {
                $unpaidLeaveDays += $days;
            }
        }

        $leaveDays = $paidLeaveDays + $unpaidLeaveDays;

        // Calculate overtime hours
        $overtimeHours = $attendances->sum('overtime_hours');

        return [
            'total_working_days' => $totalWorkingDays,
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'paid_leave_days' => $paidLeaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'overtime_hours' => $overtimeHours,
        ];
    }

    /**
     * Calculate week offs for the period.
     */
    protected function calculateWeekOffs(Employee $employee, Carbon $start, Carbon $end): int
    {
        $shift = $employee->shift;

        if (!$shift || !$shift->working_days) {
            return 0;
        }

        $workingDays = is_string($shift->working_days)
            ? json_decode($shift->working_days, true)
            : $shift->working_days;

        $weekOffs = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if (!in_array($current->dayOfWeekIso, $workingDays)) {
                $weekOffs++;
            }
            $current->addDay();
        }

        return $weekOffs;
    }

    /**
     * Calculate all earnings.
     */
    protected function calculateEarnings(Employee $employee, EmployeeSalary $salary, array $attendanceData): array
    {
        $earnings = [];

        // Basic salary (prorated based on attendance)
        $perDaySalary = $salary->basic_salary / $attendanceData['total_working_days'];
        $workingDays = $attendanceData['present_days'] + $attendanceData['paid_leave_days'];
        $earnings['basic_salary'] = round($perDaySalary * $workingDays, 2);

        // HRA (prorated)
        if ($salary->hra > 0) {
            $perDayHRA = $salary->hra / $attendanceData['total_working_days'];
            $earnings['hra'] = round($perDayHRA * $workingDays, 2);
        }

        // Conveyance (prorated)
        if ($salary->conveyance > 0) {
            $perDayConveyance = $salary->conveyance / $attendanceData['total_working_days'];
            $earnings['conveyance'] = round($perDayConveyance * $workingDays, 2);
        }

        // Medical (prorated)
        if ($salary->medical > 0) {
            $perDayMedical = $salary->medical / $attendanceData['total_working_days'];
            $earnings['medical'] = round($perDayMedical * $workingDays, 2);
        }

        // Special Allowance (prorated)
        if ($salary->special_allowance > 0) {
            $perDayAllowance = $salary->special_allowance / $attendanceData['total_working_days'];
            $earnings['special_allowance'] = round($perDayAllowance * $workingDays, 2);
        }

        // Overtime pay
        if ($attendanceData['overtime_hours'] > 0) {
            $hourlyRate = $salary->basic_salary / ($attendanceData['total_working_days'] * 8); // Assuming 8-hour workday
            $earnings['overtime'] = round($attendanceData['overtime_hours'] * $hourlyRate * $this->overtimeMultiplier, 2);
        }

        // Custom components from salary structure
        if ($salary->custom_components) {
            $customComponents = is_string($salary->custom_components)
                ? json_decode($salary->custom_components, true)
                : $salary->custom_components;

            foreach ($customComponents as $component) {
                if ($component['type'] === 'earning') {
                    $earnings[$component['name']] = $component['value'];
                }
            }
        }

        return $earnings;
    }

    /**
     * Calculate all deductions.
     */
    protected function calculateDeductions(Employee $employee, array $earnings, int $month, int $year, array $attendanceData): array
    {
        $deductions = [];

        // Loss of Pay (LOP)
        $lopAmount = $this->calculateLossOfPay($employee, $month, $year, $attendanceData);
        if ($lopAmount > 0) {
            $deductions['loss_of_pay'] = $lopAmount;
        }

        // Professional Tax (example: flat rate or slab-based)
        $grossEarnings = array_sum($earnings);
        if ($grossEarnings > 0) {
            $deductions['professional_tax'] = $this->calculateProfessionalTax($grossEarnings);
        }

        // Income Tax (TDS)
        $annualGross = $grossEarnings * 12; // Approximate annual income
        $taxAmount = $this->calculateTax($annualGross, $employee);
        if ($taxAmount > 0) {
            $deductions['income_tax'] = round($taxAmount / 12, 2); // Monthly TDS
        }

        // Provident Fund (if applicable) - typically 12% of basic
        $salary = $this->getActiveSalary($employee, $month, $year);
        if ($salary && $salary->basic_salary > 0) {
            $pfAmount = round($salary->basic_salary * 0.12, 2);
            $deductions['provident_fund'] = $pfAmount;
        }

        // Custom deductions from salary structure
        if ($salary && $salary->custom_components) {
            $customComponents = is_string($salary->custom_components)
                ? json_decode($salary->custom_components, true)
                : $salary->custom_components;

            foreach ($customComponents as $component) {
                if ($component['type'] === 'deduction') {
                    $deductions[$component['name']] = $component['value'];
                }
            }
        }

        return $deductions;
    }

    /**
     * Calculate Loss of Pay based on absences and unpaid leaves.
     */
    protected function calculateLossOfPay(Employee $employee, int $month, int $year, array $attendanceData): float
    {
        $salary = $this->getActiveSalary($employee, $month, $year);

        if (!$salary) {
            return 0;
        }

        $perDaySalary = $salary->gross_salary / $attendanceData['total_working_days'];
        $lopDays = $attendanceData['absent_days'] + $attendanceData['unpaid_leave_days'];

        return round($lopDays * $perDaySalary, 2);
    }

    /**
     * Calculate professional tax.
     */
    protected function calculateProfessionalTax(float $grossSalary): float
    {
        // Example slab-based professional tax
        if ($grossSalary <= 15000) {
            return 0;
        } elseif ($grossSalary <= 20000) {
            return 150;
        } else {
            return 200;
        }
    }

    /**
     * Calculate income tax based on tax slabs.
     */
    public function calculateTax(float $annualGross, Employee $employee): float
    {
        $currentYear = now()->year;
        $financialYear = now()->month >= 4 ? $currentYear . '-' . ($currentYear + 1) : ($currentYear - 1) . '-' . $currentYear;

        $taxSlabs = TaxSlab::where('tenant_id', $employee->tenant_id)
            ->where('financial_year', $financialYear)
            ->orderBy('min_amount')
            ->get();

        if ($taxSlabs->isEmpty()) {
            // Default Indian tax slabs (example)
            return $this->calculateDefaultTax($annualGross);
        }

        $tax = 0;
        $remainingIncome = $annualGross;

        foreach ($taxSlabs as $slab) {
            if ($remainingIncome <= 0) {
                break;
            }

            $slabMin = $slab->min_amount;
            $slabMax = $slab->max_amount ?? PHP_FLOAT_MAX;
            $taxRate = $slab->tax_rate / 100;

            if ($annualGross > $slabMin) {
                $taxableInSlab = min($remainingIncome, $slabMax - $slabMin);
                $tax += $taxableInSlab * $taxRate;
                $remainingIncome -= $taxableInSlab;
            }
        }

        return round($tax, 2);
    }

    /**
     * Calculate tax using default slabs (fallback).
     */
    protected function calculateDefaultTax(float $annualGross): float
    {
        $tax = 0;

        // Example: Indian tax slabs for FY 2024-25
        if ($annualGross <= 300000) {
            $tax = 0;
        } elseif ($annualGross <= 600000) {
            $tax = ($annualGross - 300000) * 0.05;
        } elseif ($annualGross <= 900000) {
            $tax = 15000 + ($annualGross - 600000) * 0.10;
        } elseif ($annualGross <= 1200000) {
            $tax = 45000 + ($annualGross - 900000) * 0.15;
        } elseif ($annualGross <= 1500000) {
            $tax = 90000 + ($annualGross - 1200000) * 0.20;
        } else {
            $tax = 150000 + ($annualGross - 1500000) * 0.30;
        }

        return round($tax, 2);
    }

    /**
     * Generate PDF payslip.
     */
    public function generatePayslipPDF(Payroll $payroll): string
    {
        $employee = $payroll->employee;
        $tenant = $payroll->tenant;

        $data = [
            'payroll' => $payroll,
            'employee' => $employee,
            'tenant' => $tenant,
            'month_year' => Carbon::create($payroll->year, $payroll->month, 1)->format('F Y'),
        ];

        $pdf = Pdf::loadView('payroll.payslip', $data);

        $filename = "payslip_{$employee->employee_code}_{$payroll->month}_{$payroll->year}.pdf";
        $path = storage_path("app/tenants/{$tenant->id}/payslips/{$filename}");

        // Ensure directory exists
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    /**
     * Mark payroll as paid.
     */
    public function markAsPaid(Payroll $payroll, string $paymentMethod, ?string $transactionReference = null): void
    {
        $payroll->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionReference,
        ]);
    }
}
