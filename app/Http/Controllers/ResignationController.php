<?php

namespace App\Http\Controllers;

use App\Models\Resignation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResignationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Resignation::with(['employee.user', 'employee.department', 'managerApprover', 'adminApprover']);

        // Filter based on user role
        if ($user->role === 'employee') {
            // Employee sees only their own resignations
            $query->where('employee_id', $user->employee->id);
        } elseif ($user->role === 'manager') {
            // Manager sees:
            // 1. Their own resignations
            // 2. Resignations of employees under them (pending manager approval)
            $query->where(function ($q) use ($user) {
                $q->where('employee_id', $user->employee->id)
                    ->orWhereHas('employee', function ($q2) use ($user) {
                        $q2->where('manager_id', $user->employee->id);
                    });
            });
        }
        // Admin sees all resignations

        $resignations = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('resignations.index', compact('resignations'));
    }

    public function create()
    {
        return view('resignations.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $request->validate([
            'resignation_date' => 'required|date',
            'last_working_day' => 'required|date|after:resignation_date',
            'reason' => 'required|string|min:10',
        ]);

        // Check if employee is also a manager
        $isManager = ($user->role === 'manager');

        $resignation = Resignation::create([
            'tenant_id' => auth()->user()->tenant_id,
            'employee_id' => $user->employee->id,
            'resignation_date' => $request->resignation_date,
            'last_working_day' => $request->last_working_day,
            'reason' => $request->reason,
            // If user is a manager, skip manager approval
            'manager_status' => $isManager ? 'approved' : 'pending',
            'admin_status' => 'pending',
            'final_status' => 'pending',
        ]);

        return redirect(roleRoute('resignations.index'))->with('success', 'Resignation submitted successfully.');
    }

    public function show(Resignation $resignation)
    {
        $resignation->load(['employee.user', 'employee.department', 'employee.designation', 'managerApprover', 'adminApprover']);

        return view('resignations.show', compact('resignation'));
    }

    public function approveByManager(Request $request, Resignation $resignation)
    {
        $user = Auth::user();

        if ($user->role !== 'manager') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string',
        ]);

        if ($request->action === 'approve') {
            $resignation->update([
                'manager_status' => 'approved',
                'manager_approved_by' => $user->id,
                'manager_approved_at' => now(),
                'manager_remarks' => $request->remarks,
            ]);

            return redirect(roleRoute('resignations.show', $resignation))->with('success', 'Resignation approved and forwarded to admin.');
        } else {
            $resignation->update([
                'manager_status' => 'rejected',
                'manager_approved_by' => $user->id,
                'manager_approved_at' => now(),
                'manager_remarks' => $request->remarks,
                'final_status' => 'rejected',
            ]);

            return redirect(roleRoute('resignations.show', $resignation))->with('success', 'Resignation rejected.');
        }
    }

    public function approveByAdmin(Request $request, Resignation $resignation)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['super_admin', 'company_admin'])) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'remarks' => 'nullable|string',
        ]);

        if ($request->action === 'approve') {
            $resignation->update([
                'admin_status' => 'approved',
                'admin_approved_by' => $user->id,
                'admin_approved_at' => now(),
                'admin_remarks' => $request->remarks,
                'final_status' => 'approved',
            ]);

            return redirect(roleRoute('resignations.show', $resignation))->with('success', 'Resignation approved successfully.');
        } else {
            $resignation->update([
                'admin_status' => 'rejected',
                'admin_approved_by' => $user->id,
                'admin_approved_at' => now(),
                'admin_remarks' => $request->remarks,
                'final_status' => 'rejected',
            ]);

            return redirect(roleRoute('resignations.show', $resignation))->with('success', 'Resignation rejected.');
        }
    }
}
