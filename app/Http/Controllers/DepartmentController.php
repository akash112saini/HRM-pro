<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display department listing.
     */
    public function index()
    {
        $departments = Department::with(['head', 'employees'])
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return view('departments.index', compact('departments'));
    }

    /**
     * Show department creation form.
     */
    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('departments.create', compact('employees'));
    }

    /**
     * Store new department.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'head_employee_id' => 'nullable|exists:employees,id',
        ]);

        Department::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect(roleRoute('departments.index'))
            ->with('success', 'Department created successfully');
    }

    /**
     * Display department details.
     */
    public function show($id)
    {
        $department = Department::with(['head', 'employees', 'designations'])
            ->findOrFail($id);

        return view('departments.show', compact('department'));
    }

    /**
     * Show department edit form.
     */
    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $employees = Employee::active()->orderBy('first_name')->get();

        return view('departments.edit', compact('department', 'employees'));
    }

    /**
     * Update department.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'head_employee_id' => 'nullable|exists:employees,id',
            'is_active' => 'boolean',
        ]);

        $department->update($request->all());

        return redirect(roleRoute('departments.show', $department))
            ->with('success', 'Department updated successfully');
    }

    /**
     * Delete department.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete department with active employees');
        }

        $department->delete();

        return redirect(roleRoute('departments.index'))
            ->with('success', 'Department deleted successfully');
    }
}
