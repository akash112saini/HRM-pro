<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return view('settings.leave-types.index', compact('leaveTypes'));
    }

    public function create()
    {
        return view('settings.leave-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:leave_types,code,NULL,id,tenant_id,' . auth()->user()->tenant_id,
            'annual_quota' => 'required|integer|min:0',
            'accrual_type' => 'required|in:yearly,monthly,none',
            'max_carry_forward' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'requires_approval' => 'boolean',
            'color' => 'required|string|max:7',
        ]);

        LeaveType::create(array_merge(
            $request->all(),
            ['tenant_id' => auth()->user()->tenant_id]
        ));

        return redirect()->route('admin.leave-types.index')->with('success', 'Leave Type created.');
    }

    public function edit(LeaveType $leaveType)
    {
        return view('settings.leave-types.edit', compact('leaveType'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:leave_types,code,' . $leaveType->id . ',id,tenant_id,' . auth()->user()->tenant_id,
            'annual_quota' => 'required|integer|min:0',
            'accrual_type' => 'required|in:yearly,monthly,none',
            'max_carry_forward' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'requires_approval' => 'boolean',
            'color' => 'required|string|max:7',
        ]);

        $leaveType->update($request->all());

        return redirect()->route('admin.leave-types.index')->with('success', 'Leave Type updated.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave-types.index')->with('success', 'Leave Type deleted.');
    }
}
