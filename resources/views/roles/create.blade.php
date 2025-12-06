@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Create New Role</h1>
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
                    <form action="{{ roleRoute('roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">e.g., "HR Manager", "Recruiter", "Accountant"</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                                                    {{ in_array($permission, old('permissions', [])) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Tips</h5>
                    <ul class="small">
                        <li>Role names should be descriptive (e.g., "HR Manager", "Team Lead")</li>
                        <li>Select only the permissions this role needs</li>
                        <li>Custom roles can be edited or deleted later</li>
                        <li>Users assigned to this role will inherit all selected permissions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
