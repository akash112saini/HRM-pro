@extends('super-admin.layout')

@section('title', 'Edit Email Template')

@section('content')
    <div class="page-header mb-4">
        <h1 class="page-title">Edit Template: {{ $emailTemplate->name }}</h1>
        <p class="text-muted">Customize the email template content</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('super-admin.email-templates.update', $emailTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Template Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $emailTemplate->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Subject</label>
                            <input type="text" name="subject" class="form-control"
                                value="{{ old('subject', $emailTemplate->subject) }}" required>
                            <small class="text-muted">You can use variables like @{{company_name}}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Body (HTML)</label>
                            <textarea name="body" class="form-control" rows="20"
                                required>{{ old('body', $emailTemplate->body) }}</textarea>
                            <small class="text-muted">HTML content with template variables</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Schedule Time</label>
                            <input type="time" name="schedule_time" class="form-control"
                                value="{{ old('schedule_time', $emailTemplate->schedule_time ?? '09:00') }}" required>
                            <small class="text-muted">Daily scheduled time to send renewal reminders (24-hour
                                format)</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Active (Email will be sent using this template)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Template
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                data-bs-target="#previewModal">
                                <i class="bi bi-eye"></i> Preview Template
                            </button>
                            <button type="button" class="btn btn-warning" onclick="resetToDefault()">
                                <i class="bi bi-arrow-counterclockwise"></i> Reset to Default
                            </button>
                            <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#sendTestModal">
                                <i class="bi bi-send"></i> Send Test Email
                            </button>
                            <a href="{{ route('super-admin.email-templates.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Available Variables</strong>
                </div>
                <div class="card-body">
                    <p class="small text-muted">Use these in subject and body:</p>
                    @if($emailTemplate && $emailTemplate->variables)
                        @foreach($emailTemplate->variables as $var => $description)
                            <div class="mb-2">
                                <code>@{{ '{{' . $var . '}}' }}</code>
                                <br><small class="text-muted">{{ $description }}</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No variables defined</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Template Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Email Template Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <strong>Subject:</strong>
                        <div class="p-2 bg-light border rounded" id="preview-subject">
                            {{ $emailTemplate->subject }}
                        </div>
                    </div>
                    <div>
                        <strong>Email Body:</strong>
                        <div class="border rounded p-3" style="background: #f8f9fa;">
                            <iframe id="preview-frame"
                                style="width: 100%; min-height: 500px; border: none; background: white;"></iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Send Test Email Modal -->
    <div class="modal fade" id="sendTestModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('super-admin.email-templates.send-test', $emailTemplate) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Send Test Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Recipient Email *</label>
                            <input type="email" name="test_email" class="form-control" required
                                placeholder="Enter email address">
                            <small class="text-muted">Email will be sent to this address</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Company Name (Optional)</label>
                            <input type="text" name="company_name" class="form-control" placeholder="Test Company">
                            <small class="text-muted">Will replace @{{company_name}} in template</small>
                        </div>
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                Other template variables will use test values.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Send Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview template function
        document.getElementById('previewModal').addEventListener('show.bs.modal', function () {
            const subject = document.querySelector('input[name="subject"]').value;
            const body = document.querySelector('textarea[name="body"]').value;

            // Update subject preview
            const subjectPreview = subject.replace(/\{\{(\w+)\}\}/g, function (match, key) {
                const sampleData = {
                    'company_name': 'Sample Company Ltd',
                    'plan_name': 'Premium Plan',
                    'expiry_date': 'December 15, 2025',
                    'days_remaining': '7',
                    'renewal_url': 'https://example.com/renew',
                    'current_year': new Date().getFullYear()
                };
                return sampleData[key] || match;
            });
            document.getElementById('preview-subject').textContent = subjectPreview;

            // Update body preview
            const bodyPreview = body.replace(/\{\{(\w+)\}\}/g, function (match, key) {
                const sampleData = {
                    'company_name': 'Sample Company Ltd',
                    'plan_name': 'Premium Plan',
                    'expiry_date': 'December 15, 2025',
                    'days_remaining': '7',
                    'renewal_url': 'https://example.com/renew',
                    'current_year': new Date().getFullYear()
                };
                return sampleData[key] || match;
            });

            const iframe = document.getElementById('preview-frame');
            iframe.contentDocument.open();
            iframe.contentDocument.write(bodyPreview);
            iframe.contentDocument.close();
        });

        // Reset to default function     with visual feedback
            function resetToDefault() {
                // Get confirmation
                const confirmed = confirm('Are you sure you want to reset the email body to the default template? This will overwrite your current changes.');

                if (confirmed) {
                    try {
                        const defaultBody = {!! json_encode($emailTemplate->default_body ?? $emailTemplate->body) !!};
                        const textarea = document.querySelector('textarea[name="body"]');

                        if (textarea && defaultBody) {
                            // Store current value for undo
                            const originalValue = textarea.value;

                            // Reset to default
                            textarea.value = defaultBody;

                            // Visual feedback - highlight textarea
                            textarea.style.border = '3px solid #28a745';
                            textarea.style.boxShadow = '0 0 10px rgba(40, 167, 69, 0.3)';

                            // Show success message
                            alert('✅ Template reset to default! Remember to click "Update Template" to save.');

                            // Remove highlight after 3 seconds
                            setTimeout(() => {
                                textarea.style.border = '';
                                textarea.style.boxShadow = '';
                            }, 3000);

                            console.log('Reset to default successful');
                        } else {
                            console.error('Textarea or default body not found');
                            alert('Error: Could not reset template');
                        }
                    } catch (error) {
                        console.error('Reset error:', error);
                        alert('Error: ' + error.message);
                    }
                } else {
                    console.log('Reset cancelled by user');
                }
            }
        </script>
@endsection
