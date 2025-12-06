@extends('super-admin.layout')

@section('title', 'Tenant Details')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">{{ $tenant->name }}</h1>
                <p class="text-muted">{{ $tenant->slug }}.{{ config('app.domain') }}</p>
            </div>
            <div>
                <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Company Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Company Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Company Name</label>
                            <p class="mb-0"><strong>{{ $tenant->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Slug</label>
                            <p class="mb-0"><strong>{{ $tenant->slug }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Contact Email</label>
                            <p class="mb-0">{{ $tenant->contact_email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Contact Phone</label>
                            <p class="mb-0">{{ $tenant->contact_phone ?? 'N/A' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Address</label>
                            <p class="mb-0">
                                {{ $tenant->address ?? 'N/A' }}<br>
                                {{ $tenant->city }}, {{ $tenant->state }} {{ $tenant->postal_code }}<br>
                                {{ $tenant->country }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3 class="text-primary">{{ $tenant->users->count() }}</h3>
                            <p class="text-muted mb-0">Users</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-success">{{ $tenant->employees->count() }}</h3>
                            <p class="text-muted mb-0">Employees</p>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-info">{{ $tenant->departments->count() }}</h3>
                            <p class="text-muted mb-0">Departments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription & Status -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Subscription</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Plan</label>
                        <p><span class="badge bg-primary">{{ ucfirst($tenant->subscription_plan) }}</span></p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Expires</label>
                        <p>{{ $tenant->subscription_expires_at ? $tenant->subscription_expires_at->format('M d, Y') : 'Never' }}
                        </p>
                    </div>
                    <div>
                        <label class="text-muted small">Status</label>
                        <p>
                            @if($tenant->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('super-admin.tenants.toggle-status', $tenant) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-{{ $tenant->is_active ? 'danger' : 'success' }} w-100">
                                {{ $tenant->is_active ? 'Deactivate' : 'Activate' }} Tenant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Recent Activity</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Description</th>
                            <th>User</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivity as $activity)
                            <tr>
                                <td><span class="badge bg-info">{{ $activity->action }}</span></td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->user->name ?? 'System' }}</td>
                                <td>{{ $activity->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">No activity yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection