@extends('layouts.app')

@section('title', 'Add Leave Type')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Add Leave Type</h1>
            <a href="{{ roleRoute('leave-types.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ roleRoute('leave-types.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="annual_quota" class="form-label">Annual Quota <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('annual_quota') is-invalid @enderror"
                                id="annual_quota" name="annual_quota" value="{{ old('annual_quota') }}" min="0" required>
                            @error('annual_quota')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="accrual_type" class="form-label">Accrual Type <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('accrual_type') is-invalid @enderror" id="accrual_type"
                                name="accrual_type" required>
                                <option value="yearly" {{ old('accrual_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="monthly" {{ old('accrual_type') == 'monthly' ? 'selected' : '' }}>Monthly
                                </option>
                                <option value="none" {{ old('accrual_type') == 'none' ? 'selected' : '' }}>None</option>
                            </select>
                            @error('accrual_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="max_carry_forward" class="form-label">Max Carry Forward <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('max_carry_forward') is-invalid @enderror"
                                id="max_carry_forward" name="max_carry_forward" value="{{ old('max_carry_forward', 0) }}"
                                min="0" required>
                            @error('max_carry_forward')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror"
                                id="color" name="color" value="{{ old('color', '#3B82F6') }}" required>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_paid" name="is_paid" value="1" {{ old('is_paid', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_paid">Is Paid Leave</label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="requires_approval"
                                    name="requires_approval" value="1" {{ old('requires_approval', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_approval">Requires Approval</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Leave Type
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection