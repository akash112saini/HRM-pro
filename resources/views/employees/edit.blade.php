@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title mb-0">Edit Employee</h1>
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.employees.update', $employee) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <h6 class="text-uppercase text-muted mb-3 small fw-bold">Personal Information</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                id="first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                                required>
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name"
                                name="last_name" value="{{ old('last_name', $employee->last_name) }}" required>
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email', $employee->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                name="phone" value="{{ old('phone', $employee->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                id="date_of_birth" name="date_of_birth"
                                value="{{ old('date_of_birth', $employee->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Male
                                </option>
                                <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>
                                    Female</option>
                                <option value="other" {{ old('gender', $employee->gender) == 'other' ? 'selected' : '' }}>
                                    Other</option>
                            </select>
                            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-uppercase text-muted mb-3 small fw-bold">Employment Details</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="employee_code" class="form-label">Employee Code <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('employee_code') is-invalid @enderror"
                                id="employee_code" name="employee_code"
                                value="{{ old('employee_code', $employee->employee_code) }}" required>
                            @error('employee_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="joining_date" class="form-label">Joining Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('joining_date') is-invalid @enderror"
                                id="joining_date" name="joining_date"
                                value="{{ old('joining_date', $employee->joining_date?->format('Y-m-d')) }}" required>
                            @error('joining_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="department_id" class="form-label">Department <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                                name="department_id" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="designation_id" class="form-label">Designation <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('designation_id') is-invalid @enderror" id="designation_id"
                                name="designation_id" required>
                                <option value="">Select Designation</option>
                                @foreach($designations as $desig)
                                    <option value="{{ $desig->id }}" {{ old('designation_id', $employee->designation_id) == $desig->id ? 'selected' : '' }}>
                                        {{ $desig->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('designation_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                            <select class="form-select @error('shift_id') is-invalid @enderror" id="shift_id"
                                name="shift_id" required>
                                <option value="">Select Shift</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id', $employee->shift_id) == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shift_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="manager_id" class="form-label">Reporting Manager</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id"
                                name="manager_id">
                                <option value="">Select Manager</option>
                                @foreach($managers as $emp)
                                    <option value="{{ $emp->id }}" {{ old('manager_id', $employee->manager_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->first_name }} {{ $emp->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employment_status" class="form-label">Employment Status</label>
                            <select class="form-select @error('employment_status') is-invalid @enderror"
                                id="employment_status" name="employment_status">
                                <option value="probation" {{ old('employment_status', $employee->employment_status) == 'probation' ? 'selected' : '' }}>
                                    Probation</option>
                                <option value="confirmed" {{ old('employment_status', $employee->employment_status) == 'confirmed' ? 'selected' : '' }}>
                                    Confirmed</option>
                                <option value="contract" {{ old('employment_status', $employee->employment_status) == 'contract' ? 'selected' : '' }}>
                                    Contract</option>
                            </select>
                            @error('employment_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="salary" class="form-label">Basic Salary</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('salary') is-invalid @enderror" id="salary"
                                    name="salary" value="{{ old('salary', $employee->salary) }}" step="0.01">
                            </div>
                            @error('salary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="{{ route('admin.employees.index') }}" class="btn btn-light me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Update Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection