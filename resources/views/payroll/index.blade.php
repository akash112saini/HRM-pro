@extends('layouts.app')

@section('title', 'Payroll Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Payroll Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generatePayrollModal">
                <i class="bi bi-plus-circle me-2"></i>Generate Payroll
            </button>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ roleRoute('payroll.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            <option value="">All Years</option>
                            @foreach(range(date('Y') - 2, date('Y')) as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-secondary">
                                <i class="bi bi-filter me-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payroll Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payroll Records</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Period</th>
                                <th>Gross Salary</th>
                                <th>Deductions</th>
                                <th>Net Salary</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payrolls as $payroll)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                                                style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                {{ substr($payroll->employee->full_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $payroll->employee->full_name }}</div>
                                                <small class="text-muted">{{ $payroll->employee->employee_code }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ date('F Y', mktime(0, 0, 0, $payroll->month, 1, $payroll->year)) }}</td>
                                    <td>₹{{ number_format($payroll->gross_salary, 2) }}</td>
                                    <td>₹{{ number_format($payroll->total_deductions, 2) }}</td>
                                    <td>
                                        <strong>₹{{ number_format($payroll->net_salary, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($payroll->status === 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ roleRoute('payroll.show', $payroll) }}"
                                                class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ roleRoute('payroll.download', $payroll) }}"
                                                class="btn btn-sm btn-outline-secondary" title="Download PDF">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if($payroll->status !== 'paid')
                                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                                    data-bs-target="#markPaidModal{{ $payroll->id }}" title="Mark as Paid">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Mark as Paid Modal -->
                                <div class="modal fade" id="markPaidModal{{ $payroll->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Mark as Paid</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ roleRoute('payroll.mark-paid', $payroll) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <p>Mark payroll for <strong>{{ $payroll->employee->full_name }}</strong> as
                                                        paid?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Payment Method</label>
                                                        <select name="payment_method" class="form-select" required>
                                                            <option value="bank_transfer">Bank Transfer</option>
                                                            <option value="cash">Cash</option>
                                                            <option value="cheque">Cheque</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Transaction Reference</label>
                                                        <input type="text" name="transaction_reference" class="form-control"
                                                            placeholder="Enter reference number">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Confirm Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No payroll records found for this period</p>
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
            {{ $payrolls->links() }}
        </div>
    </div>

    <!-- Generate Payroll Modal -->
    <div class="modal fade" id="generatePayrollModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Payroll</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ roleRoute('payroll.generate') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
                            <select class="form-select @error('month') is-invalid @enderror" id="month" name="month"
                                required>
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('month', date('n')) == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('month') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <select class="form-select @error('year') is-invalid @enderror" id="year" name="year" required>
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>
                            @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employees</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="employee_scope" id="all_employees"
                                    value="all" checked>
                                <label class="form-check-label" for="all_employees">
                                    All Active Employees
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="employee_scope" id="specific_employee"
                                    value="specific">
                                <label class="form-check-label" for="specific_employee">
                                    Specific Employee
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="employee_select_wrapper">
                            <label for="employee_id" class="form-label">Select Employee</label>
                            <select class="form-select" id="employee_id" name="employee_id">
                                <option value="">Select Employee</option>
                                @foreach(\App\Models\Employee::active()->get() as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->first_name }} {{ $emp->last_name }}
                                        ({{ $emp->employee_code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> This will calculate salaries based on attendance,
                            leaves, and salary structure. Existing payroll records for this period will be skipped.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Generate Payroll</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const scopeRadios = document.getElementsByName('employee_scope');
                const employeeWrapper = document.getElementById('employee_select_wrapper');

                scopeRadios.forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.value === 'specific') {
                            employeeWrapper.classList.remove('d-none');
                        } else {
                            employeeWrapper.classList.add('d-none');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection