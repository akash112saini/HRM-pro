@extends('layouts.app')

@section('title', 'Schedule Interview')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Schedule Interview</h5>
                            <a href="{{ route('interviews.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('interviews.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="candidate_id" class="form-label">Candidate <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('candidate_id') is-invalid @enderror" id="candidate_id"
                                    name="candidate_id" required>
                                    <option value="">Select Candidate</option>
                                    @foreach($candidates as $candidate)
                                        <option value="{{ $candidate->id }}" {{ old('candidate_id') == $candidate->id ? 'selected' : '' }}>
                                            {{ $candidate->first_name }} {{ $candidate->last_name }} ({{ $candidate->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('candidate_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="interviewer_id" class="form-label">Interviewer <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('interviewer_id') is-invalid @enderror"
                                    id="interviewer_id" name="interviewer_id" required>
                                    <option value="">Select Interviewer</option>
                                    @foreach($interviewers as $employee)
                                        <option value="{{ $employee->id }}" {{ old('interviewer_id') == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('interviewer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="scheduled_at" class="form-label">Date & Time <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local"
                                        class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at"
                                        name="scheduled_at" value="{{ old('scheduled_at') }}" required>
                                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label">Interview Type <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                        required>
                                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video Call
                                        </option>
                                        <option value="phone" {{ old('type') == 'phone' ? 'selected' : '' }}>Phone Call
                                        </option>
                                        <option value="in_person" {{ old('type') == 'in_person' ? 'selected' : '' }}>In Person
                                        </option>
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="round" class="form-label">Interview Round <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('round') is-invalid @enderror" id="round"
                                    name="round" value="{{ old('round', 'Technical Round 1') }}" required>
                                @error('round') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-3">
                                <a href="{{ route('interviews.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Schedule Interview</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection