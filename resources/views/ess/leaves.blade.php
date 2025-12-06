@extends('layouts.app')

@section('title', 'My Leaves')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">My Leaves</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
                <i class="bi bi-plus-lg me-2"></i>Apply Leave
            </button>
        </div>

        <!-- Leave Balances -->
        <div class="row g-4 mb-4">
            @foreach($leaveTypes as $type)
                <div class="col-md-3">
                    <div class="card border-start border-4 border-{{ $type->color ? '' : 'primary' }}"
                        style="border-color: {{ $type->color }} !important;">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">{{ $type->name }}</h6>
                            <h3 class="mb-0">
                                {{ $balances[$type->id] ?? 0 }}
                                <small class="text-muted fs-6">/ {{ $type->annual_quota }}</small>
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Leave History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0">
                <h5 class="mb-0">Leave History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Days</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Applied On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaveRequests as $leave)
                                <tr>
                                    <td>
                                        <span class="badge" style="background-color: {{ $leave->leaveType->color }};">
                                            {{ $leave->leaveType->name }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->start_date->format('d M, Y') }}</td>
                                    <td>{{ $leave->end_date->format('d M, Y') }}</td>
                                    <td>{{ $leave->total_days }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 200px;"
                                            title="{{ $leave->reason }}">
                                            {{ $leave->reason }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger',
                                                'cancelled' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$leave->status] ?? 'secondary' }}">
                                            {{ ucfirst($leave->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $leave->created_at->format('d M, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No leave requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div class="modal fade" id="applyLeaveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Apply for Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ roleRoute('leaves.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Leave Type</label>
                            <select name="leave_type_id" class="form-select" required>
                                <option value="">Select Type</option>
                                @foreach($leaveTypes as $type)
                                    <option value="{{ $type->id }}">
                                        {{ $type->name }} ({{ $balances[$type->id] ?? 0 }} available)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required
                                    min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" required min="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="3" required
                                placeholder="Reason for leave..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection