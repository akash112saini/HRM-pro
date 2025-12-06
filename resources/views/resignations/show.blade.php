@extends('layouts.app')

@section('title', 'Resignation Details')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Resignation Details</h1>
            <a href="{{ roleRoute('resignations.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="row g-4">
            <!-- Employee Information -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">Employee Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Employee Name</label>
                                <div class="fw-bold">{{ $resignation->employee->full_name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Employee Code</label>
                                <div class="fw-bold">{{ $resignation->employee->employee_code }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Department</label>
                                <div>{{ $resignation->employee->department->name ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Designation</label>
                                <div>{{ $resignation->employee->designation->name ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="mb-0">Resignation Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Resignation Date</label>
                                <div class="fw-bold">{{ $resignation->resignation_date->format('d M, Y') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">Last Working Day</label>
                                <div class="fw-bold">{{ $resignation->last_working_day->format('d M, Y') }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="text-muted small">Reason</label>
                                <div class="border rounded p-3 bg-light">{{ $resignation->reason }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Status -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Approval Status</h6>

                        <!-- Manager Approval -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold">Manager Approval</span>
                                <span
                                    class="badge bg-{{ $resignation->manager_status === 'approved' ? 'success' : ($resignation->manager_status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($resignation->manager_status) }}
                                </span>
                            </div>
                            @if($resignation->manager_approved_by)
                                <div class="small text-muted">
                                    By: {{ $resignation->managerApprover->name }}<br>
                                    At: {{ $resignation->manager_approved_at->format('d M, Y h:i A') }}
                                </div>
                                @if($resignation->manager_remarks)
                                    <div class="small mt-2 p-2 bg-light rounded">
                                        <strong>Remarks:</strong> {{ $resignation->manager_remarks }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Admin Approval -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold">Admin Approval</span>
                                <span
                                    class="badge bg-{{ $resignation->admin_status === 'approved' ? 'success' : ($resignation->admin_status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($resignation->admin_status) }}
                                </span>
                            </div>
                            @if($resignation->admin_approved_by)
                                <div class="small text-muted">
                                    By: {{ $resignation->adminApprover->name }}<br>
                                    At: {{ $resignation->admin_approved_at->format('d M, Y h:i A') }}
                                </div>
                                @if($resignation->admin_remarks)
                                    <div class="small mt-2 p-2 bg-light rounded">
                                        <strong>Remarks:</strong> {{ $resignation->admin_remarks }}
                                    </div>
                                @endif
                            @endif
                        </div>

                        <!-- Final Status -->
                        <div class="pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Final Status</span>
                                <span
                                    class="badge bg-{{ $resignation->final_status === 'approved' ? 'success' : ($resignation->final_status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($resignation->final_status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Manager Approval Actions -->
                @if(auth()->user()->role === 'manager' && $resignation->manager_status === 'pending' && $resignation->employee->manager_id === auth()->user()->employee->id)
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3">Manager Approval</h6>
                            <form action="{{ roleRoute('resignations.manager.approve', $resignation) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea name="remarks" class="form-control" rows="3"
                                        placeholder="Optional remarks..."></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">
                                        <i class="bi bi-check-lg me-2"></i>Approve
                                    </button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">
                                        <i class="bi bi-x-lg me-2"></i>Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Admin Approval Actions -->
                @if(in_array(auth()->user()->role, ['super_admin', 'company_admin']) && $resignation->admin_status === 'pending')
                    @if($resignation->employee->manager_id && $resignation->manager_status !== 'approved')
                        <div class="alert alert-warning small">
                            <i class="bi bi-info-circle me-2"></i>
                            Waiting for manager approval before admin can approve.
                        </div>
                    @else
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h6 class="mb-3">Admin Approval</h6>
                                <form action="{{ roleRoute('resignations.admin.approve', $resignation) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="3"
                                            placeholder="Optional remarks..."></textarea>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="action" value="approve" class="btn btn-success">
                                            <i class="bi bi-check-lg me-2"></i>Approve
                                        </button>
                                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                                            <i class="bi bi-x-lg me-2"></i>Reject
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection