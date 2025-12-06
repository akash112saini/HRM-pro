@if(session('impersonating'))
    <div class="alert alert-warning alert-dismissible fade show mb-0 rounded-0 border-0 d-flex align-items-center"
        role="alert">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <i class="bi bi-person-badge me-2"></i>
                    <strong>Impersonating:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})
                </div>
                <div class="col-auto">
                    <a href="{{ route('stop-impersonation') }}" class="btn btn-sm btn-dark">
                        <i class="bi bi-x-circle me-1"></i>Stop Impersonation
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif