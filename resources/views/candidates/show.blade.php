<div class="card-header">
    <h5 class="mb-0">Contact Information</h5>
</div>
<div class="card-body">
    <div class="mb-3">
        <label class="text-muted small text-uppercase">Email</label>
        <div><a href="mailto:{{ $candidate->email }}">{{ $candidate->email }}</a></div>
    </div>
    <div class="mb-3">
        <label class="text-muted small text-uppercase">Phone</label>
        <div>{{ $candidate->phone ?? 'N/A' }}</div>
    </div>
    <div class="mb-3">
        <label class="text-muted small text-uppercase">Source</label>
        <div><span class="badge bg-light text-dark border">{{ ucfirst($candidate->source) }}</span>
        </div>
    </div>
    <div class="mb-3">
        <label class="text-muted small text-uppercase">Applied Date</label>
        <div>{{ $candidate->applied_at->format('M d, Y h:i A') }}</div>
    </div>
    @if($candidate->resume_path)
        <div class="mt-4">
            <a href="#" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-file-earmark-text"></i> Download Resume
            </a>
        </div>
    @endif
</div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Stage History</h5>
    </div>
    <div class="card-body">
        <div class="timeline">
            <div class="mb-3">
                <div class="fw-bold text-primary">Current Stage: {{ ucfirst($candidate->current_stage) }}
                </div>
                <div class="small text-muted">Updated {{ $candidate->updated_at->diffForHumans() }}</div>
            </div>
        </div>

        <hr>

        <form action="{{ route('candidates.move', $candidate) }}" method="POST">
            @csrf
            <label class="form-label small fw-bold">Move to Stage</label>
            <div class="input-group">
                <select name="stage" class="form-select form-select-sm">
                    <option value="screening" {{ $candidate->current_stage == 'screening' ? 'selected' : '' }}>Screening
                    </option>
                    <option value="interview" {{ $candidate->current_stage == 'interview' ? 'selected' : '' }}>Interview
                    </option>
                    <option value="offer" {{ $candidate->current_stage == 'offer' ? 'selected' : '' }}>Offer
                    </option>
                    <option value="hired" {{ $candidate->current_stage == 'hired' ? 'selected' : '' }}>Hired
                    </option>
                    <option value="rejected" {{ $candidate->current_stage == 'rejected' ? 'selected' : '' }}>
                        Rejected</option>
                </select>
                <button class="btn btn-sm btn-primary">Move</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Interviews & Notes -->
<div class="col-md-8">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Interviews</h5>
            <a href="{{ route('interviews.create', ['candidate_id' => $candidate->id]) }}"
                class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> Schedule
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Round</th>
                            <th>Interviewer</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidate->interviews as $interview)
                            <tr>
                                <td>{{ $interview->round }}</td>
                                <td>{{ $interview->interviewer->first_name }}
                                    {{ $interview->interviewer->last_name }}
                                </td>
                                <td>{{ $interview->scheduled_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $interview->status == 'completed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($interview->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($interview->rating)
                                        @for($i = 0; $i < $interview->rating; $i++) <i
                                        class="bi bi-star-fill text-warning small"></i> @endfor
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No interviews scheduled yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Notes</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.candidates.notes', $candidate) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="notes" class="form-control" rows="5"
                        placeholder="Add internal notes about this candidate...">{{ $candidate->notes }}</textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save Notes</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
@endsection