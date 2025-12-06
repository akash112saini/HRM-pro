@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Edit Role: {{ $role->name }}</h1>
                <a href="{{ roleRoute('roles.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ roleRoute('roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name', $role->name) }}" 
                                {{ $role->is_system_role ? 'readonly' : 'required' }} autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($role->is_system_role)
                                <small class="text-muted">System role names cannot be changed</small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                    {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active (Inactive roles cannot be assigned to users)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            @error('permissions')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror

                            <div class="accordion" id="permissionsAccordion">
                                @foreach($availablePermissions as $module => $permissions)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading{{ ucfirst($module) }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ ucfirst($module) }}"
                                                aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                                <strong>{{ ucwords(str_replace('_', ' ', $module)) }}</strong>
                                                <small class="ms-2 text-muted">({{ count($permissions) }} permissions)</small>
                                            </button>
                                        </h2>
                                        <div id="collapse{{ ucfirst($module) }}" 
                                            class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                            data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    @foreach($permissions as $permission => $label)
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="permissions[]" value="{{ $permission }}" 
                                                                    id="perm_{{ $permission }}"
                                                                    {{ in_array($permission, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="perm_{{ $permission }}">
                                                                    {{ $label }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ roleRoute('roles.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Role Information</h5>
                    <ul class="list-unstyled small">
                        <li><strong>Slug:</strong> {{ $role->slug }}</li>
                        <li><strong>Type:</strong> 
                            @if($role->is_system_role)
                                <span class="badge bg-primary">System Role</span>
                            @else
                                <span class="badge bg-secondary">Custom Role</span>
                            @endif
                        </li>
                        <li><strong>Users:</strong> {{ $role->users()->count() }}</li>
                        <li><strong>Created:</strong> {{ $role->created_at->format('M d, Y') }}</li>
                    </ul>
                </div>
            </div>

            @if($role->is_system_role)
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title text-info">System Role</h5>
                    <p class="small mb-0">
                        This is a system role. The name cannot be changed, but you can modify permissions and description.
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
