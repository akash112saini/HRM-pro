@extends('layouts.app')

@section('title', 'Shifts')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Work Shifts</h1>
            <a href="{{ roleRoute('shifts.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Shift
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Shift Name</th>
                                <th>Timing</th>
                                <th>Working Days</th>
                                <th>Grace Period</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shifts as $shift)
                                <tr>
                                    <td class="fw-bold">{{ $shift->name }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($shift->start_time)->format('h:i A') }} -
                                        {{ \Carbon\Carbon::parse($shift->end_time)->format('h:i A') }}
                                    </td>
                                    <td>
                                        @php
                                            $workingDays = $shift->working_days;
                                            if (is_string($workingDays)) {
                                                $workingDays = json_decode($workingDays, true);
                                            }
                                        @endphp
                                        @if($workingDays && is_array($workingDays) && count($workingDays) > 0)
                                            @foreach($workingDays as $day)
                                                <span
                                                    class="badge bg-secondary-subtle text-secondary">{{ substr(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][$day], 0, 3) }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $shift->grace_period_minutes }} mins</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ roleRoute('shifts.edit', $shift) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ roleRoute('shifts.destroy', $shift) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No shifts found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection