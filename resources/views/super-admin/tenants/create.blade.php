@extends('super-admin.layout')

@section('title', 'Create New Client')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Create New Client</h1>
        <p class="text-muted">Add a new tenant organization with admin user</p>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('super-admin.tenants.store') }}" method="POST">
                @csrf

                <h5 class="mb-3">Company Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug (Subdomain) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                value="{{ old('slug') }}" required>
                            <span class="input-group-text">.{{ config('app.domain', 'hrm-pro.test') }}</span>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Only letters, numbers, and hyphens. Example: acme-corp</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" name="contact_email"
                            class="form-control @error('contact_email') is-invalid @enderror"
                            value="{{ old('contact_email') }}" required>
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}">
                    </div>
                </div>

                <h5 class="mb-3">Address</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Street Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}">
                    </div>
                </div>

                <h5 class="mb-3"> Subscription</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Subscription Plan <span class="text-danger">*</span></label>
                        <select name="subscription_plan"
                            class="form-select @error('subscription_plan') is-invalid @enderror" required>
                            <option value="trial" {{ old('subscription_plan') == 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="basic" {{ old('subscription_plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('subscription_plan') == 'premium' ? 'selected' : '' }}>Premium
                            </option>
                            <option value="enterprise" {{ old('subscription_plan') == 'enterprise' ? 'selected' : '' }}>
                                Enterprise</option>
                        </select>
                        @error('subscription_plan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subscription Expires At</label>
                        <input type="date" name="subscription_expires_at" class="form-control"
                            value="{{ old('subscription_expires_at') }}">
                        <small class="text-muted">Leave empty for no expiration</small>
                    </div>
                </div>

                <h5 class="mb-3">Admin User</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                        <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror"
                            value="{{ old('admin_name') }}" required>
                        @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                        <input type="email" name="admin_email"
                            class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}"
                            required>
                        @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Admin Password <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password"
                            class="form-control @error('admin_password') is-invalid @enderror" required>
                        @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="admin_password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Create Client
                    </button>
                    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection