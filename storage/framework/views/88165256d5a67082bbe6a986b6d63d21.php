<?php $__env->startSection('title', 'Edit Role'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Edit Role: <?php echo e($role->name); ?></h1>
                <a href="<?php echo e(roleRoute('roles.index')); ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(roleRoute('roles.update', $role)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="name" name="name" value="<?php echo e(old('name', $role->name)); ?>" 
                                <?php echo e($role->is_system_role ? 'readonly' : 'required'); ?> autofocus>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <?php if($role->is_system_role): ?>
                                <small class="text-muted">System role names cannot be changed</small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                id="description" name="description" rows="3"><?php echo e(old('description', $role->description)); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                    <?php echo e(old('is_active', $role->is_active) ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (Inactive roles cannot be assigned to users)
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Permissions <span class="text-danger">*</span></label>
                            <?php $__errorArgs = ['permissions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="alert alert-danger"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                            <div class="accordion" id="permissionsAccordion">
                                <?php $__currentLoopData = $availablePermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module => $permissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading<?php echo e(ucfirst($module)); ?>">
                                            <button class="accordion-button <?php echo e($loop->first ? '' : 'collapsed'); ?>" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse<?php echo e(ucfirst($module)); ?>"
                                                aria-expanded="<?php echo e($loop->first ? 'true' : 'false'); ?>">
                                                <strong><?php echo e(ucwords(str_replace('_', ' ', $module))); ?></strong>
                                                <small class="ms-2 text-muted">(<?php echo e(count($permissions)); ?> permissions)</small>
                                            </button>
                                        </h2>
                                        <div id="collapse<?php echo e(ucfirst($module)); ?>" 
                                            class="accordion-collapse collapse <?php echo e($loop->first ? 'show' : ''); ?>" 
                                            data-bs-parent="#permissionsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="col-md-6 mb-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" 
                                                                    name="permissions[]" value="<?php echo e($permission); ?>" 
                                                                    id="perm_<?php echo e($permission); ?>"
                                                                    <?php echo e(in_array($permission, old('permissions', $role->permissions ?? [])) ? 'checked' : ''); ?>>
                                                                <label class="form-check-label" for="perm_<?php echo e($permission); ?>">
                                                                    <?php echo e($label); ?>

                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="<?php echo e(roleRoute('roles.index')); ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Role Information</h5>
                    <ul class="list-unstyled small">
                        <li><strong>Slug:</strong> <?php echo e($role->slug); ?></li>
                        <li><strong>Type:</strong> 
                            <?php if($role->is_system_role): ?>
                                <span class="badge bg-primary">System Role</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Custom Role</span>
                            <?php endif; ?>
                        </li>
                        <li><strong>Users:</strong> <?php echo e($role->users()->count()); ?></li>
                        <li><strong>Created:</strong> <?php echo e($role->created_at->format('M d, Y')); ?></li>
                    </ul>
                </div>
            </div>

            <?php if($role->is_system_role): ?>
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title text-info">System Role</h5>
                    <p class="small mb-0">
                        This is a system role. The name cannot be changed, but you can modify permissions and description.
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/roles/edit.blade.php ENDPATH**/ ?>