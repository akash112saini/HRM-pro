@extends('layouts.app')

@section('title', 'Apply for Leave')

@section('content')
    <div class="container-fluid">
        name="leave_type_id" required>
        <option value="">Select Leave Type</option>
        @foreach(\App\Models\LeaveType::all() as $type)
            <option value="{{ $type->id }}" {{ old('leave_type_id') == $type->id ? 'selected' : '' }}>
                {{ $type->name }} ({{ $type->days_per_year }} days/year)
            </option>
        @endforeach
        </select>
        @error('leave_type_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date"
                name="start_date" value="{{ old('start_date') }}" required>
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6 mb-3">
            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date"
                value="{{ old('end_date') }}" required>
            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="mb-3">
        <label for="reason" class="form-label">Reason for Leave <span class="text-danger">*</span></label>
        <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3"
            required>{{ old('reason') }}</textarea>
        @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
    </div>
    </div>
@endsection