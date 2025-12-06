<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\Employee;
use App\Services\AttendanceCalculatorService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected AttendanceCalculatorService $attendanceService;

    public function __construct(AttendanceCalculatorService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display attendance listing.
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['employee', 'shift']);

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('employee_id')
            ->paginate(50);

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('attendance.index', compact('attendances', 'employees'));
    }

    /**
     * Store manual punch (Unified method).
     */
    public function store(Request $request)
    {
        if ($request->punch_type === 'in') {
            return $this->punchIn($request);
        }
        return $this->punchOut($request);
    }

    /**
     * Manual punch in.
     */
    public function punchIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'punch_time' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $punchTime = $request->punch_time ? Carbon::parse($request->punch_time) : now();

        $attendance = Attendance::firstOrCreate(
            [
                'tenant_id' => $employee->tenant_id,
                'employee_id' => $employee->id,
                'date' => $punchTime->toDateString(),
            ],
            [
                'shift_id' => $employee->shift_id,
                'status' => 'present',
            ]
        );

        $attendance->punch_in = $punchTime;
        $attendance->save();

        $this->attendanceService->calculateAttendanceMetrics($attendance);

        return redirect()->back()->with('success', 'Punch in recorded successfully');
    }

    /**
     * Manual punch out.
     */
    public function punchOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'punch_time' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $punchTime = $request->punch_time ? Carbon::parse($request->punch_time) : now();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $punchTime->toDateString())
            ->firstOrFail();

        $attendance->punch_out = $punchTime;
        $attendance->save();

        $this->attendanceService->calculateAttendanceMetrics($attendance);

        return redirect()->back()->with('success', 'Punch out recorded successfully');
    }

    /**
     * Request attendance correction.
     */
    public function requestCorrection(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'requested_punch_in' => 'nullable|date_format:Y-m-d H:i:s',
            'requested_punch_out' => 'nullable|date_format:Y-m-d H:i:s',
            'reason' => 'required|string|max:500',
        ]);

        $attendance = Attendance::findOrFail($request->attendance_id);

        AttendanceCorrection::create([
            'tenant_id' => $attendance->tenant_id,
            'attendance_id' => $attendance->id,
            'employee_id' => $attendance->employee_id,
            'requested_punch_in' => $request->requested_punch_in,
            'requested_punch_out' => $request->requested_punch_out,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Correction request submitted successfully');
    }

    /**
     * Approve correction request.
     */
    public function approveCorrection($id)
    {
        $this->attendanceService->approveCorrection($id, auth()->id());

        return redirect()->back()->with('success', 'Correction approved successfully');
    }

    /**
     * Reject correction request.
     */
    public function rejectCorrection(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $correction = AttendanceCorrection::findOrFail($id);
        $correction->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Correction rejected');
    }

    /**
     * Get attendance punch times for a specific date (AJAX endpoint).
     */
    public function getPunchTimes(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
        ]);

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'found' => false,
                'message' => 'No attendance record found for this date',
            ]);
        }

        return response()->json([
            'found' => true,
            'punch_in' => $attendance->punch_in ? $attendance->punch_in->format('H:i') : null,
            'punch_out' => $attendance->punch_out ? $attendance->punch_out->format('H:i') : null,
            'attendance_id' => $attendance->id,
        ]);
    }
}
