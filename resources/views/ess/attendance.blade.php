@extends('layouts.app')

@section('title', 'My Attendance')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">My Attendance</h1>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#correctionModal">
                Request Correction
            </button>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Present Days</h6>
                        <h3 class="mb-0 text-success">{{ $stats['present'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Absent Days</h6>
                        <h3 class="mb-0 text-danger">{{ $stats['absent'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Late Arrivals</h6>
                        <h3 class="mb-0 text-warning">{{ $stats['late'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Avg Work Hours</h6>
                        <h3 class="mb-0 text-primary">{{ number_format($stats['avg_hours'], 1) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ roleRoute('attendance') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            @foreach(range(date('Y') - 1, date('Y')) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Attendance List -->
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Punch In</th>
                                <th>Punch Out</th>
                                <th>Work Hours</th>
                                <th>Late?</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d M, Y (D)') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $attendance->status === 'present' ? 'success' : ($attendance->status === 'absent' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance->punch_in ? \Carbon\Carbon::parse($attendance->punch_in)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ $attendance->punch_out ? \Carbon\Carbon::parse($attendance->punch_out)->format('h:i A') : '-' }}
                                    </td>
                                    <td>{{ $attendance->total_work_hours ? number_format($attendance->total_work_hours, 1) . ' hrs' : '-' }}
                                    </td>
                                    <td>
                                        @if($attendance->is_late)
                                            <span class="badge bg-warning text-dark">Yes</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No attendance records found for this
                                        period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Correction Modal -->
    <div class="modal fade" id="correctionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Attendance Correction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ roleRoute('attendance.corrections.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_id" id="attendance_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Select Date</label>
                            <input type="date" id="correction_date" name="date" class="form-control" required
                                max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Select a date to see actual punch times</small>
                        </div>

                        <!-- Actual Punch Times Display -->
                        <div id="actualPunchTimes" class="alert alert-info d-none">
                            <h6 class="mb-2">Actual Punch Times for Selected Date:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Punch In:</strong> <span id="actual_punch_in">-</span>
                                </div>
                                <div class="col-6">
                                    <strong>Punch Out:</strong> <span id="actual_punch_out">-</span>
                                </div>
                            </div>
                        </div>

                        <div id="noAttendanceAlert" class="alert alert-warning d-none">
                            No attendance record found for this date.
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Requested Punch In</label>
                                <input type="time" name="requested_punch_in" id="requested_punch_in" class="form-control"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Requested Punch Out</label>
                                <input type="time" name="requested_punch_out" id="requested_punch_out" class="form-control"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason for Correction</label>
                            <textarea name="reason" class="form-control" rows="3" required
                                placeholder="Explain why correction is needed"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Correction History Section -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Correction Request History</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Requested Times</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Submitted On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($corrections ?? [] as $correction)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($correction->attendance->date ?? 'N/A')->format('d M, Y') }}</td>
                                <td>
                                    <small>
                                        <strong>In:</strong>
                                        {{ $correction->requested_punch_in ? \Carbon\Carbon::parse($correction->requested_punch_in)->format('h:i A') : '-' }}
                                        <br>
                                        <strong>Out:</strong>
                                        {{ $correction->requested_punch_out ? \Carbon\Carbon::parse($correction->requested_punch_out)->format('h:i A') : '-' }}
                                    </small>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $correction->status === 'approved' ? 'success' : ($correction->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($correction->status) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($correction->reason, 50) }}</td>
                                <td>{{ $correction->created_at->format('d M, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No correction requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('correction_date').addEventListener('change', function  () {         const date = this.value;         const employeeId = {{ auth()->user()->employee->id ?? 0 }};
             if (!date || !employeeId) return;
             // Fetch actual punch times
            fetch(`{{ roleRoute('attendance.get-punch-times') }}?date=${date}&employee_id=${employeeId}`)
             .then(response => response.json())             .then(data => {                 if (data.found) {                     // Show actual punch times                     document.getElementById('actualPunchTimes').classList.remove('d-none');                     document.getElementById('noAttendanceAlert').classList.add('d-none');                     document.getElementById('actual_punch_in').textContent = data.punch_in || '-';                     document.getElementById('actual_punch_out').textContent = data.punch_out || '-';                     document.getElementById('attendance_id').value = data.attendance_id;
                         // Pre-fill the requested times with actual times                     if (data.punch_in) {                         document.getElementById('requested_punch_in').value = data.punch_in;                     }                     if (data.punch_out) {                         document.getElementById('requested_punch_out').value = data.punch_out;                     }                 } else {                     // No attendance found                     document.getElementById('actualPunchTimes').classList.add('d-none');                     document.getElementById('noAttendanceAlert').classList.remove('d-none');                     document.getElementById('attendance_id').value = '';                 }             })             .catch(error => {                 console.error('Error fetching punch times:', error);             });     });
    </script>
@endpush