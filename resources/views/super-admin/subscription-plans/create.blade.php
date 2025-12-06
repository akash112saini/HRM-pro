@extends('super-admin.layout')

@section('title', 'Create Subscription Plan')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Create Subscription Plan</h1>
        <p class="text-muted">Define a new subscription plan with features and pricing</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('super-admin.subscription-plans.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- Basic Information --}}
                    <div class="col-12"><h5 class="border-bottom pb-2">Basic Information</h5></div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required placeholder="e.g., Enterprise">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}" required placeholder="e.g., enterprise">
                        <small class="text-muted">Lowercase, use hyphens for spaces</small>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this plan">{{ old('description') }}</textarea>
                    </div>

                    {{-- Pricing --}}
                    <div class="col-12"><h5 class="border-bottom pb-2 mt-3">Pricing</h5></div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Price (USD) <span class="text-danger">*</span></label>
                        <input type="number" name="price" step="0.01" class="form-control" value="{{ old('price', 0) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Billing Cycle <span class="text-danger">*</span></label>
                        <select name="billing_cycle" class="form-select" required>
                            <option value="monthly" {{ old('billing_cycle') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="yearly" {{ old('billing_cycle') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Display Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" placeholder="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>

                    {{-- Limits --}}
                    <div class="col-12"><h5 class="border-bottom pb-2 mt-3">Limits</h5></div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Max Employees</label>
                        <input type="number" name="max_employees" class="form-control" value="{{ old('max_employees') }}"
                            placeholder="Leave empty for unlimited">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Max Users <span class="text-danger">*</span></label>
                        <input type="number" name="max_users" class="form-control" value="{{ old('max_users', 5) }}" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label">Status <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Features & Modules --}}
                    <div class="col-12"><h5 class="border-bottom pb-2 mt-3">Features & Modules</h5></div>
                    
                    <div class="col-12">
                        <p class="text-muted mb-3">Select which features are included in this plan</p>
                        <div class="row">
                            @php
                            $availableFeatures = [
                                'Core HR' => ['Employee Management', 'Department Management', 'Designation Management'],
                                'Attendance' => ['Time Tracking', 'Clock In/Out', 'Attendance Reports', 'Shift Management'],
                                'Payroll' => ['Salary Processing', 'Payslip Generation', 'Tax Calculations', 'Allowances & Deductions'],
                                'Leave Management' => ['Leave Requests', 'Leave Approval', 'Leave Balance', 'Leave Reports'],
                                'Recruitment' => ['Job Postings', 'Applicant Tracking', 'Interview Scheduling'],
                                'Performance' => ['Performance Reviews', 'Goal Setting', 'KPI Tracking'],
                                'Reports' => ['Custom Reports', 'Export to Excel/PDF', 'Analytics Dashboard'],
                                'Other' => ['Multi-User Access', 'Email Notifications', 'API Access', 'Priority Support']
                            ];
                            @endphp

                            @foreach($availableFeatures as $category => $features)
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <strong>{{ $category }}</strong>
                                        </div>
                                        <div class="card-body">
                                            @foreach($features as $feature)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="features[]" 
                                                        value="{{ $feature }}" id="feature_{{ Str::slug($feature) }}"
                                                        {{ in_array($feature, old('features', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="feature_{{ Str::slug($feature) }}">
                                                        {{ $feature }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Create Plan
                    </button>
                    <a href="{{ route('super-admin.subscription-plans.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection