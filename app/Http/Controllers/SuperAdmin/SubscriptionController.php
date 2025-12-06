<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::query();

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        // Filter by expiring subscriptions
        if ($request->filled('expiring')) {
            $query->whereNotNull('subscription_expires_at')
                ->whereBetween('subscription_expires_at', [now(), now()->addDays(30)]);
        }

        $tenants = $query->with('users')->latest()->paginate(20);
        $plans = SubscriptionPlan::active()->get();

        return view('super-admin.subscriptions.index', compact('tenants', 'plans'));
    }

    public function updatePlan(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise',
            'subscription_expires_at' => 'nullable|date',
            'reason' => 'nullable|string',
        ]);

        $oldPlan = $tenant->subscription_plan;
        $oldExpiry = $tenant->subscription_expires_at;

        $tenant->update([
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
        ]);

        // Log subscription change
        \DB::table('subscription_changes')->insert([
            'tenant_id' => $tenant->id,
            'old_plan' => $oldPlan,
            'new_plan' => $validated['subscription_plan'],
            'old_expires_at' => $oldExpiry,
            'new_expires_at' => $validated['subscription_expires_at'] ?? null,
            'changed_by' => auth()->id(),
            'reason' => $validated['reason'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ActivityLog::logActivity(
            'updated_subscription',
            $tenant,
            ['subscription_plan' => $oldPlan],
            ['subscription_plan' => $validated['subscription_plan']],
            "Updated subscription for tenant: {$tenant->name} from {$oldPlan} to {$validated['subscription_plan']}"
        );

        return back()->with('success', 'Subscription plan updated successfully!');
    }

    public function extend(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'months' => 'required|integer|min:1|max:24',
        ]);

        $currentExpiry = $tenant->subscription_expires_at ?? now();
        $newExpiry = $currentExpiry->copy()->addMonths((int) $validated['months']);

        $tenant->update([
            'subscription_expires_at' => $newExpiry,
        ]);

        ActivityLog::logActivity(
            'extended_subscription',
            $tenant,
            ['subscription_expires_at' => $tenant->subscription_expires_at],
            ['subscription_expires_at' => $newExpiry],
            "Extended subscription for tenant: {$tenant->name} by {$validated['months']} months"
        );

        return back()->with('success', 'Subscription extended successfully!');
    }

    public function recordPayment(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $plan = SubscriptionPlan::where('slug', $tenant->subscription_plan)->first();

        $transaction = SubscriptionTransaction::create([
            'tenant_id' => $tenant->id,
            'subscription_plan_id' => $plan?->id,
            'amount' => $validated['amount'],
            'status' => 'completed',
            'payment_method' => $validated['payment_method'],
            'transaction_id' => $validated['transaction_id'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'transaction_date' => $validated['transaction_date'],
        ]);

        ActivityLog::logActivity(
            'recorded_payment',
            $transaction,
            null,
            $transaction->toArray(),
            "Recorded payment of \${$validated['amount']} for tenant: {$tenant->name}"
        );

        return back()->with('success', 'Payment recorded successfully!');
    }
}
