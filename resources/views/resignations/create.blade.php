@extends('layouts.app')

@section('title', 'Submit Resignation')

@section('content')
    <div class="container-fluid">
        <h1 class="page-title mb-4">Submit Resignation</h1>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form action="{{ roleRoute('resignations.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">Resignation Date <span class="text-danger">*</span></label>
                                <input type="date" name="resignation_date"
                                    class="form-control @error('resignation_date') is-invalid @enderror"
                                    value="{{ old('resignation_date', date('Y-m-d')) }}" required>
                                @error('resignation_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Last Working Day <span class="text-danger">*</span></label>
                                <input type="date" name="last_working_day"
                                    class="form-control @error('last_working_day') is-invalid @enderror"
                                    value="{{ old('last_working_day') }}" required>
                                @error('last_working_day')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Proposed last day of work</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Reason for Resignation <span class="text-danger">*</span></label>
                                <textarea name="reason" rows="5" class="form-control @error('reason') is-invalid @enderror"
                                    required
                                    placeholder="Please explain your reason for resignation...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 10 characters</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Your resignation will be forwarded for approval.
                                @if(auth()->user()->role === 'manager')
                                    It will go directly to admin for approval.
                                @else
                                    It will first be reviewed by your manager, then forwarded to admin for final approval.
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send me-2"></i>Submit Resignation
                                </button>
                                <a href="{{ roleRoute('resignations.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection