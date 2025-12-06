<?php $__env->startSection('title', 'Password Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="page-title mb-0">Password Management</h1>
                <p class="text-muted">Manage passwords and login access for employees</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                                value="<?php echo e(request('search')); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            <option value="company_admin" <?php echo e(request('role') == 'company_admin' ? 'selected' : ''); ?>>Company
                                Admin</option>
                            <option value="manager" <?php echo e(request('role') == 'manager' ? 'selected' : ''); ?>>Manager</option>
                            <option value="employee" <?php echo e(request('role') == 'employee' ? 'selected' : ''); ?>>Employee</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <?php if(request()->hasAny(['search', 'role'])): ?>
                        <div class="col-md-2">
                            <a href="<?php echo e(route('admin.password.index')); ?>" class="btn btn-outline-secondary w-100">Clear</a>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Employee</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                style="width: 40px; height: 40px;">
                                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo e($user->name); ?></h6>
                                                <small class="text-muted"><?php echo e($user->email); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-secondary"><?php echo e(ucfirst(str_replace('_', ' ', $user->role))); ?></span>
                                    </td>
                                    <td>
                                        <?php if($user->employee): ?>
                                            <span
                                                class="badge <?php echo e($user->employee->status === 'active' ? 'bg-success' : 'bg-danger'); ?>">
                                                <?php echo e(ucfirst($user->employee->status)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($user->created_at->format('d M Y')); ?>

                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="resetPassword(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')">
                                                <i class="bi bi-arrow-clockwise me-1"></i> Reset
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success"
                                                onclick="generateLoginLink(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')">
                                                <i class="bi bi-link-45deg me-1"></i> Link
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                onclick="confirmImpersonate(<?php echo e($user->id); ?>, '<?php echo e($user->name); ?>')">
                                                <i class="bi bi-person-badge me-1"></i> Impersonate
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No users found matching your criteria</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php if($users->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($users->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php echo $__env->make('employees.partials.password-modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function resetPassword(userId, userName) {
            if (confirm(`Reset password for ${userName}?`)) {
                fetch(`/admin/password-management/reset/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('newPasswordDisplay').textContent = data.password;
                            document.getElementById('resetEmployeeName').textContent = userName;
                            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
                        } else {
                            alert('Error resetting password');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error resetting password');
                    });
            }
        }

        function generateLoginLink(userId, userName) {
            fetch(`/admin/password-management/login-link/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('loginLinkDisplay').value = data.link;
                        document.getElementById('linkExpiresAt').textContent = data.expires_at;
                        document.getElementById('linkEmployeeName').textContent = userName;
                        new bootstrap.Modal(document.getElementById('loginLinkModal')).show();
                    } else {
                        alert('Error generating login link');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error generating login link');
                });
        }

        function copyLoginLink() {
            const input = document.getElementById('loginLinkDisplay');
            input.select();
            document.execCommand('copy');

            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-check2"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        }

        function confirmImpersonate(userId, userName) {
            document.getElementById('impersonateEmployeeName').textContent = userName;
            document.getElementById('impersonateUserId').value = userId;
            new bootstrap.Modal(document.getElementById('impersonateModal')).show();
        }

        function performImpersonate() {
            const userId = document.getElementById('impersonateUserId').value;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/password-management/impersonate/${userId}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/admin/password-management/index.blade.php ENDPATH**/ ?>