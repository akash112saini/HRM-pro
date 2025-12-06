@extends('layouts.app')

@section('title', 'Company Documents')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Company Documents</h1>
            <button type="button" class="btn btn-primary">
                <i class="bi bi-upload me-2"></i>Upload Document
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Uploaded At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-text fs-4 text-primary me-3"></i>
                                            <span class="fw-bold">{{ $document->title }}</span>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($document->description, 50) }}</td>
                                    <td>{{ $document->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ Storage::url($document->file_path) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <i class="bi bi-folder2-open fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No documents found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $documents->links() }}
        </div>
    </div>
@endsection