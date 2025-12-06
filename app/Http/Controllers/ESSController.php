<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ESSController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return view('ess.dashboard', ['error' => 'Employee record not found.']);
        }

        // Today's Attendance
        $todayAttendance = \App\Models\Attendance::where('employee_id', $employee->id)
            ->where('date', now()->format('Y-m-d'))
            ->first();

        // Recent Leaves
        $recentLeaves = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent Payslips
        $recentPayslips = \App\Models\Payroll::where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(3)
            ->get();

        return view('ess.dashboard', compact('todayAttendance', 'recentLeaves', 'recentPayslips'));
    }

    public function attendance(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendances = \App\Models\Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $stats = [
            'present' => $attendances->where('status', 'present')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'late' => $attendances->where('is_late', true)->count(),
            'avg_hours' => $attendances->where('status', 'present')->avg('total_work_hours') ?? 0,
        ];

        // Get correction history for this employee
        $corrections = \App\Models\AttendanceCorrection::with('attendance')
            ->where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('ess.attendance', compact('attendances', 'stats', 'month', 'year', 'corrections'));
    }

    public function leaves()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $leaveTypes = \App\Models\LeaveType::all();

        // Calculate balances (simplified logic, ideally use a service)
        $balances = [];
        $leaveService = app(\App\Services\LeaveAccrualService::class);
        foreach ($leaveTypes as $type) {
            $balances[$type->id] = $leaveService->getAvailableBalance($employee, $type);
        }

        $leaveRequests = \App\Models\LeaveRequest::where('employee_id', $employee->id)
            ->with('leaveType')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('ess.leaves', compact('leaveTypes', 'balances', 'leaveRequests'));
    }

    public function payslips()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $payrolls = \App\Models\Payroll::where('employee_id', $employee->id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        return view('ess.payslips', compact('payrolls'));
    }

    public function documents()
    {
        $user = Auth::user();
        $employee = $user->employee;

        $documents = \App\Models\EmployeeDocument::where('employee_id', $employee->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ess.documents', compact('documents'));
    }

    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('ess.profile', compact('employee'));
    }

    public function downloadPayslip($id)
    {
        $user = Auth::user();
        $employee = $user->employee;

        $payroll = \App\Models\Payroll::where('id', $id)
            ->where('employee_id', $employee->id)
            ->with(['employee.user', 'employee.designation', 'employee.department'])
            ->firstOrFail();

        // Return a printable HTML view instead of PDF
        return view('payslips.download', compact('payroll', 'employee'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
