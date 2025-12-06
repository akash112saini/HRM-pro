@extends('layouts.app')

@section('title', 'Leave Request Details')

@section('content')
    <div class="container-fluid">
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Employee</h6>
            <p class="fw-bold">{{ $leave->employee->first_name }} {{ $leave->employee->last_name }}</p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Status</h6>
            @if($leave->status === 'approved')
                <span class="badge bg-success">Approved</span>
            @elseif($leave->status === 'rejected')
                <span class="badge bg-danger">Rejected</span>
            @elseif($leave->status === 'cancelled')
                <span class="badge bg-secondary">Cancelled</span>
            @else
                <span class="badge bg-warning text-dark">Pending</span>
            @endif
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Leave Type</h6>
            <p>{{ $leave->leaveType->name }}</p>
        </div>
        <div class="col-md-6">
            <h6 class="text-muted text-uppercase small">Duration</h6>
            <p>
                {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} -
                {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                <span class="text-muted">({{ $leave->days }} days)</span>
            </p>
        </div>
    </div>

    <div class="mb-4">
        <h6 class="text-muted text-uppercase small">Reason</h6>
        <p class="bg-light p-3 rounded">{{ $leave->reason }}</p>
    </div>

    @if($leave->rejection_reason)
        <div class="mb-4">
            <h6 class="text-danger text-uppercase small">Rejection Reason</h6>
            <p class="bg-danger-subtle text-danger p-3 rounded">{{ $leave->rejection_reason }}</p>
    </div>
    </div>
    </div>
    </div>
@endsection