

<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Users</h1>
        <a href="<?php echo e(roleRoute('users.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add User
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Linked Employee</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <?php echo e(substr($user->name, 0, 1)); ?>

                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo e($user->name); ?></div>
                                        <small class="text-muted"><?php echo e($user->email); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php switch($user->role):
                                    case ('super_admin'): ?>
                                        <span class="badge bg-danger">Super Admin</span>
                                        <?php break; ?>
                                    <?php case ('company_admin'): ?>
                                        <span class="badge bg-primary">Company Admin</span>
                                        <?php break; ?>
                                    <?php case ('manager'): ?>
                                        <span class="badge bg-warning text-dark">Manager</span>
                                        <?php break; ?>
                                    <?php case ('employee'): ?>
                                        <span class="badge bg-info text-dark">Employee</span>
                                        <?php break; ?>
                                    <?php default: ?>
                                        <span class="badge bg-secondary"><?php echo e($user->role); ?></span>
                                <?php endswitch; ?>
                            </td>
                            <td>
                                <?php if($user->employee): ?>
                                    <a href="<?php echo e(roleRoute('employees.show', $user->employee)); ?>" class="text-decoration-none">
                                        <?php echo e($user->employee->first_name); ?> <?php echo e($user->employee->last_name); ?>

                                        <small class="text-muted">(<?php echo e($user->employee->employee_code); ?>)</small>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Not Linked</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($user->is_active): ?>
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never'); ?>

                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?php echo e(roleRoute('users.edit', $user)); ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if($user->id !== auth()->id()): ?>
                                        <form action="<?php echo e(roleRoute('users.destroy', $user)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/users/index.blade.php ENDPATH**/ ?>