@extends('super-admin.layout')

@section('title', 'Password Audit Log')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Password Audit Log</h1>
        <p class="text-muted">User: {{ $user->name }} ({{ $user->email }})</p>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Changed By</th>
                            <th>IP Address</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs as $log)
                            <tr>
                                <td>{{ $log->changed_at->format('M d, Y H:i:s') }}</td>
                                <td>
                                    <span class="badge bg-{{ $log->action == 'force_reset' ? 'warning' : 'info' }}">
                                        {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>
                                <td>{{ $log->changedBy->name ?? 'Self' }}</td>
                                <td><code>{{ $log->ip_address }}</code></td>
                                <td>{{ $log->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No password changes recorded</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($auditLogs->hasPages())
            <div class="card-footer">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>

    <div class="mt-3">
        <a href="{{ route('super-admin.passwords.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Password Management
        </a>
    </div>
@endsection