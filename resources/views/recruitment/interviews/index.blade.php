@extends('layouts.app')

@section('title', 'Interviews')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Scheduled Interviews</h1>
            <a href="{{ route('interviews.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Schedule Interview
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Interviewer</th>
                                <th>Round</th>
                                <th>Type</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interviews as $interview)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $interview->candidate->first_name }} {{ $interview->candidate->last_name }}
                                        <div class="small text-muted">{{ $interview->candidate->email }}</div>
                                    </td>
                                    <td>{{ $interview->interviewer->first_name }} {{ $interview->interviewer->last_name }}</td>
                                    <td>{{ $interview->round }}</td>
                                    <td>
                                        @if($interview->type === 'video') <i class="bi bi-camera-video me-1"></i> Video
                                        @elseif($interview->type === 'phone') <i class="bi bi-telephone me-1"></i> Phone
                                        @else <i class="bi bi-building me-1"></i> In Person
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ \Carbon\Carbon::parse($interview->scheduled_at)->format('M d, Y') }}</div>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($interview->scheduled_at)->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        @if($interview->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($interview->status === 'cancelled')
                                            <span class="badge bg-danger">Cancelled</span>
                                        @elseif($interview->status === 'no_show')
                                            <span class="badge bg-warning text-dark">No Show</span>
                                        @else
                                            <span class="badge bg-primary">Scheduled</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('interviews.edit', $interview) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('interviews.destroy', $interview) }}" method="POST"
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
                                    <td colspan="7" class="text-center py-5 text-muted">No interviews scheduled.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection