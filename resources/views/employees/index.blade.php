@extends('layouts.app')

@section('title', 'Employees')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">Employees</h1>
            <a href="{{ roleRoute('employees.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Add New Employee
            </a>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ roleRoute('employees.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0"
                                    placeholder="Search by name, email or code..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="employment_status" class="form-select">
                                <option value="">All Status</option>
                                <option value="probation" {{ request('employment_status') == 'probation' ? 'selected' : '' }}>
                                    Probation</option>
                                <option value="confirmed" {{ request('employment_status') == 'confirmed' ? 'selected' : '' }}>
                                    Confirmed</option>
                                <option value="resigned" {{ request('employment_status') == 'resigned' ? 'selected' : '' }}>
                                    Resigned</option>
                                <option value="terminated" {{ request('employment_status') == 'terminated' ? 'selected' : '' }}>Terminated
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Employee Cards Grid -->
        <div class="row g-4">
            @forelse($employees as $employee)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 60px; height: 60px; font-size: 1.5rem;">
                                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $employee->full_name }}</h5>
                                    <p class="text-muted small mb-1">{{ $employee->designation->name ?? 'N/A' }}</p>
                                    <span
                                        class="badge {{ $employee->employment_status == 'confirmed' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($employee->employment_status) }}
                                    </span>
                                </div>
                            </div>

                            <hr>

                            <div class="small">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="bi bi-hash"></i> Code:</span>
                                    <strong>{{ $employee->employee_code }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="bi bi-envelope"></i> Email:</span>
                                    <strong>{{ $employee->email }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="bi bi-telephone"></i> Phone:</span>
                                    <strong>{{ $employee->phone }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted"><i class="bi bi-diagram-3"></i> Department:</span>
                                    <strong>{{ $employee->department->name ?? 'N/A' }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted"><i class="bi bi-calendar-event"></i> Joined:</span>
                                    <strong>{{ $employee->joining_date->format('d M Y') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between gap-2">
                                <!-- Password Management Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                        id="passwordMenu{{$employee->id}}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-key"></i> Manage
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="passwordMenu{{$employee->id}}">
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                onclick="resetPassword({{ $employee->user_id }}, '{{ $employee->full_name }}');">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Reset Password
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                onclick="generateLoginLink({{ $employee->user_id }}, '{{ $employee->full_name }}');">
                                                <i class="bi bi-link-45deg me-2"></i>Generate Login Link
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)" 
                                                onclick="confirmImpersonate({{ $employee->user_id }}, '{{ $employee->full_name }}');">
                                                <i class="bi bi-person-badge me-2"></i>Impersonate
                                            </a>
                                        </li>

                                    </ul>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ roleRoute('employees.edit', $employee->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ roleRoute('employees.destroy', $employee->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="text-muted mt-3">No employees found</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $employees->links() }}
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