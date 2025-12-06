<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalRevenue = SubscriptionTransaction::where('status', 'completed')->sum('amount');
        $monthlyRevenue = SubscriptionTransaction::where('status', 'completed')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // Recent tenants
        $recentTenants = Tenant::latest()->take(5)->get();

        // Subscription breakdown
        $subscriptionStats = Tenant::selectRaw('subscription_plan, COUNT(*) as count')
            ->groupBy('subscription_plan')
            ->get();

        // Revenue chart data (last 6 months)
        $revenueChartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = SubscriptionTransaction::where('status', 'completed')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $revenueChartData[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Expiring subscriptions (next 30 days)
        $expiringSubscriptions = Tenant::where('is_active', true)
            ->whereNotNull('subscription_expires_at')
            ->whereBetween('subscription_expires_at', [now(), now()->addDays(30)])
            ->count();

        return view('super-admin.dashboard', compact(
            'totalTenants',
            'activeTenants',
            'totalUsers',
            'totalRevenue',
            'monthlyRevenue',
            'recentTenants',
            'subscriptionStats',
            'revenueChartData',
            'expiringSubscriptions'
        ));
    }
}
