@extends('layouts.app')

@section('title', 'Appraisal Cycles')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Appraisal Cycles</h1>
            <a href="{{ route('appraisal-cycles.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Cycle
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cycles as $cycle)
                                <tr>
                                    <td class="fw-bold">{{ $cycle->name }}</td>
                                    <td>{{ $cycle->start_date->format('M d, Y') }}</td>
                                    <td>{{ $cycle->end_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($cycle->is_active)
                                            <span class="badge bg-success-subtle text-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('appraisal-cycles.edit', $cycle) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No appraisal cycles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection