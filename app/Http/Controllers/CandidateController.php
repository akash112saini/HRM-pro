<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    /**
     * Display candidate listing (Kanban view).
     */
    public function index(Request $request)
    {
        $query = Candidate::with('jobPosting');

        // Filter by job posting
        if ($request->filled('job_posting_id')) {
            $query->where('job_posting_id', $request->job_posting_id);
        }

        $candidates = $query->orderBy('applied_at', 'desc')->get();

        // Group by stage for Kanban
        $stages = [
            'applied' => $candidates->where('current_stage', 'applied'),
            'screening' => $candidates->where('current_stage', 'screening'),
            'interview' => $candidates->where('current_stage', 'interview'),
            'offer' => $candidates->where('current_stage', 'offer'),
            'hired' => $candidates->where('current_stage', 'hired'),
            'rejected' => $candidates->where('current_stage', 'rejected'),
        ];

        $jobPostings = JobPosting::active()->get();

        return view('candidates.index', compact('stages', 'jobPostings'));
    }

    /**
     * Show candidate creation form.
     */
    public function create()
    {
        $jobPostings = JobPosting::active()->get();
        return view('candidates.create', compact('jobPostings'));
    }

    /**
     * Store new candidate.
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_posting_id' => 'required|exists:job_postings,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'source' => 'required|in:website,referral,linkedin,job_portal,other',
        ]);

        $data = $request->all();
        $data['tenant_id'] = app('tenant.id');
        $data['applied_at'] = now();

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'tenant');
            $data['resume_path'] = $path;
        }

        Candidate::create($data);

        return redirect()->route('candidates.index')
            ->with('success', 'Candidate added successfully');
    }

    /**
     * Display candidate details.
     */
    public function show($id)
    {
        $candidate = Candidate::with(['jobPosting', 'interviews.interviewer'])
            ->findOrFail($id);

        return view('candidates.show', compact('candidate'));
    }

    /**
     * Move candidate to different stage.
     */
    public function moveStage(Request $request, $id)
    {
        $request->validate([
            'stage' => 'required|in:applied,screening,interview,offer,hired,rejected',
        ]);

        $candidate = Candidate::findOrFail($id);
        $candidate->update(['current_stage' => $request->stage]);

        return redirect()->back()
            ->with('success', 'Candidate moved to ' . $request->stage);
    }

    /**
     * Update candidate notes.
     */
    public function updateNotes(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $candidate = Candidate::findOrFail($id);
        $candidate->update(['notes' => $request->notes]);

        return redirect()->back()
            ->with('success', 'Notes updated successfully');
    }
}
