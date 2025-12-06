@extends('super-admin.layout')

@section('title', 'Password Management')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Password Management</h1>
        <p class="text-muted">View and manage all user passwords across platform</p>
    </div>

    <!-- Password Display Alert (One-time only) -->
    @if(session('new_password'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-check-circle me-2"></i>Password Reset Successful</h5>
            <p class="mb-2">Password for <strong>{{ session('password_user') }}</strong> has been reset successfully.</p>
            <div class="bg-white p-3 rounded border mb-2">
                <strong>New Password:</strong> <code class="fs-5 text-danger">{{ session('new_password') }}</code>
                <button class="btn btn-sm btn-outline-primary ms-2" onclick="copyPassword('{{ session('new_password') }}')">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            <p class="mb-0 small text-warning"><i class="bi bi-exclamation-triangle"></i> <strong>Important:</strong> This
                password will only be shown once. Please copy and share it securely with the user.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Login Token Display Alert (One-time only) -->
    @if(session('login_url'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="bi bi-link-45deg me-2"></i>Secure Login Token Generated</h5>
            <p class="mb-2">One-time login link for <strong>{{ session('token_user') }}</strong> (expires
                {{ session('expires_at') }})
            </p>
            <div class="bg-white p-3 rounded border mb-2">
                <input type="text" class="form-control" id="loginUrl" value="{{ session('login_url') }}" readonly>
                <button class="btn btn-sm btn-outline-primary mt-2" onclick="copyLoginUrl()">
                    <i class="bi bi-clipboard"></i> Copy Link
                </button>
            </div>
            <p class="mb-0 small text-info"><i class="bi bi-info-circle"></i> This link can only be used once and expires in 24
                hours.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Impersonation Banner -->
    @if(session()->has('impersonating_from'))
        <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
            <div>
                <i class="bi bi-person-badge me-2"></i>
                <strong>Currently Impersonating: {{ auth()->user()->name }}</strong>
            </div>
            <form action="{{ route('super-admin.stop-impersonating') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-x-circle me-1"></i>Stop Impersonating
                </button>
            </form>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="tenant_id" class="form-select">
                        <option value="">All Tenants</option>
                        @foreach(\App\Models\Tenant::all() as $tenant)
                            <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="company_admin" {{ request('role') == 'company_admin' ? 'selected' : '' }}>Company Admin
                        </option>
                        <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Tenant</th>
                            <th>Role</th>
                            <th>Password Changes</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong><br>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </td>
                                <td>{{ $user->tenant->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</span>
                                </td>
                                <td>{{ $user->password_audit_logs_count }} times</td>
                                <td>{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#resetPasswordModal{{ $user->id }}">
                                            <i class="bi bi-key"></i> Reset
                                        </button>
                                        <a href="{{ route('super-admin.passwords.audit', $user) }}"
                                            class="btn btn-outline-info">
                                            <i class="bi bi-clock-history"></i> Audit
                                        </a>
                                    </div>
                                    <div class="btn-group btn-group-sm mt-1" role="group">
                                        <form action="{{ route('super-admin.passwords.generate-token', $user) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Generate login link">
                                                <i class="bi bi-link-45deg"></i> Login Link
                                            </button>
                                        </form>
                                        <form action="{{ route('super-admin.passwords.impersonate', $user) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning" title="Impersonate user"
                                                onclick="return confirm('Impersonate {{ $user->name }}?')">
                                                <i class="bi bi-person-badge"></i> Impersonate
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Reset Password Modal -->
                                    <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('super-admin.passwords.reset', $user) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reset Password for {{ $user->name }}</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">New Password</label>
                                                            <input type="password" name="new_password" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Confirm Password</label>
                                                            <input type="password" name="new_password_confirmation"
                                                                class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Notes (Optional)</label>
                                                            <textarea name="notes" class="form-control" rows="2"
                                                                placeholder="Reason for reset..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Reset Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No users found</td>
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

    <script>
        function copyPassword(password) {
            navigator.clipboard.writeText(password).then(function () {
                alert('Password copied to clipboard!');
            });
        }

        function copyLoginUrl() {
            const loginUrl = document.getElementById('loginUrl');
            loginUrl.select();
            navigator.clipboard.writeText(loginUrl.value).then(function () {
                alert('Login URL copied to clipboard!');
            });
        }
    </script>
@endsection