@extends('layouts.app')

@section('title', 'Create Appraisal')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Start New Appraisal</h5>
                            <a href="{{ route('appraisals.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('appraisals.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="appraisal_cycle_id" class="form-label">Appraisal Cycle <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('appraisal_cycle_id') is-invalid @enderror"
                                    id="appraisal_cycle_id" name="appraisal_cycle_id" required>
                                    <option value="">Select Cycle</option>
                                    @foreach($cycles as $cycle)
                                        <option value="{{ $cycle->id }}" {{ old('appraisal_cycle_id') == $cycle->id ? 'selected' : '' }}>
                                            {{ $cycle->name }} ({{ $cycle->start_date->format('M Y') }} -
                                            {{ $cycle->end_date->format('M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('appraisal_cycle_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="employee_id" class="form-label">Employee <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id"
                                        name="employee_id" required>
                                        <option value="">Select Employee</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employee_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="reviewer_id" class="form-label">Reviewer <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('reviewer_id') is-invalid @enderror" id="reviewer_id"
                                        name="reviewer_id" required>
                                        <option value="">Select Reviewer</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ old('reviewer_id') == $employee->id ? 'selected' : '' }}>
                                                {{ $employee->first_name }} {{ $employee->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('reviewer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Initial Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending">Pending</option>
                                    <option value="self_review">Self Review</option>
                                    <option value="manager_review">Manager Review</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('appraisals.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Appraisal</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection