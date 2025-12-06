<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Candidate;
use App\Models\Employee;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    public function index()
    {
        $interviews = Interview::with(['candidate', 'interviewer'])
            ->orderBy('scheduled_at')
            ->get();
        return view('recruitment.interviews.index', compact('interviews'));
    }

    public function create()
    {
        $candidates = Candidate::orderBy('name')->get();
        $interviewers = Employee::orderBy('first_name')->get();

        return view('recruitment.interviews.create', compact('candidates', 'interviewers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'interviewer_id' => 'required|exists:employees,id',
            'scheduled_at' => 'required|date|after:now',
            'type' => 'required|in:phone,video,in_person',
            'round' => 'required|string|max:50',
        ]);

        Interview::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id'), 'status' => 'scheduled']
        ));

        return redirect()->route('interviews.index')
            ->with('success', 'Interview scheduled successfully.');
    }

    public function edit(Interview $interview)
    {
        $candidates = Candidate::all();
        $interviewers = Employee::all();
        return view('recruitment.interviews.edit', compact('interview', 'candidates', 'interviewers'));
    }

    public function update(Request $request, Interview $interview)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'interviewer_id' => 'required|exists:employees,id',
            'scheduled_at' => 'required|date',
            'type' => 'required|in:phone,video,in_person',
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $interview->update($request->all());

        return redirect()->route('interviews.index')
            ->with('success', 'Interview updated successfully.');
    }

    public function destroy(Interview $interview)
    {
        $interview->delete();
        return redirect()->route('interviews.index')
            ->with('success', 'Interview deleted successfully.');
    }
}
