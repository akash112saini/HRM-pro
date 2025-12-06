<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SetTenantDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply for authenticated users who are NOT super admins
        if ($user && $user->role !== 'super_admin' && $user->tenant_id) {
            $tenantId = $user->tenant_id;
            $prefix = env('TENANT_DB_PREFIX', '');
            $databaseName = $prefix . 'hrmpro_tenant_' . $tenantId;

            // Set the tenant database connection dynamically
            Config::set('database.connections.tenant.database', $databaseName);

            // Purge the tenant connection to ensure a fresh connection is made
            DB::purge('tenant');

            // Reconnect to the tenant database
            DB::reconnect('tenant');

            // Set the default connection to tenant
            DB::setDefaultConnection('tenant');
        }

        return $next($request);
    }
}
