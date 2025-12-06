<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

echo "Checking Super Admin...\n";
$user = User::where('email', 'superadmin@hrm-pro.com')->first();

if ($user) {
    echo "User found. ID: " . $user->id . "\n";
    echo "Role: " . $user->role . "\n";
    if (Hash::check('password', $user->password)) {
        echo "Password matches.\n";
    } else {
        echo "Password does NOT match.\n";
    }
} else {
    echo "User NOT found.\n";
}

echo "\nChecking Environment...\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "TENANT_DB_PREFIX (env): " . env('TENANT_DB_PREFIX') . "\n";
echo "TENANT_DB_PREFIX (config): " . config('database.connections.tenant.database') . "\n"; // This won't show prefix directly but checking config
