@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="container-fluid">
        @if($employee && $employee->user)
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="page-title">My Profile</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="bi bi-pencil me-2"></i>Edit Profile
                </button>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($employee->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $employee->user->profile_photo_path) }}" alt="Profile Photo"
                                        class="rounded-circle img-thumbnail"
                                        style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto text-white"
                                        style="width: 150px; height: 150px; font-size: 4rem;">
                                        {{ substr($employee->user->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <h4 class="mb-1">{{ $employee->user->name }}</h4>
                            <p class="text-muted mb-3">{{ $employee->user->email }}</p>
                            <span class="badge bg-primary">{{ ucfirst($employee->user->role) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Full Name</label>
                                    <p class="fw-bold">{{ $employee->user->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Email Address</label>
                                    <p class="fw-bold">{{ $employee->user->email }}</p>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Role</label>
                                    <p class="fw-bold">{{ ucfirst(str_replace('_', ' ', $employee->user->role)) }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Joined Date</label>
                                    <p class="fw-bold">{{ $employee->user->created_at->format('d M, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-danger">
                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Profile Not Found</h4>
                <p class="mb-0">Unable to load your employee profile. Please contact your administrator.</p>
            </div>
        @endif
    </div>

    <!-- Edit Profile Modal -->
    @if($employee && $employee->user)
        <div class="modal fade" id="editProfileModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ roleRoute('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $employee->user->name }}"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ $employee->user->email }}"
                                    required>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">New Password (optional)</label>
                                <input type="password" name="password" class="form-control" minlength="8">
                                <small class="text-muted">Leave blank to keep current password</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" minlength="8">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection