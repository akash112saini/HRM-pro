@extends('layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-muted">Here's what's happening today.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ roleRoute('attendance') }}" class="btn btn-outline-primary">
                    <i class="bi bi-clock me-2"></i>Attendance
                </a>
                <a href="{{ roleRoute('leaves') }}" class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-2"></i>Apply Leave
                </a>
            </div>
        </div>

        @if(isset($error))
            <div class="alert alert-danger">{{ $error }}</div>
        @else

            <div class="row g-4">
                <!-- Today's Status -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Today's Attendance</h5>
                            @if($todayAttendance)
                                <div class="text-center py-3">
                                    <div class="display-4 text-success mb-2">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <h4>Present</h4>
                                    <p class="text-muted">
                                        Punched in at {{ \Carbon\Carbon::parse($todayAttendance->punch_in)->format('h:i A') }}
                                        @if($todayAttendance->is_late)
                                            <span class="badge bg-warning text-dark ms-2">Late</span>
                                        @endif
                                    </p>
                                    @if($todayAttendance->punch_out)
                                        <p class="text-muted">Punched out at
                                            {{ \Carbon\Carbon::parse($todayAttendance->punch_out)->format('h:i A') }}</p>
                                    @else
                                        <form action="{{ roleRoute('attendance.punch-out') }}" method="POST">
                                            @csrf
                                            <button class="btn btn-danger">Punch Out</button>
                                        </form>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <div class="display-4 text-secondary mb-2">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <h4>Not Punched In</h4>
                                    <p class="text-muted">You haven't marked your attendance yet.</p>
                                    <form action="{{ roleRoute('attendance.punch-in') }}" method="POST">
                                        @csrf
                                        <button class="btn btn-success btn-lg px-4">Punch In</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-md-8">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                            <i class="bi bi-calendar-check fs-3 text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-1">Leave Balance</h6>
                                            <h4 class="mb-0">View Details</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                                            <i class="bi bi-cash-stack fs-3 text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-1">Last Salary</h6>
                                            <h4 class="mb-0">
                                                {{ $recentPayslips->first() ? number_format($recentPayslips->first()->net_salary) : 'N/A' }}
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card border-0 shadow-sm mt-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="mb-0">Recent Leave Requests</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Dates</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentLeaves as $leave)
                                            <tr>
                                                <td>{{ $leave->leaveType->name }}</td>
                                                <td>{{ $leave->start_date->format('d M') }} - {{ $leave->end_date->format('d M') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $leave->status === 'approved' ? 'success' : ($leave->status === 'rejected' ? 'danger' : 'warning') }}">
                                                        {{ ucfirst($leave->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No recent leave requests</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection