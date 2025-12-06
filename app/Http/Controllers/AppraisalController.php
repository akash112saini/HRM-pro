<?php

namespace App\Http\Controllers;

use App\Models\Appraisal;
use App\Models\AppraisalCycle;
use App\Models\Employee;
use Illuminate\Http\Request;

class AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $appraisals = Appraisal::with(['employee', 'cycle', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();

        return view('performance.appraisals.index', compact('appraisals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $cycles = AppraisalCycle::active()->get();

        return view('performance.appraisals.create', compact('employees', 'cycles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'appraisal_cycle_id' => 'required|exists:appraisal_cycles,id',
            'reviewer_id' => 'required|exists:employees,id',
            'status' => 'required|in:pending,self_review,manager_review,completed',
        ]);

        Appraisal::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect()->route('appraisals.index')
            ->with('success', 'Appraisal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Appraisal $appraisal)
    {
        return view('performance.appraisals.show', compact('appraisal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appraisal $appraisal)
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $cycles = AppraisalCycle::active()->get();

        return view('performance.appraisals.edit', compact('appraisal', 'employees', 'cycles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appraisal $appraisal)
    {
        $request->validate([
            'status' => 'required|in:pending,self_review,manager_review,completed',
            'final_rating' => 'nullable|numeric|min:1|max:5',
        ]);

        $appraisal->update($request->all());

        return redirect()->route('appraisals.index')
            ->with('success', 'Appraisal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appraisal $appraisal)
    {
        $appraisal->delete();

        return redirect()->route('appraisals.index')
            ->with('success', 'Appraisal deleted successfully.');
    }
}
