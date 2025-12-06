<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeaveAccrualService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    protected LeaveAccrualService $leaveService;

    public function __construct(LeaveAccrualService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Display leave requests.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // For managers, show only their team's requests
        if (auth()->user()->role === 'manager') {
            $managerId = auth()->user()->employee->id ?? null;
            $query->whereHas('employee', function ($q) use ($managerId) {
                $q->where('manager_id', $managerId);
            });
        }

        // Filter by Month and Year
        if ($request->filled('month')) {
            $query->whereMonth('start_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $leaveRequests = $query->orderBy('applied_at', 'desc')->paginate(20);

        return view('leaves.index', compact('leaveRequests'));
    }

    /**
     * Show leave application form.
     */
    public function create()
    {
        $leaveTypes = LeaveType::all();
        $employee = auth()->user()->employee;

        // Get leave balances
        $balances = [];
        foreach ($leaveTypes as $type) {
            $balances[$type->id] = $this->leaveService->getAvailableBalance($employee, $type);
        }

        return view('leaves.create', compact('leaveTypes', 'balances'));
    }

    /**
     * Store leave request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
        ]);

        $employee = auth()->user()->employee;
        $leaveType = LeaveType::findOrFail($request->leave_type_id);

        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check available balance
        $availableBalance = $this->leaveService->getAvailableBalance($employee, $leaveType);

        if ($totalDays > $availableBalance) {
            return redirect()->back()
                ->with('error', 'Insufficient leave balance')
                ->withInput();
        }

        LeaveRequest::create([
            'tenant_id' => $employee->tenant_id,
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
            'applied_at' => now(),
        ]);

        return redirect(roleRoute('leaves.index'))
            ->with('success', 'Leave request submitted successfully');
    }

    /**
     * Approve leave request.
     */
    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Deduct from balance
        $this->leaveService->deductLeave($leaveRequest);

        return redirect()->back()->with('success', 'Leave request approved');
    }

    /**
     * Reject leave request.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leaveRequest = LeaveRequest::findOrFail($id);

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->back()->with('success', 'Leave request rejected');
    }

    /**
     * Cancel leave request.
     */
    public function cancel($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Only allow cancellation by the employee who applied
        if ($leaveRequest->employee_id !== auth()->user()->employee->id) {
            abort(403);
        }

        // Restore balance if it was approved
        if ($leaveRequest->status === 'approved') {
            $this->leaveService->restoreLeave($leaveRequest);
        }

        $leaveRequest->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Leave request cancelled');
    }
}
