

<?php $__env->startSection('title', 'Employee Goals'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title">Employee Goals</h1>
            <a href="<?php echo e(route('goals.create')); ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Goal
            </a>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Goal Title</th>
                                <th>Cycle</th>
                                <th>Weightage</th>
                                <th>Progress</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $goals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $goal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo e($goal->employee->first_name); ?> <?php echo e($goal->employee->last_name); ?>

                                        </div>
                                    </td>
                                    <td><?php echo e($goal->title); ?></td>
                                    <td><?php echo e($goal->cycle->name); ?></td>
                                    <td><?php echo e($goal->weightage); ?>%</td>
                                    <td>
                                        <div class="progress" style="height: 6px; width: 100px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo e($goal->progress); ?>%">
                                            </div>
                                        </div>
                                        <small class="text-muted"><?php echo e($goal->progress); ?>%</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="<?php echo e(route('goals.edit', $goal)); ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">No goals found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/performance/goals/index.blade.php ENDPATH**/ ?>