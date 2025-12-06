@extends('layouts.app')

@section('title', 'Password Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title mb-0">Password Management</h1>
                <p class="text-muted">Manage passwords and login access for employees</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                                value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="company_admin" {{ request('role') == 'company_admin' ? 'selected' : '' }}>Company
                                Admin</option>
                            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    @if(request()->hasAny(['search', 'role']))
                        <div class="col-md-2">
                            <a href="{{ route('admin.password.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                    </td>
                                    <td>
                                        @if($user->employee)
                                            <span
                                                class="badge {{ $user->employee->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($user->employee->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="resetPassword({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="generateLoginLink({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bi bi-link-45deg me-1"></i> Link
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="confirmImpersonate({{ $user->id }}, '{{ $user->name }}')">
                                                <i class="bi bi-person-badge me-1"></i> Impersonate
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No users found matching your criteria</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('employees.partials.password-modals')
@endsection

@push('scripts')
    <script>
        function resetPassword(userId, userName) {
            if (confirm(`Reset password for ${userName}?`)) {
                fetch(`/admin/password-management/reset/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('newPasswordDisplay').textContent = data.password;
                            document.getElementById('resetEmployeeName').textContent = userName;
                            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
                        } else {
                            alert('Error resetting password');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error resetting password');
                    });
            }
        }

        function generateLoginLink(userId, userName) {
            fetch(`/admin/password-management/login-link/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('loginLinkDisplay').value = data.link;
                        document.getElementById('linkExpiresAt').textContent = data.expires_at;
                        document.getElementById('linkEmployeeName').textContent = userName;
                        new bootstrap.Modal(document.getElementById('loginLinkModal')).show();
                    } else {
                        alert('Error generating login link');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error generating login link');
                });
        }

        function copyLoginLink() {
            const input = document.getElementById('loginLinkDisplay');
            input.select();
            document.execCommand('copy');

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        }

        function confirmImpersonate(userId, userName) {
            document.getElementById('impersonateEmployeeName').textContent = userName;
            document.getElementById('impersonateUserId').value = userId;
            new bootstrap.Modal(document.getElementById('impersonateModal')).show();
        }

        function performImpersonate() {
            const userId = document.getElementById('impersonateUserId').value;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/password-management/impersonate/${userId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush