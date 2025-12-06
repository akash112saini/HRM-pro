@extends('layouts.app')

@section('title', 'Attendance Corrections')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Attendance Corrections</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestCorrectionModal">
                <i class="bi bi-plus-lg"></i> Request Correction
            </button>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Employee</th>
                                <th>Original Punch</th>
                                <th>Requested Punch</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($corrections as $correction)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($correction->date)->format('M d, Y') }}</td>
                                                <td>
                                                    <div>{{ $correction->employee->first_name }} {{ $correction->employee->last_name }}
                                                    </div>
                                                    <div class="small text-muted">{{ $correction->employee->employee_code }}</div>
                                                </td>
                                                <td>
                                                    <div>In:
                                                        {{ $correction->original_punch_in ? \Carbon\Carbon::parse($correction->original_punch_in)->format('h:i A') : '-' }}
                                                    </div>
                                                    <div>Out:
                                                        {{ $correction->original_punch_out ? \Carbon\Carbon::parse($correction->original_punch_out)->format('h:i A') : '-' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-primary">In:
                                                        {{ $correction->requested_punch_in ? \Carbon\Carbon::parse($correction->requested_punch_in)->format('h:i A') : '-' }}
                                                    </div>
                                                    <div class="text-primary">Out:
                                                        {{ $correction->requested_punch_out ? \Carbon\Carbon::parse($correction->requested_punch_out)->format('h:i A') : '-' }}
                                                    </div>
                                                </td>
                                                    @endif
                                </td>
                                </tr>
                            @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No correction requests found.</td>
                    </tr>
                @endforelse
                </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- Request Modal -->
    <div class="modal fade" id="requestCorrectionModal" tabindex="-1">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="punch_in" class="form-label">Correct Punch In</label>
                                <input type="time" class="form-control" id="punch_in" name="punch_in">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="punch_out" class="form-label">Correct Punch Out</label>
                                <input type="time" class="form-control" id="punch_out" name="punch_out">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection