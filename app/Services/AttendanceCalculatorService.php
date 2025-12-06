<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BiometricDevice;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceCalculatorService
{
    /**
     * Process biometric device data and create/update attendance.
     */
    public function syncBiometricData(array $data): Attendance
    {
        // Validate device
        $device = BiometricDevice::where('device_id', $data['device_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Find employee by code
        $employee = Employee::where('tenant_id', $device->tenant_id)
            ->where('employee_code', $data['employee_code'])
            ->where('employment_status', '!=', 'terminated')
            ->firstOrFail();

        $timestamp = Carbon::parse($data['timestamp']);
        $date = $timestamp->toDateString();

        // Get or create attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'tenant_id' => $device->tenant_id,
                'employee_id' => $employee->id,
                'date' => $date,
            ],
            [
                'shift_id' => $employee->shift_id,
                'status' => 'present',
            ]
        );

        // Determine if this is punch in or punch out
        if (!$attendance->punch_in) {
            $attendance->punch_in = $timestamp;
            $attendance->punch_in_device_id = $device->device_id;

            if (isset($data['latitude']) && isset($data['longitude'])) {
                $attendance->punch_in_location = [
                    'lat' => $data['latitude'],
                    'lng' => $data['longitude'],
                ];
            }
        } else {
            $attendance->punch_out = $timestamp;
            $attendance->punch_out_device_id = $device->device_id;

            if (isset($data['latitude']) && isset($data['longitude'])) {
                $attendance->punch_out_location = [
                    'lat' => $data['latitude'],
                    'lng' => $data['longitude'],
                ];
            }
        }

        $attendance->save();

        // Calculate attendance metrics
        $this->calculateAttendanceMetrics($attendance);

        // Update device last sync
        $device->update(['last_sync_at' => now()]);

        return $attendance->fresh();
    }

    /**
     * Calculate all attendance metrics (work hours, late, early departure, overtime).
     */
    public function calculateAttendanceMetrics(Attendance $attendance): void
    {
        if (!$attendance->punch_in) {
            return;
        }

        $shift = $attendance->shift ?? $attendance->employee->shift;

        if (!$shift) {
            Log::warning("No shift assigned for employee {$attendance->employee_id}");
            return;
        }

        // Calculate work hours
        $this->calculateWorkHours($attendance);

        // Check late arrival
        $this->checkLateArrival($attendance, $shift);

        // Check early departure
        if ($attendance->punch_out) {
            $this->checkEarlyDeparture($attendance, $shift);
            $this->calculateOvertime($attendance, $shift);
        }

        $attendance->save();
    }

    /**
     * Calculate total work hours.
     */
    protected function calculateWorkHours(Attendance $attendance): void
    {
        if (!$attendance->punch_in || !$attendance->punch_out) {
            $attendance->total_work_hours = 0;
            return;
        }

        $punchIn = Carbon::parse($attendance->punch_in);
        $punchOut = Carbon::parse($attendance->punch_out);

        // Calculate total hours
        $totalMinutes = $punchOut->diffInMinutes($punchIn);

        // Deduct break time (assuming 1 hour break for shifts > 6 hours)
        $breakMinutes = $totalMinutes > 360 ? 60 : 0;

        $workMinutes = $totalMinutes - $breakMinutes;
        $attendance->total_work_hours = round($workMinutes / 60, 2);
    }

    /**
     * Check if employee arrived late.
     */
    protected function checkLateArrival(Attendance $attendance, Shift $shift): void
    {
        $punchIn = Carbon::parse($attendance->punch_in);
        $shiftStart = Carbon::parse($attendance->date . ' ' . $shift->start_time);

        // Add grace period
        $shiftStartWithGrace = $shiftStart->copy()->addMinutes($shift->grace_period_minutes);

        if ($punchIn->greaterThan($shiftStartWithGrace)) {
            $attendance->is_late = true;
            $attendance->late_minutes = $punchIn->diffInMinutes($shiftStart);
        } else {
            $attendance->is_late = false;
            $attendance->late_minutes = 0;
        }
    }

    /**
     * Check if employee left early.
     */
    protected function checkEarlyDeparture(Attendance $attendance, Shift $shift): void
    {
        $punchOut = Carbon::parse($attendance->punch_out);
        $shiftEnd = Carbon::parse($attendance->date . ' ' . $shift->end_time);

        if ($punchOut->lessThan($shiftEnd)) {
            $attendance->is_early_departure = true;
            $attendance->early_departure_minutes = $shiftEnd->diffInMinutes($punchOut);
        } else {
            $attendance->is_early_departure = false;
            $attendance->early_departure_minutes = 0;
        }
    }

    /**
     * Calculate overtime hours.
     */
    protected function calculateOvertime(Attendance $attendance, Shift $shift): void
    {
        $punchOut = Carbon::parse($attendance->punch_out);
        $shiftEnd = Carbon::parse($attendance->date . ' ' . $shift->end_time);

        if ($punchOut->greaterThan($shiftEnd)) {
            $overtimeMinutes = $punchOut->diffInMinutes($shiftEnd);
            $attendance->overtime_hours = round($overtimeMinutes / 60, 2);
        } else {
            $attendance->overtime_hours = 0;
        }
    }

    /**
     * Process attendance status for employees without punch (mark absent, handle leaves/holidays).
     */
    public function processAttendanceStatus(Employee $employee, Carbon $date): void
    {
        $attendance = Attendance::where('tenant_id', $employee->tenant_id)
            ->where('employee_id', $employee->id)
            ->where('date', $date->toDateString())
            ->first();

        // Check if it's a holiday
        $isHoliday = Holiday::where('tenant_id', $employee->tenant_id)
            ->where('date', $date->toDateString())
            ->exists();

        if ($isHoliday) {
            if ($attendance) {
                $attendance->update(['status' => 'holiday']);
            } else {
                Attendance::create([
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'shift_id' => $employee->shift_id,
                    'status' => 'holiday',
                ]);
            }
            return;
        }

        // Check if it's a week off
        $shift = $employee->shift;
        if ($shift && $shift->working_days) {
            $workingDays = is_string($shift->working_days)
                ? json_decode($shift->working_days, true)
                : $shift->working_days;

            $dayOfWeek = $date->dayOfWeekIso; // 1 (Monday) to 7 (Sunday)

            if (!in_array($dayOfWeek, $workingDays)) {
                if ($attendance) {
                    $attendance->update(['status' => 'week_off']);
                } else {
                    Attendance::create([
                        'tenant_id' => $employee->tenant_id,
                        'employee_id' => $employee->id,
                        'date' => $date->toDateString(),
                        'shift_id' => $employee->shift_id,
                        'status' => 'week_off',
                    ]);
                }
                return;
            }
        }

        // Check if employee is on approved leave
        $onLeave = LeaveRequest::where('tenant_id', $employee->tenant_id)
            ->where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date->toDateString())
            ->where('end_date', '>=', $date->toDateString())
            ->exists();

        if ($onLeave) {
            if ($attendance) {
                $attendance->update(['status' => 'on_leave']);
            } else {
                Attendance::create([
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'shift_id' => $employee->shift_id,
                    'status' => 'on_leave',
                ]);
            }
            return;
        }

        // If no punch and not holiday/week_off/leave, mark as absent
        if (!$attendance || !$attendance->punch_in) {
            if ($attendance) {
                $attendance->update(['status' => 'absent']);
            } else {
                Attendance::create([
                    'tenant_id' => $employee->tenant_id,
                    'employee_id' => $employee->id,
                    'date' => $date->toDateString(),
                    'shift_id' => $employee->shift_id,
                    'status' => 'absent',
                ]);
            }
        }
    }

    /**
     * Batch process attendance for all employees for a specific date.
     */
    public function batchProcessAttendance(Carbon $date, ?int $tenantId = null): void
    {
        $query = Employee::where('employment_status', '!=', 'terminated');

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $employees = $query->get();

        foreach ($employees as $employee) {
            try {
                $this->processAttendanceStatus($employee, $date);
            } catch (\Exception $e) {
                Log::error("Failed to process attendance for employee {$employee->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Approve attendance correction request.
     */
    public function approveCorrection(int $correctionId, int $approvedBy): bool
    {
        return DB::transaction(function () use ($correctionId, $approvedBy) {
            $correction = \App\Models\AttendanceCorrection::findOrFail($correctionId);

            $correction->update([
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ]);

            // Update attendance record
            $attendance = $correction->attendance;

            if ($correction->requested_punch_in) {
                $attendance->punch_in = $correction->requested_punch_in;
            }

            if ($correction->requested_punch_out) {
                $attendance->punch_out = $correction->requested_punch_out;
            }

            $attendance->save();

            // Recalculate metrics
            $this->calculateAttendanceMetrics($attendance);

            return true;
        });
    }
}
