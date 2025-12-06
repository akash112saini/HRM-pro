@extends('layouts.app')

@section('title', 'Performance Appraisals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Performance Appraisals</h1>
        <div>
            <a href="{{ route('appraisal-cycles.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-calendar-range"></i> Cycles
            </a>
            <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-bullseye"></i> Goals
            </a>
            <a href="{{ route('appraisals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Appraisal
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th>
                            <th>Cycle</th>
                            <th>Reviewer</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appraisals as $appraisal)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        {{ substr($appraisal->employee->first_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $appraisal->employee->first_name }} {{ $appraisal->employee->last_name }}</div>
                                        <small class="text-muted">{{ $appraisal->employee->designation->name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $appraisal->cycle->name }}</td>
                            <td>{{ $appraisal->reviewer->first_name }} {{ $appraisal->reviewer->last_name }}</td>
                            <td>
                                @switch($appraisal->status)
                                    @case('pending')
                                        <span class="badge bg-secondary-subtle text-secondary">Pending</span>
                                        @break
                                    @case('self_review')
                                        <span class="badge bg-info-subtle text-info">Self Review</span>
                                        @break
                                    @case('manager_review')
                                        <span class="badge bg-warning-subtle text-warning">Manager Review</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success-subtle text-success">Completed</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @if($appraisal->final_rating)
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $appraisal->final_rating ? '-fill' : '' }}"></i>
                                        @endfor
                                        <span class="text-dark ms-1">({{ $appraisal->final_rating }})</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('appraisals.show', $appraisal) }}" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('appraisals.edit', $appraisal) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-clipboard-data fs-1 d-block mb-3"></i>
                                    No appraisals found. Start a new review cycle.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
