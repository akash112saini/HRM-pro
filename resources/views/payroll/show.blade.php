@extends('layouts.app')

<h3>{{ config('app.name') }}</h3>
<p class="text-muted">Payslip for {{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}
</p>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <table class="table table-borderless">
            <tr>
                <th class="w-25">Employee Name</th>
                <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
            </tr>
            <tr>
                <th>Employee ID</th>
                <td>{{ $payroll->employee->employee_code }}</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $payroll->employee->department->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Designation</th>
                <td>{{ $payroll->employee->designation->name ?? '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="table table-borderless">
            <tr>
                <th class="w-25">Payslip No</th>
                <td>#{{ str_pad($payroll->id, 6, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <th>Working Days</th>
                <td>{{ $payroll->total_working_days }}</td>
            </tr>
            <tr>
                <th>Present Days</th>
                <td>{{ $payroll->present_days }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($payroll->status === 'paid')
                        <span class="badge bg-success">PAID</span>
                    @else
                        <span class="badge bg-warning text-dark">PENDING</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card bg-light border-0">
            <div class="card-header bg-success text-white">Earnings</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end">{{ number_format($payroll->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td>HRA</td>
                            <td class="text-end">{{ number_format($payroll->hra, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Allowances</td>
                            <td class="text-end">{{ number_format($payroll->allowances, 2) }}</td>
                        </tr>
                        @if($payroll->overtime_pay > 0)
                            <tr>
                                <td>Overtime Pay</td>
                                <td class="text-end">{{ number_format($payroll->overtime_pay, 2) }}</td>
                            </tr>
                        @endif
                        @if($payroll->bonus > 0)
                            <tr>
                                <td>Bonus</td>
                                <td class="text-end">{{ number_format($payroll->bonus, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="fw-bold">
                            <td>Total Earnings</td>
                            <td class="text-end">{{ number_format($payroll->total_earnings, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card bg-light border-0">
            <div class="card-header bg-danger text-white">Deductions</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td>Provident Fund</td>
                            <td class="text-end">{{ number_format($payroll->pf, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Professional Tax</td>
                            <td class="text-end">{{ number_format($payroll->professional_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td>TDS (Tax)</td>
                            <td class="text-end">{{ number_format($payroll->tds, 2) }}</td>
                        </tr>
                        @if($payroll->lop_deduction > 0)
                            <tr>
                                <td>Loss of Pay</td>
                                <td class="text-end">{{ number_format($payroll->lop_deduction, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="fw-bold">
                            <td>Total Deductions</td>
                            <td class="text-end">{{ number_format($payroll->total_deductions, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-primary d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Net Salary</h4>
            <h3 class="mb-0 fw-bold">{{ number_format($payroll->net_salary, 2) }}</h3>
        </div>
    </div>
</div>

@if($payroll->status === 'pending')
    <div class="text-end mt-4">
        <form action="{{ roleRoute('payroll.mark-paid', $payroll) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-lg">Mark as Paid</button>
        </form>
    </div>
@endif
</div>
</div>
</div>
@endsection