@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Roles & Permissions</h1>
                        <p class="text-muted mb-0">Manage roles and their access capabilities</p>
                    </div>
                    <a href="{{ roleRoute('roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Role
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Users</th>
                                <th>Permissions</th>
                                <th>Description</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ $role->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $role->slug }}</small>
                                    </td>
                                    <td>
                                        @if($role->is_system_role)
                                            <span class="badge bg-primary">System</span>
                                        @else
                                            <span class="badge bg-secondary">Custom</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($role->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-warning">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $role->users()->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ count($role->permissions ?? []) }} permissions
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ Str::limit($role->description, 50) }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ roleRoute('roles.edit', $role) }}"
                                                class="btn btn-sm btn-outline-primary" title="Edit Role">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            @if(!$role->is_system_role && $role->users()->count() === 0)
                                                <form action="{{ roleRoute('roles.destroy', $role) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No roles found. <a href="{{ roleRoute('roles.create') }}">Create your first role</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">About Roles</h5>
                    <ul class="mb-0 small">
                        <li><strong>System Roles:</strong> Default roles that cannot be deleted (Company Admin, Manager,
                            Employee)</li>
                        <li><strong>Custom Roles:</strong> Roles created by admins for specific needs (e.g., HR Manager,
                            Recruiter)</li>
                        <li><strong>Permissions:</strong> Each role can have specific permissions for different modules</li>
                        <li><strong>Deletion:</strong> Custom roles can only be deleted if they have no users assigned</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection