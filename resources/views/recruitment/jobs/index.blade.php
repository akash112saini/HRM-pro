@extends('layouts.app')

@section('title', 'Job Postings')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Job Postings</h1>
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Post New Job
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job Title</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Posted Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobPostings as $job)
                                <tr>
                                    <td class="fw-bold">{{ $job->title }}</td>
                                    <td>{{ $job->department->name ?? '-' }}</td>
                                    <td>
                                        <span
                                            class="badge bg-info text-dark">{{ ucwords(str_replace('_', ' ', $job->type)) }}</span>
                                    </td>
                                    <td>{{ $job->location }}</td>
                                    <td>
                                        @if($job->status === 'published')
                                            <span class="badge bg-success">Published</span>
                                        @elseif($job->status === 'closed')
                                            <span class="badge bg-secondary">Closed</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($job->posted_date)->format('M d, Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.jobs.edit', $job) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST"
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
                                    <td colspan="7" class="text-center py-5 text-muted">No job postings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection