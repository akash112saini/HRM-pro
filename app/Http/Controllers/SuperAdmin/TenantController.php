<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Services\TenantMigrationService;

class TenantController extends Controller
{
    protected $migrationService;

    public function __construct(TenantMigrationService $migrationService)
    {
        $this->migrationService = $migrationService;
    }
    public function index(Request $request)
    {
        $query = Tenant::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        $tenants = $query->withCount('users')
            ->latest()
            ->paginate(15);

        // Manually count employees for each tenant (cross-database)
        foreach ($tenants as $tenant) {
            try {
                $tenant->employees_count = $tenant->runOnTenant(function () {
                    return \App\Models\Employee::count();
                });
            } catch (\Exception $e) {
                $tenant->employees_count = 0; // Fallback if DB not found or error
            }
        }

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('super-admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug|alpha_dash',
            'contact_email' => 'required|email|unique:tenants,contact_email',
            'contact_phone' => 'nullable|string|max:20',
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise',
            'subscription_expires_at' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',

            // Admin user details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'] ?? null,
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_expires_at' => $validated['subscription_expires_at'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'is_active' => true,
        ]);

        // Create admin user
        $adminUser = User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'role' => 'company_admin',
            'is_active' => true,
        ]);

        // Create tenant database and run migrations
        try {
            $this->migrationService->createDatabase($tenant);
            $this->migrationService->migrate($tenant);
            $this->migrationService->seed($tenant, 'TenantDatabaseSeeder');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to setup database for tenant {$tenant->name}: " . $e->getMessage());
        }

        // Log activity
        ActivityLog::logActivity(
            'created_tenant',
            $tenant,
            null,
            $tenant->toArray(),
            "Created tenant: {$tenant->name} with admin user: {$adminUser->email}"
        );

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant created successfully!');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['users']); // Users are in landlord DB

        // Load tenant-specific relationships
        try {
            $tenant->runOnTenant(function () use ($tenant) {
                $tenant->load(['departments']);
                $tenant->employees_count = \App\Models\Employee::count();
            });
        } catch (\Exception $e) {
            $tenant->employees_count = 0;
            // Departments will remain unloaded or empty collection if failed
        }

        // Get recent activity for this tenant
        $recentActivity = ActivityLog::where('tenant_id', $tenant->id)
            ->latest()
            ->take(10)
            ->get();

        return view('super-admin.tenants.show', compact('tenant', 'recentActivity'));
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('tenants')->ignore($tenant->id)],
            'contact_email' => ['required', 'email', Rule::unique('tenants')->ignore($tenant->id)],
            'contact_phone' => 'nullable|string|max:20',
            'subscription_plan' => 'required|in:trial,basic,premium,enterprise',
            'subscription_expires_at' => 'nullable|date',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        $oldValues = $tenant->toArray();
        $tenant->update($validated);

        // Log activity
        ActivityLog::logActivity(
            'updated_tenant',
            $tenant,
            $oldValues,
            $tenant->toArray(),
            "Updated tenant: {$tenant->name}"
        );

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    public function destroy(Tenant $tenant)
    {
        // Soft delete by deactivating
        $tenant->update(['is_active' => false]);

        // Log activity
        ActivityLog::logActivity(
            'deactivated_tenant',
            $tenant,
            ['is_active' => true],
            ['is_active' => false],
            "Deactivated tenant: {$tenant->name}"
        );

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant deactivated successfully!');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $oldStatus = $tenant->is_active;
        $tenant->update(['is_active' => !$oldStatus]);

        // Log activity
        ActivityLog::logActivity(
            $tenant->is_active ? 'activated_tenant' : 'deactivated_tenant',
            $tenant,
            ['is_active' => $oldStatus],
            ['is_active' => $tenant->is_active],
            ($tenant->is_active ? 'Activated' : 'Deactivated') . " tenant: {$tenant->name}"
        );

        return back()->with('success', 'Tenant status updated successfully!');
    }
    public function forceDelete(Tenant $tenant)
    {
        // Get the database name before deleting the tenant
        $databaseName = env('TENANT_DB_PREFIX', '') . 'hrmpro_tenant_' . $tenant->id;

        // Delete the admin user associated with this tenant
        User::where('tenant_id', $tenant->id)->delete();

        // Delete the tenant record
        $tenant->delete();

        // Log activity
        ActivityLog::logActivity(
            'deleted_tenant',
            null, // Tenant is gone
            $tenant->toArray(),
            null,
            "Permanently deleted tenant: {$tenant->name}"
        );

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant deleted successfully! Please manually drop the database: ' . $databaseName);
    }
}
