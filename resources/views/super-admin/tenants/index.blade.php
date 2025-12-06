@extends('super-admin.layout')

@section('title', 'Clients Management')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">Clients (Tenants)</h1>
            <p class="text-muted">Manage your client organizations</p>
        </div>
        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add New Client
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('super-admin.tenants.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, slug, email..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="plan" class="form-select">
                        <option value="">All Plans</option>
                        <option value="trial" {{ request('plan') == 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="basic" {{ request('plan') == 'basic' ? 'selected' : '' }}>Basic</option>
                        <option value="premium" {{ request('plan') == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ request('plan') == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Company</th>
                            <th>Contact</th>
                            <th>Plan</th>
                            <th>Expires</th>
                            <th>Users</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
                            <tr>
                                <td>
                                    <strong>{{ $tenant->name }}</strong><br>
                                    <small
                                        class="text-muted">{{ $tenant->slug }}.{{ config('app.domain', 'hrm-pro.test') }}</small>
                                </td>
                                <td>
                                    {{ $tenant->contact_email }}<br>
                                    <small class="text-muted">{{ $tenant->contact_phone ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge 
                                                @if($tenant->subscription_plan == 'enterprise') bg-danger
                                                @elseif($tenant->subscription_plan == 'premium') bg-primary
                                                @elseif($tenant->subscription_plan == 'basic') bg-info
                                                @else bg-secondary
                                                @endif">
                                        {{ ucfirst($tenant->subscription_plan) }}
                                    </span>
                                </td>
                                <td>
                                    @if($tenant->subscription_expires_at)
                                        {{ $tenant->subscription_expires_at->format('M d, Y') }}<br>
                                        <small
                                            class="{{ $tenant->subscription_expires_at->isPast() ? 'text-danger' : ($tenant->subscription_expires_at->diffInDays() < 30 ? 'text-warning' : 'text-success') }}">
                                            {{ $tenant->subscription_expires_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted">No expiry</span>
                                    @endif
                                </td>
                                <td>{{ $tenant->users_count }}</td>
                                <td>{{ $tenant->employees_count }}</td>
                                <td>
                                    @if($tenant->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.tenants.show', $tenant) }}"
                                            class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('super-admin.tenants.edit', $tenant) }}"
                                            class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-outline-{{ $tenant->is_active ? 'danger' : 'success' }}"
                                                title="{{ $tenant->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="bi bi-{{ $tenant->is_active ? 'x-circle' : 'check-circle' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('super-admin.tenants.force-delete', $tenant) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to PERMANENTLY delete this tenant? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Permanently">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No clients found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tenants->hasPages())
            <div class="card-footer">
                {{ $tenants->links() }}
            </div>
        @endif
    </div>
@endsection