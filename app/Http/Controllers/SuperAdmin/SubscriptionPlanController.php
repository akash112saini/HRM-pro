<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('sort_order')->get();
        return view('super-admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.subscription-plans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_plans,slug|alpha_dash',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_employees' => 'nullable|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $plan = SubscriptionPlan::create($validated);

        ActivityLog::logActivity(
            'created_subscription_plan',
            $plan,
            null,
            $plan->toArray(),
            "Created subscription plan: {$plan->name}"
        );

        return redirect()->route('super-admin.subscription-plans.index')
            ->with('success', 'Subscription plan created successfully!');
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('super-admin.subscription-plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('subscription_plans')->ignore($subscriptionPlan->id)],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features' => 'nullable|array',
            'max_employees' => 'nullable|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $oldValues = $subscriptionPlan->toArray();
        $subscriptionPlan->update($validated);

        ActivityLog::logActivity(
            'updated_subscription_plan',
            $subscriptionPlan,
            $oldValues,
            $subscriptionPlan->toArray(),
            "Updated subscription plan: {$subscriptionPlan->name}"
        );

        return redirect()->route('super-admin.subscription-plans.index')
            ->with('success', 'Subscription plan updated successfully!');
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->delete();

        ActivityLog::logActivity(
            'deleted_subscription_plan',
            null,
            $subscriptionPlan->toArray(),
            null,
            "Deleted subscription plan: {$subscriptionPlan->name}"
        );

        return redirect()->route('super-admin.subscription-plans.index')
            ->with('success', 'Subscription plan deleted successfully!');
    }
}
