@extends('layouts.app')

@section('title', 'My Documents')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">My Documents</h1>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Document Type</th>
                                <th>Document Name</th>
                                <th>Uploaded On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($document->document_type) }}</span>
                                    </td>
                                    <td>{{ $document->document_name }}</td>
                                    <td>{{ $document->created_at->format('d M, Y') }}</td>
                                    <td>
                                        <a href="{{ Storage::url($document->document_path) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        No documents found. Documents uploaded by HR will appear here.
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