@extends('layouts.app')

@section('title', 'Department Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">{{ $department->name }}</h1>
            <div>
                <a href="{{ roleRoute('departments.edit', $department) }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ roleRoute('departments.index') }}" class="btn btn-outline-secondary ms-2">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Department Info -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted small">Code</label>
                            <div class="fw-bold">{{ $department->code ?? 'N/A' }}</div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Status</label>
                            <div>
                                @if($department->is_active)
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted small">Department Head</label>
                            @if($department->head)
                                <div class="d-flex align-items-center mt-1">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                        style="width: 32px; height: 32px;">
                                        {{ substr($department->head->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $department->head->first_name }}
                                            {{ $department->head->last_name }}
                                        </div>
                                        <small class="text-muted">{{ $department->head->designation->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted fst-italic">Not Assigned</div>
                            @endif
                        </div>
                        <div class="mb-0">
                            <label class="text-muted small">Description</label>
                            <p class="mb-0">{{ $department->description ?? 'No description available.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees & Designations -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Employees ({{ $department->employees->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($department->employees as $employee)
                                        <tr>
                                            <td>{{ $employee->email }}</td>
                                            <td>
                                                @if($employee->is_active)
                                                    <span class="badge bg-success-subtle text-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">No employees in this department.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection