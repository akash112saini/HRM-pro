<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tenant;

// Setup
$tenant = Tenant::find(1); // Demo Corp
$admin = User::where('email', 'admin@demo.com')->first();
$manager = User::where('email', 'manager@demo.com')->first();
$employee = User::where('email', 'john.doe@demo.com')->first();
$superAdmin = User::where('role', 'super_admin')->first();

if (!$tenant || !$admin) {
    echo "Error: Missing test data (Tenant 1 or admin@demo.com)\n";
    exit(1);
}

// Helper to test routes
function testRoutes($user, $roleName, $tenantId = null)
{
    echo "\n--- Testing Routes for Role: $roleName ---\n";

    if ($user) {
        Auth::login($user);
        // Set tenant context if needed
        if ($tenantId) {
            // Simulate middleware
            config(['database.connections.tenant.database' => 'hrmpro_tenant_' . $tenantId]);
            DB::purge('tenant');
            DB::reconnect('tenant');
            DB::setDefaultConnection('tenant');
        }
    } else {
        echo "Skipping: User not found.\n";
        return;
    }

    $routes = Route::getRoutes();
    $count = 0;
    $errors = 0;

    foreach ($routes as $route) {
        if (!in_array('GET', $route->methods))
            continue;

        $uri = $route->uri();
        $name = $route->getName();

        // Filter routes based on role prefix to avoid 403s
        if ($roleName === 'Super Admin' && !str_starts_with($uri, 'super-admin'))
            continue;
        if ($roleName === 'Company Admin' && !str_starts_with($uri, 'admin'))
            continue;
        if ($roleName === 'Manager' && !str_starts_with($uri, 'manager'))
            continue;
        if ($roleName === 'Employee' && !str_starts_with($uri, 'employee'))
            continue;

        // Skip parameterized routes for now (too complex to mock all params automatically)
        if (str_contains($uri, '{'))
            continue;

        try {
            // We can't easily "get" the route in Tinker without making a real HTTP request,
            // but we can check if the controller class exists and method is callable.
            // A better approach for Tinker is to just instantiate the controller? 
            // No, that misses middleware.

            // Let's just print the routes we WOULD test.
            // Actually, we can use Laravel's test helpers if we were in a test file, but here we are in a script.
            // Let's try to resolve the controller.

            $action = $route->getAction();
            if (isset($action['controller'])) {
                $controllerAction = $action['controller'];
                list($controller, $method) = explode('@', $controllerAction);

                if (!class_exists($controller)) {
                    echo "[FAIL] Controller missing: $controller (Route: $uri)\n";
                    $errors++;
                } else {
                    // echo "[PASS] Controller exists: $controller\n";
                }
            }
            $count++;
        } catch (\Exception $e) {
            echo "[FAIL] Error checking route $uri: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    echo "Checked $count routes. Found $errors errors.\n";
}

// Run Tests
testRoutes($superAdmin, 'Super Admin');
testRoutes($admin, 'Company Admin', $tenant->id);
testRoutes($manager, 'Manager', $tenant->id);
testRoutes($employee, 'Employee', $tenant->id);
