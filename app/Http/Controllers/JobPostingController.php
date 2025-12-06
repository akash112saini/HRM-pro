<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Models\Department;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function index()
    {
        $jobPostings = JobPosting::with('department')
            ->orderByDesc('posted_at')
            ->get();
        return view('recruitment.jobs.index', compact('jobPostings'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('recruitment.jobs.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'location' => 'required|string|max:255',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'status' => 'required|in:draft,published,closed',
            'posted_at' => 'nullable|date',
            'closing_date' => 'nullable|date',
        ]);

        JobPosting::create(array_merge(
            $request->all(),
            [
                'tenant_id' => app('tenant.id'),
                'posted_at' => $request->posted_at ?? now()
            ]
        ));

        return redirect()->route('jobs.index')
            ->with('success', 'Job posting created successfully.');
    }

    public function edit($id)
    {
        $job = JobPosting::findOrFail($id);
        $departments = Department::orderBy('name')->get();
        return view('recruitment.jobs.edit', compact('job', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $job = JobPosting::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'location' => 'required|string|max:255',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'status' => 'required|in:draft,published,closed',
            'posted_at' => 'nullable|date',
            'closing_date' => 'nullable|date',
        ]);

        $job->update($request->all());

        return redirect()->route('jobs.index')
            ->with('success', 'Job posting updated successfully.');
    }

    public function destroy($id)
    {
        $job = JobPosting::findOrFail($id);
        $job->delete();
        return redirect()->route('jobs.index')
            ->with('success', 'Job posting deleted successfully.');
    }
}
