<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Password Reset Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Password has been reset for <strong id="resetEmployeeName"></strong></p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>New Password:</strong>
                    <code id="newPasswordDisplay" class="fs-5 d-block mt-2 p-2 bg-light border rounded"></code>
                </div>
                <p class="text-muted small">Please share this password securely with the employee. They should change it
                    after their first login.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Login Link Modal -->
<div class="modal fade" id="loginLinkModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Login Link Generated</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Secure login link generated for <strong id="linkEmployeeName"></strong></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="loginLinkDisplay" readonly>
                    <button class="btn btn-outline-primary" type="button" onclick="copyLoginLink()">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Expires:</strong> <span id="linkExpiresAt"></span>
                </div>
                <p class="text-muted small">
                    <i class="bi bi-shield-check me-1"></i>
                    This link can be used once and will expire after 24 hours.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Impersonate Confirmation Modal -->
<div class="modal fade" id="impersonateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    Confirm Impersonation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>You are about to impersonate <strong id="impersonateEmployeeName"></strong></p>
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Important:</strong>
                    <ul class="mt-2 mb-0">
                        <li>This action will be logged for audit purposes</li>
                        <li>You will be logged in as this employee</li>
                        <li>You can stop impersonating at any time</li>
                    </ul>
                </div>
                <input type="hidden" id="impersonateUserId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="performImpersonate()">
                    <i class="bi bi-person-badge me-2"></i>Start Impersonation
                </button>
            </div>
        </div>
    </div>
</div>