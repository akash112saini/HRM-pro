@extends('layouts.app')

@section('title', 'Add Candidate')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add New Candidate</h5>
                            <a href="{{ route('admin.candidates.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.candidates.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}" required>
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                        name="phone" value="{{ old('phone') }}">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="source" class="form-label">Source <span class="text-danger">*</span></label>
                                    <select class="form-select @error('source') is-invalid @enderror" id="source"
                                        name="source" required>
                                        <option value="">Select Source</option>
                                        <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website
                                        </option>
                                        <option value="linkedin" {{ old('source') == 'linkedin' ? 'selected' : '' }}>LinkedIn
                                        </option>
                                        <option value="job_portal" {{ old('source') == 'job_portal' ? 'selected' : '' }}>Job
                                            Portal</option>
                                        <option value="referral" {{ old('source') == 'referral' ? 'selected' : '' }}>Referral
                                        </option>
                                        <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="job_posting_id" class="form-label">Applied For <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('job_posting_id') is-invalid @enderror"
                                    id="job_posting_id" name="job_posting_id" required>
                                    <option value="">Select Job Posting</option>
                                    @foreach($jobPostings as $job)
                                        <option value="{{ $job->id }}" {{ old('job_posting_id') == $job->id ? 'selected' : '' }}>
                                            {{ $job->title }} ({{ $job->department->name ?? 'General' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('job_posting_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="resume" class="form-label">Resume (PDF, DOC, DOCX)</label>
                                <input type="file" class="form-control @error('resume') is-invalid @enderror" id="resume"
                                    name="resume" accept=".pdf,.doc,.docx">
                                @error('resume') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('candidates.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Add Candidate</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection