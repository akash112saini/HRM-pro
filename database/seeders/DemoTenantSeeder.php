<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoTenantSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Super Admin (Global User)
        User::withoutGlobalScopes()->updateOrCreate(
            ['email' => 'superadmin@hrm-pro.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'tenant_id' => null,
            ]
        );

        // 2. Create Demo Tenant
        $tenant = Tenant::firstOrCreate(
            ['domain' => 'demo.hrm-pro.test'],
            [
                'name' => 'Demo Corp',
                'slug' => 'demo-corp',
                'subscription_plan' => 'enterprise',
                'is_active' => true,
            ]
        );

        // Set tenant context
        app()->instance('tenant.id', $tenant->id);

        // 2. Create Organization Structure
        $dept = Department::firstOrCreate(['name' => 'IT', 'tenant_id' => $tenant->id]);
                'employee_code' => 'EMP001',
                'first_name' => 'Demo',
                'last_name' => 'Employee',
                'email' => 'employee@demo.com',
                'department_id' => $dept->id,
                'designation_id' => $devDesig->id,
                'shift_id' => $shift->id,
                'joining_date' => now(),
            ]
        );
    }
}
