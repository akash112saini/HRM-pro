<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class TenantRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = app()->bound('tenant.id') ? app('tenant.id') : null;

        // Company Admin Role
        Role::firstOrCreate(
            ['slug' => 'company_admin'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Company Admin',
                'description' => 'Full access to all modules and settings within the company.',
                'permissions' => Role::availablePermissions(), // All permissions
                'is_system_role' => true,
                'is_active' => true,
            ]
        );

        // HR Manager Role
        Role::firstOrCreate(
            ['slug' => 'hr_manager'],
            [
                'tenant_id' => $tenantId,
                'name' => 'HR Manager',
                'description' => 'Access to manage employees, attendance, leaves, and payroll.',
                'permissions' => [
                    'view_employees',
                    'create_employees',
                    'edit_employees',
                    'delete_employees',
                    'view_attendance',
                    'manage_attendance',
                    'approve_corrections',
                    'view_leaves',
                    'approve_leaves',
                    'manage_leave_types',
                    'view_payroll',
                    'manage_payroll',
                    'generate_payroll',
                    'view_jobs',
                    'manage_jobs',
                    'view_candidates',
                    'manage_candidates',
                    'schedule_interviews',
                    'view_reports',
                    'export_reports'
                ],
                'is_system_role' => true,
                'is_active' => true,
            ]
        );

        // Employee Role
        Role::firstOrCreate(
            ['slug' => 'employee'],
            [
                'tenant_id' => $tenantId,
                'name' => 'Employee',
                'description' => 'Access to self-service portal.',
                'permissions' => [
                    'view_attendance',
                    'apply_leave',
                    'view_own_payslip',
                    'view_holidays', // Assuming this permission exists or will be added
                ],
                'is_system_role' => true,
                'is_active' => true,
            ]
        );
    }
}
