<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

echo "--- Tenant Information ---\n";
$tenants = Tenant::all();
foreach ($tenants as $tenant) {
    echo "ID: " . $tenant->id . "\n";
    echo "Name: " . $tenant->company_name . "\n";
    echo "Domain: " . $tenant->domain . "\n";
    echo "Subdomain: " . $tenant->slug . "\n";
    echo "Database Name (Expected): dmrhospi_hrmpro_tenant_" . $tenant->id . "\n";
    echo "------------------------\n";
}

echo "\n--- Database Check ---\n";
$dbs = DB::select('SHOW DATABASES LIKE "dmrhospi_hrmpro_tenant%"');
foreach ($dbs as $db) {
    echo "Found Database: " . $db->{'Database (dmrhospi_hrmpro_tenant%)'} . "\n";
}
