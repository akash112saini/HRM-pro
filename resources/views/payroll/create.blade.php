@extends('layouts.app')

@section('title', 'Generate Payroll')

@section('content')
    <div class="container-fluid">

        <div class="mb-3">
            <label for="month" class="form-label">Month <span class="text-danger">*</span></label>
            <select class="form-select @error('month') is-invalid @enderror" id="month" name="month" required>
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
                <input class="form-check-input" type="radio" name="employee_scope" id="all_employees" value="all" checked>
                <label class="form-check-label" for="all_employees">
                    All Active Employees
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="employee_scope" id="specific_employee" value="specific">
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

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Generate Payroll</button>
        </div>
        </form>
    </div>
    </div>
    </div>
    </div>
    </div>

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
@endsection