<div class="card mb-2 shadow-sm" style="cursor: move;">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0">{{ $candidate->name }}</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.candidates.show', $candidate) }}"><i
                                class="bi bi-eye me-2"></i>View Details</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="moveStage({{ $candidate->id }}, 'screening')"><i
                                class="bi bi-arrow-right me-2"></i>Move to Screening</a></li>
                    <li><a class="dropdown-item" href="#" onclick="moveStage({{ $candidate->id }}, 'interview')"><i
                                class="bi bi-arrow-right me-2"></i>Move to Interview</a></li>
                    <li><a class="dropdown-item" href="#" onclick="moveStage({{ $candidate->id }}, 'offer')"><i
                                class="bi bi-arrow-right me-2"></i>Move to Offer</a></li>
                    <li><a class="dropdown-item" href="#" onclick="moveStage({{ $candidate->id }}, 'hired')"><i
                                class="bi bi-arrow-right me-2"></i>Mark as Hired</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#"
                            onclick="moveStage({{ $candidate->id }}, 'rejected')"><i
                                class="bi bi-x-circle me-2"></i>Reject</a></li>
                </ul>
            </div>
        </div>

        <div class="small text-muted mb-2">
            <i class="bi bi-briefcase"></i> {{ $candidate->jobPosting->title ?? 'N/A' }}
        </div>

        <div class="d-flex justify-content-between align-items-center small mb-2">
            <span><i class="bi bi-envelope"></i> {{ Str::limit($candidate->email, 20) }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-secondary-subtle text-secondary">
                {{ ucfirst($candidate->source) }}
            </span>
            <small class="text-muted">{{ $candidate->applied_at->diffForHumans() }}</small>
        </div>

        @if($candidate->interviews_count > 0)
            <div class="mt-2 pt-2 border-top">
                <small class="text-muted">
                    <i class="bi bi-calendar-check"></i> {{ $candidate->interviews_count }} interview(s)
                </small>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        function moveStage(candidateId, stage) {
            if (confirm('Move candidate to ' + stage + '?')) {
                fetch(`/candidates/${candidateId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ stage: stage })
                }).then(response => {
                    if (response.ok) {
                        location.reload();
                    }
                });
            }
        }
    </script>
@endpush