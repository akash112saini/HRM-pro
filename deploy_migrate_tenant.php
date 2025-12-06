<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

$tenantId = 5;
$tenant = Tenant::find($tenantId);

if (!$tenant) {
    die("Tenant not found.\n");
}

echo "Migrating for Tenant: " . $tenant->company_name . " (ID: $tenantId)\n";

// Set DB Connection
$prefix = env('TENANT_DB_PREFIX', '');
$databaseName = $prefix . 'hrmpro_tenant_' . $tenantId;

Config::set('database.connections.tenant.database', $databaseName);
DB::purge('tenant');
DB::reconnect('tenant');
DB::setDefaultConnection('tenant');

echo "Connected to: " . DB::connection('tenant')->getDatabaseName() . "\n";

// Run Migrations
Artisan::call('migrate:fresh', [
    '--path' => 'database/migrations',
    '--database' => 'tenant',
    '--force' => true,
]);

echo Artisan::output();

echo "Running Tenant Specific Migrations...\n";
Artisan::call('migrate', [
    '--path' => 'database/migrations/tenant',
    '--database' => 'tenant',
    '--force' => true,
]);

echo Artisan::output();
