

<?php $__env->startSection('title', 'Role Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Roles & Permissions</h1>
                        <p class="text-muted mb-0">Manage roles and their access capabilities</p>
                    </div>
                    <a href="<?php echo e(roleRoute('roles.create')); ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Create Role
                    </a>
                </div>
            </div>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Users</th>
                                <th>Permissions</th>
                                <th>Description</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($role->name); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e($role->slug); ?></small>
                                    </td>
                                    <td>
                                        <?php if($role->is_system_role): ?>
                                            <span class="badge bg-primary">System</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Custom</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($role->is_active): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?php echo e($role->users()->count()); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?php echo e(count($role->permissions ?? [])); ?> permissions
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo e(Str::limit($role->description, 50)); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?php echo e(roleRoute('roles.edit', $role)); ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit Role">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if(!$role->is_system_role && $role->users()->count() === 0): ?>
                                                <form action="<?php echo e(roleRoute('roles.destroy', $role)); ?>" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this role?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Role">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No roles found. <a href="<?php echo e(roleRoute('roles.create')); ?>">Create your first role</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">About Roles</h5>
                    <ul class="mb-0 small">
                        <li><strong>System Roles:</strong> Default roles that cannot be deleted (Company Admin, Manager,
                            Employee)</li>
                        <li><strong>Custom Roles:</strong> Roles created by admins for specific needs (e.g., HR Manager,
                            Recruiter)</li>
                        <li><strong>Permissions:</strong> Each role can have specific permissions for different modules</li>
                        <li><strong>Deletion:</strong> Custom roles can only be deleted if they have no users assigned</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/roles/index.blade.php ENDPATH**/ ?>