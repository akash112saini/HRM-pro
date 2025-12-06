@extends('layouts.app')

@section('title', 'Designations')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Designations</h1>
            <a href="{{ roleRoute('designations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Designation
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Designation Name</th>
                                <th>Department</th>
                                <th>Level</th>
                                <th>Description</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($designations as $designation)
                                <tr>
                                    <td class="fw-bold">{{ $designation->name }}</td>
                                    <td>
                                        @if($designation->department)
                                            <span
                                                class="badge bg-light text-dark border">{{ $designation->department->name }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $designation->level }}</td>
                                    <td>{{ Str::limit($designation->description, 50) }}</td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ roleRoute('designations.edit', $designation) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ roleRoute('designations.destroy', $designation) }}" method="POST"
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
                                    <td colspan="5" class="text-center py-5 text-muted">No designations found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection