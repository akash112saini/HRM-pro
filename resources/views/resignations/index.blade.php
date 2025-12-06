@extends('layouts.app')

@section('title', 'Resignations')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Resignations</h1>
            <a href="{{ roleRoute('resignations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Submit Resignation
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Resignation Date</th>
                                <th>Last Working Day</th>
                                <th>Manager Status</th>
                                <th>Admin Status</th>
                                <th>Final Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resignations as $resignation)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $resignation->employee->full_name }}</div>
                                        <small class="text-muted">{{ $resignation->employee->employee_code }}</small>
                                    </td>
                                    <td>{{ $resignation->employee->department->name ?? '-' }}</td>
                                    <td>{{ $resignation->resignation_date->format('d M, Y') }}</td>
                                    <td>{{ $resignation->last_working_day->format('d M, Y') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $resignation->manager_status === 'approved' ? 'success' : ($resignation->manager_status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($resignation->manager_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $resignation->admin_status === 'approved' ? 'success' : ($resignation->admin_status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($resignation->admin_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $resignation->final_status === 'approved' ? 'success' : ($resignation->final_status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($resignation->final_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ roleRoute('resignations.show', $resignation) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No resignations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $resignations->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection