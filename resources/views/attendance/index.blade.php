@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Attendance Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#punchModal">
                <i class="bi bi-clock-history me-2"></i>Manual Punch
            </button>
        </div>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Present Today</p>
                                <h4 class="mb-0">142</h4>
                            </div>
                            <i class="bi bi-check-circle-fill text-success fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Absent Today</p>
                                <h4 class="mb-0">8</h4>
                            </div>
                            <i class="bi bi-x-circle-fill text-danger fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">Late Arrivals</p>
                                <h4 class="mb-0">12</h4>
                            </div>
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-1">On Leave</p>
                                <h4 class="mb-0">6</h4>
                            </div>
                            <i class="bi bi-calendar-x-fill text-info fs-2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select">
                                <option value="">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="leave">On Leave</option>
                                <option value="holiday">Holiday</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <th>Work Hours</th>
                                <th>Status</th>
                                <th>Late/Early</th>
                                <th>Overtime</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                {{ strtoupper(substr($attendance->employee->first_name, 0, 1) . substr($attendance->employee->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $attendance->employee->full_name }}</div>
                                                <small class="text-muted">{{ $attendance->employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $attendance->date->format('d M Y') }}</td>
                                    <td>
                                        @if($attendance->punch_in)
                                            <span class="badge bg-success-subtle text-success">
                                                {{ $attendance->punch_in->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->punch_out)
                                            <span class="badge bg-danger-subtle text-danger">
                                                {{ $attendance->punch_out->format('h:i A') }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($attendance->total_work_hours, 2) }}h</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'present' => 'success',
                                                'absent' => 'danger',
                                                'leave' => 'info',
                                                'holiday' => 'secondary',
                                                'week_off' => 'warning'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$attendance->status] ?? 'secondary' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attendance->is_late)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-clock"></i> Late {{ $attendance->late_minutes }}m
                                            </span>
                                        @endif
                                        @if($attendance->is_early_departure)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-door-open"></i> Early {{ $attendance->early_departure_minutes }}m
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($attendance->overtime_hours > 0)
                                            <span class="badge bg-success-subtle text-success">
                                                +{{ number_format($attendance->overtime_hours, 2) }}h
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#correctionModal"><i
                                                            class="bi bi-pencil me-2"></i>Request Correction</a></li>
                                                <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                        Details</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No attendance records found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <p class="text-muted mb-1">Absent Today</p>
        <h4 class="mb-0">8</h4>
    </div>
    <i class="bi bi-x-circle-fill text-danger fs-2"></i>
    </div>
    </div>
    </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">Late Arrivals</p>
                        <h4 class="mb-0">12</h4>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill text-warning fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted mb-1">On Leave</p>
                        <h4 class="mb-0">6</h4>
                    </div>
                    <i class="bi bi-calendar-x-fill text-info fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">All Employees</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">On Leave</option>
                            <option value="holiday">Holiday</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Punch In</th>
                            <th>Punch Out</th>
                            <th>Work Hours</th>
                            <th>Status</th>
                            <th>Late/Early</th>
                            <th>Overtime</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                            style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($attendance->employee->first_name, 0, 1) . substr($attendance->employee->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $attendance->employee->full_name }}</div>
                                            <small class="text-muted">{{ $attendance->employee->employee_code }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $attendance->date->format('d M Y') }}</td>
                                <td>
                                    @if($attendance->punch_in)
                                        <span class="badge bg-success-subtle text-success">
                                            {{ $attendance->punch_in->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->punch_out)
                                        <span class="badge bg-danger-subtle text-danger">
                                            {{ $attendance->punch_out->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($attendance->total_work_hours, 2) }}h</strong></td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'present' => 'success',
                                            'absent' => 'danger',
                                            'leave' => 'info',
                                            'holiday' => 'secondary',
                                            'week_off' => 'warning'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$attendance->status] ?? 'secondary' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($attendance->is_late)
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock"></i> Late {{ $attendance->late_minutes }}m
                                        </span>
                                    @endif
                                    @if($attendance->is_early_departure)
                                        <span class="badge bg-warning">
                                            <i class="bi bi-door-open"></i> Early {{ $attendance->early_departure_minutes }}m
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->overtime_hours > 0)
                                        <span class="badge bg-success-subtle text-success">
                                            +{{ number_format($attendance->overtime_hours, 2) }}h
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                            data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#correctionModal"><i class="bi bi-pencil me-2"></i>Request
                                                    Correction</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bi bi-eye me-2"></i>View
                                                    Details</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mt-2">No attendance records found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
    </div>

    <!-- Manual Punch Modal -->
    <div class="modal fade" id="punchModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manual Punch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.attendance.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Employee</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Punch Type</label>
                            <select name="punch_type" class="form-select" required>
                                <option value="in">Punch In</option>
                                <option value="out">Punch Out</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date & Time</label>
                            <input type="datetime-local" name="punch_time" class="form-control"
                                value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Punch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection