<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Super Admin - {{ config('app.name', 'HRM-Pro') }} - @yield('title')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Favicon -->
    @php $favicon = \App\Models\Setting::get('super_admin_favicon'); @endphp
    @if($favicon)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}" type="image/x-icon">
    @endif

    <style>
        :root {
            --sidebar-width: 260px;
            --super-admin-primary:
                {{ \App\Models\Setting::get('admin_panel_primary_color', '#6366f1') }}
            ;
            --super-admin-secondary:
                {{ \App\Models\Setting::get('admin_panel_secondary_color', '#8b5cf6') }}
            ;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background-color: #f1f5f9;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--super-admin-primary) 0%, var(--super-admin-secondary) 100%);
            padding: 20px 0;
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar .logo {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 10px 24px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .sidebar .logo i {
            margin-right: 12px;
            font-size: 1.8rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 24px;
            margin: 4px 12px;
            border-radius: 10px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
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
            padding: 24px;
            min-height: 100vh;
        }

        .top-navbar {
            background-color: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 16px 24px;
            margin-bottom: 24px;
            border-radius: 12px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--super-admin-primary);
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
        }

        .page-header {
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--super-admin-primary) 0%, var(--super-admin-secondary) 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--super-admin-secondary) 0%, var(--super-admin-primary) 100%);
        }

        .badge {
            padding: 6px 12px;
            font-weight: 500;
            border-radius: 6px;
        }

        .table {
            background-color: #fff;
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            @php $logo = \App\Models\Setting::get('super_admin_logo'); @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Super Admin Logo"
                    style="max-height: 50px; max-width: 100%; display: block;">
            @else
                <i class="bi bi-shield-check"></i> Super Admin
            @endif
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('super-admin.dashboard') }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.tenants.index') }}">
                    <i class="bi bi-building"></i> Clients (Tenants)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.subscription-plans.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.subscription-plans.index') }}">
                    <i class="bi bi-box-seam"></i> Subscription Plans
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.subscriptions.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.subscriptions.index') }}">
                    <i class="bi bi-credit-card"></i> Subscriptions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.revenue.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.revenue.index') }}">
                    <i class="bi bi-graph-up"></i> Revenue Reports
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.passwords.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.passwords.index') }}">
                    <i class="bi bi-key"></i> Password Management
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.super-admins.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.super-admins.index') }}">
                    <i class="bi bi-person-check"></i> Super Admin Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.roles.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.roles.index') }}">
                    <i class="bi bi-shield-lock"></i> Roles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.settings.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.settings.index') }}">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('super-admin.email-templates.*') ? 'active' : '' }}"
                    href="{{ route('super-admin.email-templates.index') }}">
                    <i class="bi bi-envelope-paper"></i> Email Templates
                </a>
            </li>
            <li class="nav-item mt-4 dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('super-admin.profile.*') ? 'active' : '' }}"
                    href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i> Account
                </a>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item" href="{{ route('super-admin.profile.show') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="/logout" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 text-muted">Welcome back, {{ auth()->user()->name }}</h5>
            </div>
            <div>
                <span class="badge bg-primary">Super Admin</span>
            </div>
        </div>

        <!-- Alerts -->
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

    @stack('scripts')
</body>

</html>