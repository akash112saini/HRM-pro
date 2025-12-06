<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenant databases
        $landlordDb = env('DB_DATABASE', 'hrmpro_landlord');
        $tenants = DB::connection('mysql')->table('tenants')->get();

        foreach ($tenants as $tenant) {
            $tenantDb = 'hrmpro_tenant_' . $tenant->id;
            $this->seedRolesForTenant($tenantDb, $tenant->id);
        }

        $this->command->info('Default roles seeded for all tenants.');
    }

    /**
     * Seed default roles for a specific tenant database
     */
    private function seedRolesForTenant($database, $tenantId)
    {
        $now = now();

        // Check if roles already exist
        $existingRoles = DB::connection('mysql')
            ->table($database . '.roles')
            ->where('tenant_id', $tenantId)
            ->where('is_system_role', true)
            ->count();

        if ($existingRoles > 0) {
            $this->command->info("Roles already exist for tenant {$tenantId}, skipping...");
            return;
        }

        $allPermissions = $this->getAllPermissions();

        // Company Admin Role
        DB::connection('mysql')->table($database . '.roles')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Company Admin',
            'slug' => 'company_admin',
            'description' => 'Full access to all modules and settings within the company.',
            'permissions' => json_encode($allPermissions),
            'is_system_role' => true,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Manager Role
        DB::connection('mysql')->table($database . '.roles')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Manager',
            'slug' => 'manager',
            'description' => 'Access to manage their team members and view reports.',
            'permissions' => json_encode([
                'view_employees',
                'view_attendance',
                'approve_corrections',
                'view_leaves',
                'approve_leaves',
                'view_payroll',
                'view_own_payslip',
                'view_appraisals',
                'conduct_appraisals',
                'manage_goals',
                'view_reports',
            ]),
            'is_system_role' => true,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Employee Role
        DB::connection('mysql')->table($database . '.roles')->insert([
            'tenant_id' => $tenantId,
            'name' => 'Employee',
            'slug' => 'employee',
            'description' => 'Access to self-service portal.',
            'permissions' => json_encode([
                'view_attendance',
                'apply_leave',
                'view_own_payslip',
            ]),
            'is_system_role' => true,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->command->info("Seeded 3 default roles for tenant {$tenantId}");
    }

    /**
     * Get all available permissions
     */
    private function getAllPermissions(): array
    {
        return [
            // Employees
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            // Attendance
            'view_attendance',
            'manage_attendance',
            'approve_corrections',
            // Leaves
            'view_leaves',
            'apply_leave',
            'approve_leaves',
            'manage_leave_types',
            // Payroll
            'view_payroll',
            'manage_payroll',
            'view_own_payslip',
            'generate_payroll',
            // Recruitment
            'view_jobs',
            'manage_jobs',
            'view_candidates',
            'manage_candidates',
            'schedule_interviews',
            // Performance
            'view_appraisals',
            'conduct_appraisals',
            'manage_goals',
            // Assets
            'view_assets',
            'manage_assets',
            'assign_assets',
            // Settings
            'manage_departments',
            'manage_designations',
            'manage_shifts',
            'manage_salary_structures',
            'manage_tax_slabs',
            'manage_holidays',
            'manage_users',
            'manage_roles',
            // Reports
            'view_reports',
            'export_reports',
        ];
    }
}
