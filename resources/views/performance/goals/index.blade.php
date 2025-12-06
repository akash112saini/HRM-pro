@extends('layouts.app')

@section('title', 'Employee Goals')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Employee Goals</h1>
            <a href="{{ route('goals.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Goal
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Goal Title</th>
                                <th>Cycle</th>
                                <th>Weightage</th>
                                <th>Progress</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($goals as $goal)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $goal->employee->first_name }} {{ $goal->employee->last_name }}
                                        </div>
                                    </td>
                                    <td>{{ $goal->title }}</td>
                                    <td>{{ $goal->cycle->name }}</td>
                                    <td>{{ $goal->weightage }}%</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress }}%">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $goal->progress }}%</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('goals.edit', $goal) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No goals found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection