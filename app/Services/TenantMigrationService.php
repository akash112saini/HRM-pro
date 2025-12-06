<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;

class TenantMigrationService
{
    /**
     * Get the database name for a tenant.
     */
    protected function getDatabaseName(Tenant $tenant)
    {
        $prefix = env('TENANT_DB_PREFIX', '');
        return $prefix . 'hrmpro_tenant_' . $tenant->id;
    }

    /**
     * Create a new database for the tenant.
     */
    public function createDatabase(Tenant $tenant)
    {
        $databaseName = $this->getDatabaseName($tenant);

        try {
            // Use the default connection (e.g., mysql) to create the new database
            DB::connection('mysql')->statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch (\Exception $e) {
            // Log warning but proceed, assuming DB might be created manually
            \Illuminate\Support\Facades\Log::warning("Could not create database {$databaseName}: " . $e->getMessage());
        }

        return $databaseName;
    }

    /**
     * Run migrations for a specific tenant.
     */
    public function migrate(Tenant $tenant, $fresh = false)
    {
        $databaseName = $this->getDatabaseName($tenant);

        // Set the tenant database connection dynamically
        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        $command = $fresh ? 'migrate:fresh' : 'migrate';

        // Run migrations on the tenant connection
        Artisan::call($command, [
            '--database' => 'tenant',
            '--path' => 'database/migrations/tenant', // We will move tenant-specific migrations here
            '--force' => true,
        ]);

        return Artisan::output();
    }

    /**
     * Seed the tenant database.
     */
    public function seed(Tenant $tenant, $class = 'DatabaseSeeder')
    {
        $databaseName = $this->getDatabaseName($tenant);

        Config::set('database.connections.tenant.database', $databaseName);
        DB::purge('tenant');
        DB::reconnect('tenant');

        app()->instance('tenant.id', $tenant->id); // Bind tenant ID for seeders/models

        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--class' => $class,
            '--force' => true,
        ]);

        return Artisan::output();
    }
}
