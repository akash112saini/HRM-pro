@extends('layouts.app')

@section('title', 'Leave Types')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Leave Types</h1>
            <a href="{{ roleRoute('leave-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add Leave Type
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Annual Quota</th>
                                <th>Accrual Type</th>
                                <th>Paid</th>
                                <th>Approval</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveTypes as $leaveType)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="rounded-circle me-2"
                                                style="width: 10px; height: 10px; background-color: {{ $leaveType->color }};"></span>
                                            <strong>{{ $leaveType->name }}</strong>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $leaveType->code }}</span></td>
                                    <td>{{ $leaveType->annual_quota }} days</td>
                                    <td>{{ ucfirst($leaveType->accrual_type) }}</td>
                                    <td>
                                        @if($leaveType->is_paid)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($leaveType->requires_approval)
                                            <span class="badge bg-warning text-dark">Required</span>
                                        @else
                                            <span class="badge bg-info text-dark">Auto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ roleRoute('leave-types.edit', $leaveType) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ roleRoute('leave-types.destroy', $leaveType) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No leave types found</p>
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