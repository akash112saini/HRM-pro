@extends('super-admin.layout')

@section('title', 'Revenue Reports')

@section('content')
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Revenue Reports</h1>
                <p class="text-muted">Track platform revenue and payments</p>
            </div>
            <a href="{{ route('super-admin.revenue.export', request()->all()) }}" class="btn btn-success">
                <i class="bi bi-download me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ request('start_date', $startDate) }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Revenue Summary -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Total Revenue</h6>
                <h2>${{ number_format($totalRevenue, 2) }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Revenue by Plan</h6>
                @foreach($revenueByPlan as $item)
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ ucfirst($item->subscription_plan) }}</span>
                        <strong>${{ number_format($item->total, 2) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Payment Methods</h6>
                @foreach($revenueByMethod as $item)
                    <div class="d-flex justify-content-between mb-1">
                        <span>{{ ucfirst($item->payment_method ?? 'N/A') }}</span>
                        <strong>${{ number_format($item->total, 2) }}</strong>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Monthly Trend (Last 12 Months)</h5>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Recent Transactions</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Tenant</th>
                            <th>Plan</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('M d, Y') }}</td>
                                <td>{{ $transaction->tenant->name }}</td>
                                <td>{{ $transaction->subscriptionPlan->name ?? 'N/A' }}</td>
                                <td><strong>${{ number_format($transaction->amount, 2) }}</strong></td>
                                <td>{{ ucfirst($transaction->payment_method ?? 'N/A') }}</td>
                                <td>
                                    <span class="badge bg-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No transactions found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($recentTransactions->hasPages())
            <div class="card-footer">
                {{ $recentTransactions->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            const ctx = document.getElementById('revenueChart');
            const trendData = @json($monthlyTrend);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trendData.map(d => d.month),
                    datasets: [{
                        label: 'Revenue ($)',
                        data: trendData.map(d => d.revenue),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endpush
@endsection