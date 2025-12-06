<?php

namespace App\Http\Controllers;

use App\Models\EmployeeGoal;
use App\Models\Employee;
use App\Models\AppraisalCycle;
use Illuminate\Http\Request;

class EmployeeGoalController extends Controller
{
    public function index()
    {
        $goals = EmployeeGoal::with(['employee', 'cycle'])->get();
        return view('performance.goals.index', compact('goals'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        $cycles = AppraisalCycle::active()->get();
        return view('performance.goals.create', compact('employees', 'cycles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_cycle_id' => 'required|exists:appraisal_cycles,id',
            'title' => 'required|string|max:255',
            'weightage' => 'required|numeric|min:0|max:100',
        ]);

        EmployeeGoal::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect()->route('goals.index')
            ->with('success', 'Goal created successfully.');
    }

    public function edit(EmployeeGoal $goal)
    {
        $employees = Employee::active()->get();
        $cycles = AppraisalCycle::active()->get();
        return view('performance.goals.edit', compact('goal', 'employees', 'cycles'));
    }

    public function update(Request $request, EmployeeGoal $goal)
    {
        $goal->update($request->all());
        return redirect()->route('goals.index')
            ->with('success', 'Goal updated successfully.');
    }

    public function destroy(EmployeeGoal $goal)
    {
        $goal->delete();
        return redirect()->route('goals.index')
            ->with('success', 'Goal deleted successfully.');
    }
}
