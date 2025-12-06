@extends('layouts.app')

@section('title', 'Recruitment - Candidates')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Recruitment Pipeline</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCandidateModal">
                <i class="bi bi-person-plus me-2"></i>Add Candidate
            </button>
        </div>

        <!-- Job Posting Filter -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Job Posting</label>
                        <select class="form-select" onchange="window.location.href='?job_posting_id='+this.value">
                            <option value="">All Job Postings</option>
                            @foreach($jobPostings as $job)
                                <option value="{{ $job->id }}" {{ request('job_posting_id') == $job->id ? 'selected' : '' }}>
                                    {{ $job->title }} ({{ $job->candidates_count ?? 0 }} candidates)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 text-end">
                        <span class="badge bg-primary me-2">{{ $stages['applied']->count() }} Applied</span>
                        <span class="badge bg-info me-2">{{ $stages['screening']->count() }} Screening</span>
                        <span class="badge bg-warning me-2">{{ $stages['interview']->count() }} Interview</span>
                        <span class="badge bg-purple me-2">{{ $stages['offer']->count() }} Offer</span>
                        <span class="badge bg-success me-2">{{ $stages['hired']->count() }} Hired</span>
                        <span class="badge bg-danger">{{ $stages['rejected']->count() }} Rejected</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="card">
            <div class="card-body p-3">
                <div class="row g-3">
                    <!-- Applied Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-inbox text-primary"></i> Applied</span>
                                <span class="badge bg-primary rounded-pill">{{ $stages['applied']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['applied'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Screening Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-search text-info"></i> Screening</span>
                                <span class="badge bg-info rounded-pill">{{ $stages['screening']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['screening'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Interview Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-camera-video text-warning"></i> Interview</span>
                                <span class="badge bg-warning rounded-pill">{{ $stages['interview']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['interview'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Offer Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-file-text" style="color: #8B5CF6;"></i> Offer</span>
                                <span class="badge rounded-pill"
                                    style="background-color: #8B5CF6;">{{ $stages['offer']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['offer'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Hired Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-check-circle text-success"></i> Hired</span>
                                <span class="badge bg-success rounded-pill">{{ $stages['hired']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['hired'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Rejected Column -->
                    <div class="col-md-2">
                        <div class="bg-light rounded p-3">
                            <h6 class="mb-3 d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-x-circle text-danger"></i> Rejected</span>
                                <span class="badge bg-danger rounded-pill">{{ $stages['rejected']->count() }}</span>
                            </h6>
                            <div class="kanban-column" style="min-height: 500px; max-height: 70vh; overflow-y: auto;">
                                @foreach($stages['rejected'] as $candidate)
                                    @include('candidates.partials.candidate-card', ['candidate' => $candidate])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Candidate Modal -->
    <div class="modal fade" id="addCandidateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Candidate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.candidates.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Job Posting *</label>
                                <select name="job_posting_id" class="form-select" required>
                                    <option value="">Select Job</option>
                                    @foreach($jobPostings as $job)
                                        <option value="{{ $job->id }}">{{ $job->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Source *</label>
                                <select name="source" class="form-select" required>
                                    <option value="website">Company Website</option>
                                    <option value="referral">Employee Referral</option>
                                    <option value="linkedin">LinkedIn</option>
                                    <option value="job_portal">Job Portal</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Resume (PDF/DOC)</label>
                                <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Candidate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .kanban-column::-webkit-scrollbar {
                width: 6px;
            }

            .kanban-column::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .kanban-column::-webkit-scrollbar-thumb {
                background: #888;
                border-radius: 10px;
            }
        </style>
    @endpush
@endsection