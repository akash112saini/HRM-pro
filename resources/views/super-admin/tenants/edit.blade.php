@extends('super-admin.layout')

@section('title', 'Edit Tenant')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Edit Tenant: {{ $tenant->name }}</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST">
                @csrf
                @method('PUT')

                <h5 class="mb-3">Company Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $tenant->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug', $tenant->slug) }}" required>
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email"
                            class="form-control @error('contact_email') is-invalid @enderror"
                            value="{{ old('contact_email', $tenant->contact_email) }}" required>
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control"
                            value="{{ old('contact_phone', $tenant->contact_phone) }}">
                    </div>
                </div>

                <h5 class="mb-3">Address</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Street Address</label>
                        <textarea name="address" class="form-control"
                            rows="2">{{ old('address', $tenant->address) }}</textarea>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $tenant->city) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $tenant->state) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Country</label>
                        <input type="text" name="country" class="form-control"
                            value="{{ old('country', $tenant->country) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control"
                            value="{{ old('postal_code', $tenant->postal_code) }}">
                    </div>
                </div>

                <h5 class="mb-3">Subscription</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Subscription Plan</label>
                        <select name="subscription_plan" class="form-select" required>
                            <option value="trial" {{ old('subscription_plan', $tenant->subscription_plan) == 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="basic" {{ old('subscription_plan', $tenant->subscription_plan) == 'basic' ? 'selected' : '' }}>Basic</option>
                            <option value="premium" {{ old('subscription_plan', $tenant->subscription_plan) == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Subscription Expires At</label>
                        <input type="date" name="subscription_expires_at" class="form-control"
                            value="{{ old('subscription_expires_at', $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <h5 class="mb-3">Branding</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Primary Color</label>
                        <input type="color" name="primary_color" class="form-control"
                            value="{{ old('primary_color', $tenant->primary_color) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Secondary Color</label>
                        <input type="color" name="secondary_color" class="form-control"
                            value="{{ old('secondary_color', $tenant->secondary_color) }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Update Tenant
                    </button>
                    <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection