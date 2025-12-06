@extends('layouts.app')

@section('title', 'Payroll Report')

@section('content')
    <div class="container-fluid">
        <h1 class="page-title mb-4">Payroll Report</h1>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Month</label>
                            <input type="month" name="month" class="form-control"
                                value="{{ request('month', now()->format('Y-m')) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Department</label>
                            <select name="department_id" class="form-select">
                                <option value="">All Departments</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Payroll Summary</h5>
                <p class="text-muted">Select month and click "Generate Report" to view payroll data.</p>
            </div>
        </div>
    </div>
@endsection