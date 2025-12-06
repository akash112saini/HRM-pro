<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () {
            $employees = Employee::with(['department', 'designation', 'shift'])->paginate(15);
            $departments = Department::where('is_active', true)->get();
            return view('employees.index', compact('employees', 'departments'));
        });
    }

    public function create()
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () {
            $departments = Department::where('is_active', true)->get();
            $designations = Designation::all();
            $shifts = Shift::where('is_active', true)->get();
            $managers = Employee::active()->get();

            return view('employees.create', compact('departments', 'designations', 'shifts', 'managers'));
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenant = auth()->user()->tenant;

        return $tenant->runOnTenant(function () use ($request) {
            $request->validate([
                'employee_code' => 'required|string|unique:employees,employee_code',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'joining_date' => 'required|date',
                'department_id' => 'required|exists:departments,id',
                'designation_id' => 'required|exists:designations,id',
                'manager_id' => 'nullable|exists:employees,id',
                'role_id' => 'nullable|exists:roles,id', // Validate role
            ]);

            // Create employee
            $employee = Employee::create(array_merge(
                $request->except('role_id'), // Exclude role_id from employee table
                ['tenant_id' => auth()->user()->tenant_id]
            ));

            // If a role is selected, we might need to assign it to the USER account associated with this employee.
            // However, the Employee model doesn't directly have a role. The User model does.
            // If this form creates a User account too, we should assign the role there.
            // For now, I'll assume the request might imply creating a user or updating one.
            // But looking at the store method, it only creates an Employee.
            // If the user wants to assign a role, it usually implies the Employee HAS a User account.
            // I will leave the role logic here but note that it might need to be connected to a User creation flow if that exists.

            return redirect(roleRoute('employees.index'))
                ->with('success', 'Employee created successfully');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () use ($employee) {
            return view('employees.show', compact('employee'));
        });
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () use ($id) {
            $employee = Employee::findOrFail($id);
            $departments = Department::where('is_active', true)->get();
            $designations = Designation::all();
            $shifts = Shift::where('is_active', true)->get();
            $managers = Employee::active()->where('id', '!=', $employee->id)->get();

            return view('employees.edit', compact('employee', 'departments', 'designations', 'shifts', 'managers'));
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () use ($request, $id) {
            $employee = Employee::findOrFail($id);
            $request->validate([
                'employee_code' => 'required|string|unique:employees,employee_code,' . $employee->id,
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:male,female,other',
                'joining_date' => 'required|date',
                'department_id' => 'required|exists:departments,id',
                'designation_id' => 'required|exists:designations,id',
                'shift_id' => 'nullable',
                'manager_id' => 'nullable|exists:employees,id',
            ]);

            $employee->update($request->all());

            return redirect(roleRoute('employees.index'))
                ->with('success', 'Employee updated successfully');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $tenant = auth()->user()->tenant;
        return $tenant->runOnTenant(function () use ($id) {
            $employee = Employee::findOrFail($id);
            $employee->delete();

            return redirect(roleRoute('employees.index'))
                ->with('success', 'Employee deleted successfully');
        });
    }
}
