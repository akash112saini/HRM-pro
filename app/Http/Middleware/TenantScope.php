<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->identifyTenant($request);

        if (!$tenant) {
            // Localhost Demo Bypass
            if ($request->getHost() === '127.0.0.1' || $request->getHost() === 'localhost') {
                $demoTenant = \App\Models\Tenant::where('slug', 'demo-corp')->first();
                if ($demoTenant) {
                    app()->instance('tenant', $demoTenant);
                    app()->instance('tenant.id', $demoTenant->id);
                    $this->configureTenant($demoTenant);
                    return $next($request);
                }
            }

            // Allow Super Admin to bypass tenant check for non-admin routes
            if (auth()->check() && auth()->user()->isSuperAdmin()) {
                // If accessing /admin routes, use demo tenant for data access
                if ($request->is('admin/*')) {
                    $demoTenant = \App\Models\Tenant::where('slug', 'demo-corp')->first();
                    if ($demoTenant) {
                        app()->instance('tenant', $demoTenant);
                        app()->instance('tenant.id', $demoTenant->id);
                        $this->configureTenant($demoTenant);
                    }
                } else {
                    app()->instance('tenant', null);
                    app()->instance('tenant.id', null);
                }
                return $next($request);
            }

            abort(404, 'Tenant not found');
        }

        // Check if tenant is active
        if (!$tenant->is_active) {
            abort(403, 'Tenant account is inactive');
        }

        // Check subscription expiry
        if ($tenant->subscription_expires_at && $tenant->subscription_expires_at->isPast()) {
            abort(403, 'Subscription has expired');
        }

        // Bind tenant to the application container
        app()->instance('tenant', $tenant);
        app()->instance('tenant.id', $tenant->id);

        // Set tenant-specific configurations
        $this->configureTenant($tenant);

        return $next($request);
    }

    /**
     * Identify the tenant from the request.
     */
    protected function identifyTenant(Request $request): ?Tenant
    {
        $identification = config('tenant.identification', 'subdomain');

        return match ($identification) {
            'subdomain' => $this->identifyBySubdomain($request),
            'domain' => $this->identifyByDomain($request),
            'header' => $this->identifyByHeader($request),
            'path' => $this->identifyByPath($request),
            default => null,
        };
    }

    /**
     * Identify tenant by subdomain.
     */
    protected function identifyBySubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $centralDomains = config('tenant.central_domains', []);

        // Check if it's a central domain
        if (in_array($host, $centralDomains)) {
            return null;
        }

        // Extract subdomain
        $parts = explode('.', $host);

        if (count($parts) < 2) {
            return null;
        }

        $subdomain = $parts[0];

        return Tenant::where('slug', $subdomain)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Identify tenant by custom domain.
     */
    protected function identifyByDomain(Request $request): ?Tenant
    {
        $host = $request->getHost();

        return Tenant::where('domain', $host)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Identify tenant by request header.
     */
    protected function identifyByHeader(Request $request): ?Tenant
    {
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            return null;
        }

        return Tenant::where('id', $tenantId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Identify tenant by path segment.
     */
    protected function identifyByPath(Request $request): ?Tenant
    {
        $slug = $request->segment(1);

        if (!$slug) {
            return null;
        }

        return Tenant::where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Configure tenant-specific settings.
     */
    protected function configureTenant(Tenant $tenant): void
    {
        // Set tenant database connection
        $tenantId = $tenant->id;
        $prefix = env('TENANT_DB_PREFIX', '');
        $databaseName = $prefix . 'hrmpro_tenant_' . $tenantId;

        // Set the tenant database connection dynamically
        config(['database.connections.tenant.database' => $databaseName]);

        // Purge the tenant connection to ensure a fresh connection is made
        \Illuminate\Support\Facades\DB::purge('tenant');

        // Reconnect to the tenant database
        \Illuminate\Support\Facades\DB::reconnect('tenant');

        // Set tenant-specific cache prefix
        if (config('tenant.features.isolated_cache')) {
            config(['cache.prefix' => 'tenant_' . $tenant->id . '_']);
        }

        // Set tenant-specific filesystem disk
        if (config('tenant.features.isolated_filesystem')) {
            config([
                'filesystems.disks.tenant' => [
                    'driver' => 'local',
                    'root' => storage_path('app/tenants/' . $tenant->id),
                    'visibility' => 'private',
                ],
            ]);
        }

        // Apply tenant settings (timezone, currency, etc.)
        if ($tenant->settings) {
            $settings = is_string($tenant->settings)
                ? json_decode($tenant->settings, true)
                : $tenant->settings;

            if (isset($settings['timezone'])) {
                config(['app.timezone' => $settings['timezone']]);
                date_default_timezone_set($settings['timezone']);
            }

            if (isset($settings['currency'])) {
                config(['app.currency' => $settings['currency']]);
            }
        }
    }
}
