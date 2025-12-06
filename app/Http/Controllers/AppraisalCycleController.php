<?php

namespace App\Http\Controllers;

use App\Models\AppraisalCycle;
use Illuminate\Http\Request;

class AppraisalCycleController extends Controller
{
    public function index()
    {
        $cycles = AppraisalCycle::orderByDesc('start_date')->get();
        return view('performance.cycles.index', compact('cycles'));
    }

    public function create()
    {
        return view('performance.cycles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        AppraisalCycle::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect()->route('appraisal-cycles.index')
            ->with('success', 'Appraisal Cycle created successfully.');
    }

    public function edit(AppraisalCycle $appraisalCycle)
    {
        return view('performance.cycles.edit', compact('appraisalCycle'));
    }

    public function update(Request $request, AppraisalCycle $appraisalCycle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $appraisalCycle->update($request->all());

        return redirect()->route('appraisal-cycles.index')
            ->with('success', 'Appraisal Cycle updated successfully.');
    }

    public function destroy(AppraisalCycle $appraisalCycle)
    {
        $appraisalCycle->delete();
        return redirect()->route('appraisal-cycles.index')
            ->with('success', 'Appraisal Cycle deleted successfully.');
    }
}
