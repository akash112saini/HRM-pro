@extends('layouts.app')

@section('title', 'My Payslips')

@section('content')
    <div class="container-fluid">
        <h1 class="page-title mb-4">My Payslips</h1>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Month</th>
                                <th>Year</th>
                                <th>Basic Salary</th>
                                <th>Allowances</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }}</td>
                                    <td>{{ $payroll->year }}</td>
                                    <td>{{ number_format($payroll->basic_salary, 2) }}</td>
                                    <td>
                                        @php
                                            $allowances = $payroll->gross_salary - $payroll->basic_salary;
                                        @endphp
                                        {{ number_format($allowances, 2) }}
                                    </td>
                                    <td>{{ number_format($payroll->deductions['Tax'] ?? 0, 2) }}</td>
                                    <td class="fw-bold text-success">{{ number_format($payroll->net_salary, 2) }}</td>
                                    <td>
                                        <span class="badge bg-success">Paid</span>
                                    </td>
                                    <td>
                                        <a href="{{ roleRoute('payslips.download', $payroll->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-download me-1"></i>Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                                        No payslips available yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $payrolls->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection