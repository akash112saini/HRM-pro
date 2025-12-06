@extends('layouts.app')

@section('title', 'Asset Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Assets</h1>
        <a href="{{ route('assets.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Asset
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Asset Name</th>
                            <th>Code/Tag</th>
                            <th>Type</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Purchase Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $asset->name }}</div>
                                <small class="text-muted">{{ $asset->model_number }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $asset->code }}</span></td>
                            <td>{{ ucfirst($asset->type) }}</td>
                            <td>
                                @if($asset->assigned_to)
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                            {{ substr($asset->employee->first_name, 0, 1) }}
                                        </div>
                                        <small>{{ $asset->employee->first_name }} {{ $asset->employee->last_name }}</small>
                                    </div>
                                @else
                                    <span class="text-muted small">Unassigned</span>
                                @endif
                            </td>
                            <td>
                                @switch($asset->status)
                                    @case('available')
                                        <span class="badge bg-success-subtle text-success">Available</span>
                                        @break
                                    @case('assigned')
                                        <span class="badge bg-primary-subtle text-primary">Assigned</span>
                                        @break
                                    @case('maintenance')
                                        <span class="badge bg-warning-subtle text-warning">Maintenance</span>
                                        @break
                                    @case('retired')
                                        <span class="badge bg-secondary-subtle text-secondary">Retired</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ $asset->status }}</span>
                                @endswitch
                            </td>
                            <td>{{ $asset->purchase_date ? \Carbon\Carbon::parse($asset->purchase_date)->format('M d, Y') : '-' }}</td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-laptop fs-1 d-block mb-3"></i>
                                    No assets found.
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
