@extends('super-admin.layout')

@section('title', 'Manage Roles')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Super Admin Roles</h1>
            <p class="text-muted">Manage role-based permissions</p>
        </div>
        <a href="{{ route('super-admin.roles.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create Role
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Description</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td><strong>{{ $role->name }}</strong></td>
                                <td>{{ Str::limit($role->description, 50) }}</td>
                                <td><span class="badge bg-info">{{ $role->users_count }} users</span></td>
                                <td><span class="badge bg-secondary">{{ count($role->permissions ?? []) }} permissions</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                                        {{ $role->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"
                                                onclick="return confirm('Delete this role?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">No roles found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection