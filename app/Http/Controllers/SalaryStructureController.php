<?php

namespace App\Http\Controllers;

use App\Models\SalaryStructure;
use Illuminate\Http\Request;

class SalaryStructureController extends Controller
{
    public function index()
    {
        $structures = SalaryStructure::all();
        return view('settings.salary-structures.index', compact('structures'));
    }

    public function create()
    {
        return view('settings.salary-structures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'components' => 'required|array',
            'components.*.name' => 'required|string',
            'components.*.type' => 'required|in:fixed,percentage',
            'components.*.value' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        SalaryStructure::create(array_merge(
            $request->except('components'),
            [
                'components' => $request->components,
                'tenant_id' => auth()->user()->tenant_id,
                'is_active' => $request->boolean('is_active', true)
            ]
        ));

        return redirect()->route('salary-structures.index')->with('success', 'Salary Structure created.');
    }

    public function edit(SalaryStructure $salaryStructure)
    {
        return view('settings.salary-structures.edit', compact('salaryStructure'));
    }

    public function update(Request $request, SalaryStructure $salaryStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'components' => 'required|array',
            'components.*.name' => 'required|string',
            'components.*.type' => 'required|in:fixed,percentage',
            'components.*.value' => 'required|numeric',
            'is_active' => 'boolean',
        ]);

        $salaryStructure->update(array_merge(
            $request->except('components'),
            [
                'components' => $request->components,
                'is_active' => $request->boolean('is_active', true)
            ]
        ));

        return redirect()->route('salary-structures.index')->with('success', 'Salary Structure updated.');
    }

    public function destroy(SalaryStructure $salaryStructure)
    {
        $salaryStructure->delete();
        return redirect()->route('salary-structures.index')->with('success', 'Salary Structure deleted.');
    }
}
