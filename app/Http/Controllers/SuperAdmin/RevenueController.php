<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionTransaction;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        // Total revenue
        $totalRevenue = SubscriptionTransaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Revenue by plan
        $revenueByPlan = SubscriptionTransaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->join('tenants', 'subscription_transactions.tenant_id', '=', 'tenants.id')
            ->select('tenants.subscription_plan', DB::raw('SUM(subscription_transactions.amount) as total'))
            ->groupBy('tenants.subscription_plan')
            ->get();

        // Revenue by payment method
        $revenueByMethod = SubscriptionTransaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Recent transactions
        $recentTransactions = SubscriptionTransaction::with(['tenant', 'subscriptionPlan'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->paginate(20);

        // Monthly trend
        $monthlyTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = SubscriptionTransaction::where('status', 'completed')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');

            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        return view('super-admin.revenue.index', compact(
            'totalRevenue',
            'revenueByPlan',
            'revenueByMethod',
            'recentTransactions',
            'monthlyTrend',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfMonth());

        $transactions = SubscriptionTransaction::with(['tenant', 'subscriptionPlan'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $filename = "revenue_report_" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Date',
                'Tenant',
                'Plan',
                'Amount',
                'Payment Method',
                'Transaction ID',
                'Status'
            ]);

            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_date->format('Y-m-d'),
                    $transaction->tenant->name,
                    $transaction->subscriptionPlan?->name ?? 'N/A',
                    $transaction->amount,
                    $transaction->payment_method,
                    $transaction->transaction_id ?? 'N/A',
                    $transaction->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
