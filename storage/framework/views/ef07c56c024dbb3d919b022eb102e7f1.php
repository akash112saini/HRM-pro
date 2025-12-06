

<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <h1 class="page-title">Dashboard</h1>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card" style="border-left-color: #3B82F6;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Total Employees</p>
                                <h3 class="mb-0"><?php echo e($stats['total_employees'] ?? 156); ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people fs-2 text-primary"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success-subtle text-success">
                                <i class="bi bi-arrow-up"></i> 12% from last month
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card" style="border-left-color: #10B981;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Present Today</p>
                                <h3 class="mb-0"><?php echo e($stats['present_today'] ?? 142); ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-check-circle fs-2 text-success"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted small">91% attendance rate</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card" style="border-left-color: #F59E0B;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Pending Leaves</p>
                                <h3 class="mb-0"><?php echo e($stats['pending_leaves'] ?? 8); ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-calendar-x fs-2 text-warning"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted small">Awaiting approval</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card stat-card" style="border-left-color: #EF4444;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1">Absent Today</p>
                                <h3 class="mb-0"><?php echo e($stats['absent_today'] ?? 14); ?></h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="bi bi-person-x fs-2 text-danger"></i>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-muted small">9% of total employees</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Leaves and Birthdays -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Pending Leaves</h5>
                        <span class="badge bg-warning"><?php echo e($stats['pending_leaves'] ?? 8); ?></span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Employee</th>
                                        <th>Leave Type</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    SD
                                                </div>
                                                <span class="d-none d-md-inline">Sarah Davis</span>
                                                <span class="d-md-none">S. Davis</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-primary-subtle text-primary">Casual Leave</span></td>
                                        <td>2 days</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    MB
                                                </div>
                                                <span class="d-none d-md-inline">Michael Brown</span>
                                                <span class="d-md-none">M. Brown</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-danger-subtle text-danger">Sick Leave</span></td>
                                        <td>1 day</td>
                                        <td><span class="badge bg-success">Approved</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-2"
                                                    style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    EW
                                                </div>
                                                <span class="d-none d-md-inline">Emily Wilson</span>
                                                <span class="d-md-none">E. Wilson</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-success-subtle text-success">Privilege Leave</span></td>
                                        <td>5 days</td>
                                        <td><span class="badge bg-warning">Pending</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Upcoming Birthdays</h5>
                        <span class="badge bg-primary">This Week</span>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">John Anderson</h6>
                                    <small class="text-muted">Software Engineer - Tomorrow</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Wish</button>
                            </div>
                            <div class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Lisa Martinez</h6>
                                    <small class="text-muted">HR Manager - Dec 2</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Wish</button>
                            </div>
                            <div class="list-group-item d-flex align-items-center border-0 px-0">
                                <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-gift"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">David Kim</h6>
                                    <small class="text-muted">Product Manager - Dec 3</small>
                                </div>
                                <button class="btn btn-sm btn-outline-primary">Wish</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row g-4 mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Attendance Trend</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="attendanceChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Department Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Responsive table improvements */
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: 10px;
            }

            .table th,
            .table td {
                font-size: 0.85rem;
                padding: 0.75rem 0.5rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stat-card h3 {
                font-size: 1.75rem;
            }

            .badge {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
        }

        @media (max-width: 576px) {
            .stat-card h3 {
                font-size: 1.5rem;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .card-header h5 {
                font-size: 1rem;
            }

            .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        // Attendance Trend Chart
        const ctx1 = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Present',
                    data: [142, 138, 145, 140, 143, 85, 32],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Absent',
                    data: [14, 18, 11, 16, 13, 8, 5],
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Department Distribution Chart
        const ctx2 = document.getElementById('departmentChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['IT', 'Sales', 'HR', 'Finance', 'Operations'],
                datasets: [{
                    data: [45, 30, 15, 25, 41],
                    backgroundColor: [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#8B5CF6',
                        '#EC4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/HR-pro/resources/views/dashboard.blade.php ENDPATH**/ ?>