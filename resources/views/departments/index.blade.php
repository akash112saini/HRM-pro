@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Departments</h1>
        <a href="{{ roleRoute('departments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Department
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Head</th>
                            <th>Employees</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $department)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $department->name }}</div>
                                <small class="text-muted">{{ Str::limit($department->description, 50) }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $department->code ?? 'N/A' }}</span></td>
                            <td>
                                @if($department->head)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ substr($department->head->first_name, 0, 1) . substr($department->head->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div>{{ $department->head->first_name }} {{ $department->head->last_name }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info rounded-pill">
                                    {{ $department->employees_count }} Employees
                                </span>
                            </td>
                            <td>
                                @if($department->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ roleRoute('departments.show', $department) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ roleRoute('departments.edit', $department) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ roleRoute('departments.destroy', $department) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-3"></i>
                                    No departments found. Start by creating one.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
