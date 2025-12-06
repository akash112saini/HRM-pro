@extends('super-admin.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="text-muted">Overview of your HRM-Pro platform</p>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Tenants -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1">Total Clients</p>
                        <h2 class="mb-0">{{ $totalTenants }}</h2>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-building text-primary fs-4"></i>
                    </div>
                </div>
                <small class="text-success"><i class="bi bi-arrow-up"></i> Active: {{ $activeTenants }}</small>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1">Total Users</p>
                        <h2 class="mb-0">{{ number_format($totalUsers) }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                </div>
                <small class="text-muted">Across all tenants</small>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1">Monthly Revenue</p>
                        <h2 class="mb-0">${{ number_format($monthlyRevenue, 2) }}</h2>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-currency-dollar text-info fs-4"></i>
                    </div>
                </div>
                <small class="text-muted">This month</small>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-muted mb-1">Total Revenue</p>
                        <h2 class="mb-0">${{ number_format($totalRevenue, 2) }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                        <i class="bi bi-graph-up text-warning fs-4"></i>
                    </div>
                </div>
                <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> {{ $expiringSubscriptions }} expiring
                    soon</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Revenue Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Revenue Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Subscription Breakdown -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Subscription Plans</h5>
                </div>
                <div class="card-body">
                    <canvas id="subscriptionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-3">
        <!-- Recent Tenants -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Clients</h5>
                    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Email</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTenants as $tenant)
                                    <tr>
                                        <td>
                                            <strong>{{ $tenant->name }}</strong><br>
                                            <small class="text-muted">{{ $tenant->slug }}</small>
                                        </td>
                                        <td>{{ $tenant->contact_email }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ ucfirst($tenant->subscription_plan) }}</span>
                                        </td>
                                        <td>
                                            @if($tenant->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $tenant->created_at->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No tenants found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart');
            const revenueData = @json($revenueChartData);

            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map(d => d.month),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: revenueData.map(d => d.revenue),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Subscription Chart
            const subscriptionCtx = document.getElementById('subscriptionChart');
            const subscriptionData = @json($subscriptionStats);

            new Chart(subscriptionCtx, {
                type: 'doughnut',
                data: {
                    labels: subscriptionData.map(d => d.subscription_plan.charAt(0).toUpperCase() + d.subscription_plan.slice(1)),
                    datasets: [{
                        data: subscriptionData.map(d => d.count),
                        backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        </script>
    @endpush
@endsection