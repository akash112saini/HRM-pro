<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Department;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::with('department')
            ->orderBy('department_id')
            ->orderBy('level')
            ->get();
        return view('designations.index', compact('designations'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('designations.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1',
        ]);

        Designation::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect(roleRoute('designations.index'))
            ->with('success', 'Designation created successfully.');
    }

    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        return view('designations.edit', compact('designation', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'level' => 'required|integer|min:1',
        ]);

        $designation->update($request->all());

        return redirect(roleRoute('designations.index'))
            ->with('success', 'Designation updated successfully.');
    }

    public function destroy($id)
    {
        $designation = Designation::findOrFail($id);
        $designation->delete();
        return redirect(roleRoute('designations.index'))
            ->with('success', 'Designation deleted successfully.');
    }
}
