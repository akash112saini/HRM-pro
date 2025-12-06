<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'HRM-Pro') }} - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e3a8a 0%, #1e40af 100%);
            padding-top: 20px;
            padding-bottom: 20px;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 24px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            border: none;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }

        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }

        .navbar-top {
            margin-left: var(--sidebar-width);
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 2px solid #f0f0f0;
            font-weight: 600;
            padding: 15px 20px;
        }

        .stat-card {
            border-left: 4px solid var(--primary-color);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .badge {
            padding: 6px 12px;
            font-weight: 500;
        }

        .table {
            background-color: #fff;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 20px;
        }

        .logo-text {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 10px 20px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content,
            .navbar-top {
                margin-left: 0;
                width: 100%;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                width: 100vw;
                height: 100vh;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                top: 0;
                left: 0;
            }

            .sidebar-overlay.active {
                display: block;
            }

            /* Enhanced mobile responsiveness */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
                margin-bottom: 1rem;
            }

            .stat-card {
                margin-bottom: 0.5rem;
            }

            .card {
                margin-bottom: 1rem;
            }

            .g-4 {
                --bs-gutter-y: 1rem;
            }
        }

        @media (max-width: 576px) {
            .page-title {
                font-size: 1.5rem;
            }

            .main-content {
                padding: 15px;
            }

            .card-body {
                padding: 1rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .stat-card h3 {
                font-size: 1.75rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-text">
            <i class="bi bi-building"></i> HRM-Pro
        </div>

        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @if(in_array(auth()->user()->role, ['super_admin', 'company_admin']))
                <!-- Users -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>

                <!-- Roles -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                        href="{{ route('admin.roles.index') }}">
                        <i class="bi bi-shield-lock"></i> Roles
                    </a>
                </li>
            @endif

            @if(in_array(auth()->user()->role, ['super_admin', 'company_admin', 'manager']))
                <!-- Employees -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}"
                        href="{{ route('admin.employees.index') }}">
                        <i class="bi bi-person-badge"></i> Employees
                    </a>
                </li>

                <!-- Attendance -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"
                        href="{{ route('admin.attendance.index') }}">
                        <i class="bi bi-calendar-check"></i> Attendance
                    </a>
                </li>

                <!-- Leave Requests -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.leave-requests.*') ? 'active' : '' }}"
                        href="{{ route('admin.leave-requests.index') }}">
                        <i class="bi bi-calendar-x"></i> Leave Requests
                    </a>
                </li>

                <!-- Payroll -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}"
                        href="{{ route('admin.payroll.index') }}">
                        <i class="bi bi-cash-stack"></i> Payroll
                    </a>
                </li>

                <!-- Job Postings -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}"
                        href="{{ route('admin.jobs.index') }}">
                        <i class="bi bi-briefcase"></i> Job Postings
                    </a>
                </li>

                <!-- Candidates -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}"
                        href="{{ route('admin.candidates.index') }}">
                        <i class="bi bi-person-lines-fill"></i> Candidates
                    </a>
                </li>

                <!-- Interviews -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.interviews.*') ? 'active' : '' }}"
                        href="{{ route('admin.interviews.index') }}">
                        <i class="bi bi-camera-video"></i> Interviews
                    </a>
                </li>

                <!-- Appraisals -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.appraisals.*') ? 'active' : '' }}"
                        href="{{ route('admin.appraisals.index') }}">
                        <i class="bi bi-star"></i> Appraisals
                    </a>
                </li>

                <!-- Goals -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.goals.*') ? 'active' : '' }}"
                        href="{{ route('admin.goals.index') }}">
                        <i class="bi bi-bullseye"></i> Goals
                    </a>
                </li>

                <!-- Password Management -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.password.*') ? 'active' : '' }}"
                        href="{{ route('admin.password.index') }}">
                        <i class="bi bi-key"></i> Passwords
                    </a>
                </li>

                <!-- Assets -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.assets.*') ? 'active' : '' }}"
                        href="{{ route('admin.assets.index') }}">
                        <i class="bi bi-pc-display"></i> Assets
                    </a>
                </li>

                <!-- Biometric Devices -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}"
                        href="{{ route('admin.devices.index') }}">
                        <i class="bi bi-fingerprint"></i> Biometric Devices
                    </a>
                </li>

                <!-- Resignations -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.resignations.*') ? 'active' : '' }}"
                        href="{{ route('admin.resignations.index') }}">
                        <i class="bi bi-box-arrow-right"></i> Resignations
                    </a>
                </li>

                <!-- Settings Dropdown -->
                <li class="nav-item">
                    <a class="nav-link" href="#" id="settingsDropdown" role="button"
                        onclick="toggleSettingsDropdown(); return false;">
                        <i class="bi bi-gear"></i> Settings
                        <i class="bi bi-chevron-down ms-auto" id="settingsChevron" style="transition: transform 0.3s;"></i>
                    </a>
                    <ul class="nav flex-column ms-3" id="settingsSubmenu" style="display: none;">
                        <!-- Departments -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"
                                href="{{ route('admin.departments.index') }}">
                                <i class="bi bi-diagram-3"></i> Departments
                            </a>
                        </li>

                        <!-- Designations -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.designations.*') ? 'active' : '' }}"
                                href="{{ route('admin.designations.index') }}">
                                <i class="bi bi-award"></i> Designations
                            </a>
                        </li>

                        <!-- Shifts -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.shifts.*') ? 'active' : '' }}"
                                href="{{ route('admin.shifts.index') }}">
                                <i class="bi bi-clock"></i> Shifts
                            </a>
                        </li>

                        <!-- Leave Types -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.leave-types.*') ? 'active' : '' }}"
                                href="{{ route('admin.leave-types.index') }}">
                                <i class="bi bi-file-text"></i> Leave Types
                            </a>
                        </li>

                        <!-- Holidays -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.holidays.*') ? 'active' : '' }}"
                                href="{{ route('admin.holidays.index') }}">
                                <i class="bi bi-calendar-event"></i> Holidays
                            </a>
                        </li>

                        <!-- Salary Structures -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.salary-structures.*') ? 'active' : '' }}"
                                href="{{ route('admin.salary-structures.index') }}">
                                <i class="bi bi-wallet2"></i> Salary Structures
                            </a>
                        </li>

                        <!-- Tax Slabs -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.tax-slabs.*') ? 'active' : '' }}"
                                href="{{ route('admin.tax-slabs.index') }}">
                                <i class="bi bi-percent"></i> Tax Slabs
                            </a>
                        </li>

                        <!-- Biometric Devices -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.devices.*') ? 'active' : '' }}"
                                href="{{ route('admin.devices.index') }}">
                                <i class="bi bi-fingerprint"></i> Biometric Devices
                            </a>
                        </li>

                        <!-- Documents -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('documents.*') ? 'active' : '' }}"
                                href="{{ route('documents.company') }}">
                                <i class="bi bi-file-earmark-text"></i> Documents
                            </a>
                        </li>

                        <!-- Reports -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}"
                                href="{{ route('reports.attendance') }}">
                                <i class="bi bi-graph-up"></i> Reports
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            <!-- Profile & Logout -->
            <li class="nav-item mt-4">
                <a class="nav-link {{ request()->routeIs('employee.profile') ? 'active' : '' }}"
                    href="{{ route('employee.profile') }}">
                    <i class="bi bi-person-circle"></i> My Profile
                </a>
            </li>
            <li class="nav-item">
                <form action="/logout" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="nav-link text-start w-100 border-0" style="background: transparent;">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-top px-4 py-3">
        <div class="d-flex align-items-center">
            <button class="btn btn-link d-md-none me-3 p-0" id="sidebarToggle">
                <i class="bi bi-list fs-1"></i>
            </button>
            <button class="btn btn-link text-dark position-relative" data-bs-toggle="dropdown">
                <i class="bi bi-bell fs-5"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                <li>
                    <h6 class="dropdown-header">Notifications</h6>
                </li>
                <li><a class="dropdown-item" href="#">New leave request from John Doe</a></li>
                <li><a class="dropdown-item" href="#">Payroll generated for November</a></li>
                <li><a class="dropdown-item" href="#">Interview scheduled tomorrow</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item text-center" href="#">View All</a></li>
            </ul>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link text-dark d-flex align-items-center" data-bs-toggle="dropdown">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                    style="width: 35px; height: 35px;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down ms-2"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i> Profile</a></li>
                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i> Settings</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
        </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarToggle = document.getElementById('sidebarToggle');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function () {
                    sidebar.classList.toggle('active');
                    sidebarOverlay.classList.toggle('active');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function () {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                });
            }

            // Settings Dropdown Toggle
            window.toggleSettingsDropdown = function () {
                const submenu = document.getElementById('settingsSubmenu');
                const chevron = document.getElementById('settingsChevron');
                if (submenu.style.display === 'none' || submenu.style.display === '') {
                    submenu.style.display = 'block';
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    submenu.style.display = 'none';
                    chevron.style.transform = 'rotate(0deg)';
                }
            };
        });
    </script>
    <!-- Chart.js (for dashboard) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    @stack('scripts')
</body>

</html>