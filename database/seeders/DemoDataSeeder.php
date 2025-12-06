<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds for all tenants.
     */
    public function run(): void
    {
        // Get all tenant databases
        $tenants = DB::connection('mysql')->table('tenants')->get();

        foreach ($tenants as $tenant) {
            $tenantDb = 'hrmpro_tenant_' . $tenant->id;
            $this->command->info("Seeding demo data for tenant {$tenant->id} ({$tenant->name})...");

            $this->seedTenantData($tenantDb, $tenant->id);

            $this->command->info("✓ Completed seeding for tenant {$tenant->id}");
        }

        $this->command->info("\n🎉 All demo data seeded successfully!");
    }

    /**
     * Seed demo data for a specific tenant
     */
    private function seedTenantData(string $database, int $tenantId): void
    {
        $now = Carbon::now();

        // 1. Departments (if not exist)
        $this->seedDepartments($database, $tenantId, $now);

        // 2. Designations
        $this->seedDesignations($database, $tenantId, $now);

        // 3. Additional Shifts
        $this->seedShifts($database, $tenantId, $now);

        // 4. Holidays
        $this->seedHolidays($database, $tenantId, $now);

        // 5. Leave Types
        $this->seedLeaveTypes($database, $tenantId, $now);

        // 6. Employees
        $this->seedEmployees($database, $tenantId, $now);

        // 7. Users (linked to employees)
        $this->seedUsers($database, $tenantId, $now);

        // 8. Job Postings
        $this->seedJobPostings($database, $tenantId, $now);

        // 9. Candidates
        $this->seedCandidates($database, $tenantId, $now);

        // Skip leave requests and attendance - tables don't exist yet
        // $this->seedLeaveRequests($database, $tenantId, $now);
        // $this->seedAttendance($database, $tenantId, $now);
    }

    private function seedDepartments($db, $tenantId, $now): void
    {
        $departments = [
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT and Software Development'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'HR and Recruitment'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance and Accounting'],
            ['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Marketing and Communications'],
            ['name' => 'Sales', 'code' => 'SLS', 'description' => 'Sales and Business Development'],
        ];

        foreach ($departments as $dept) {
            $exists = DB::connection('mysql')
                ->table($db . '.departments')
                ->where('tenant_id', $tenantId)
                ->where('code', $dept['code'])
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.departments')->insert(array_merge($dept, [
                    'tenant_id' => $tenantId,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedDesignations($db, $tenantId, $now): void
    {
        // Get department IDs
        $depts = DB::connection('mysql')
            ->table($db . '.departments')
            ->where('tenant_id', $tenantId)
            ->get()
            ->keyBy('code');

        $designations = [
            ['name' => 'CEO', 'department_id' => $depts['IT']->id ?? 1, 'level' => 1],
            ['name' => 'CTO', 'department_id' => $depts['IT']->id ?? 1, 'level' => 2],
            ['name' => 'Senior Developer', 'department_id' => $depts['IT']->id ?? 1, 'level' => 3],
            ['name' => 'Junior Developer', 'department_id' => $depts['IT']->id ?? 1, 'level' => 4],
            ['name' => 'HR Manager', 'department_id' => $depts['HR']->id ?? 1, 'level' => 2],
            ['name' => 'HR Executive', 'department_id' => $depts['HR']->id ?? 1, 'level' => 3],
            ['name' => 'Finance Manager', 'department_id' => $depts['FIN']->id ?? 1, 'level' => 2],
            ['name' => 'Accountant', 'department_id' => $depts['FIN']->id ?? 1, 'level' => 3],
            ['name' => 'Marketing Manager', 'department_id' => $depts['MKT']->id ?? 1, 'level' => 2],
            ['name' => 'Sales Executive', 'department_id' => $depts['SLS']->id ?? 1, 'level' => 3],
        ];

        foreach ($designations as $desig) {
            DB::connection('mysql')->table($db . '.designations')->insert(array_merge($desig, [
                'tenant_id' => $tenantId,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function seedShifts($db, $tenantId, $now): void
    {
        // Check if shifts already exist
        $count = DB::connection('mysql')
            ->table($db . '.shifts')
            ->where('tenant_id', $tenantId)
            ->count();

        if ($count >= 3) {
            return; // Already have shifts from previous seeder
        }

        $shifts = [
            ['name' => 'General Shift', 'start_time' => '09:30:00', 'end_time' => '18:30:00'],
            ['name' => 'First Shift', 'start_time' => '06:00:00', 'end_time' => '15:00:00'],
            ['name' => 'Second Shift', 'start_time' => '15:00:00', 'end_time' => '00:00:00'],
        ];

        foreach ($shifts as $shift) {
            DB::connection('mysql')->table($db . '.shifts')->insert(array_merge($shift, [
                'tenant_id' => $tenantId,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function seedHolidays($db, $tenantId, $now): void
    {
        $holidays = [
            ['name' => 'Republic Day', 'date' => '2025-01-26', 'is_optional' => false],
            ['name' => 'Holi', 'date' => '2025-03-14', 'is_optional' => false],
            ['name' => 'Good Friday', 'date' => '2025-04-18', 'is_optional' => true],
            ['name' => 'Independence Day', 'date' => '2025-08-15', 'is_optional' => false],
            ['name' => 'Gandhi Jayanti', 'date' => '2025-10-02', 'is_optional' => false],
            ['name' => 'Diwali', 'date' => '2025-10-20', 'is_optional' => false],
            ['name' => 'Christmas', 'date' => '2025-12-25', 'is_optional' => false],
        ];

        foreach ($holidays as $holiday) {
            DB::connection('mysql')->table($db . '.holidays')->insert(array_merge($holiday, [
                'tenant_id' => $tenantId,
                'description' => $holiday['name'] . ' celebration',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    private function seedLeaveTypes($db, $tenantId, $now): void
    {
        $leaveTypes = [
            ['name' => 'Casual Leave', 'code' => 'CL', 'annual_quota' => 10, 'max_carry_forward' => 5, 'accrual_type' => 'yearly', 'color' => '#3498db'],
            ['name' => 'Sick Leave', 'code' => 'SL', 'annual_quota' => 12, 'max_carry_forward' => 0, 'accrual_type' => 'yearly', 'color' => '#e74c3c'],
            ['name' => 'Privilege Leave', 'code' => 'PL', 'annual_quota' => 20, 'max_carry_forward' => 10, 'accrual_type' => 'monthly', 'color' => '#2ecc71'],
            ['name' => 'Maternity Leave', 'code' => 'ML', 'annual_quota' => 180, 'max_carry_forward' => 0, 'accrual_type' => 'yearly', 'color' => '#9b59b6'],
            ['name' => 'Paternity Leave', 'code' => 'PAT', 'annual_quota' => 15, 'max_carry_forward' => 0, 'accrual_type' => 'yearly', 'color' => '#34495e'],
        ];

        foreach ($leaveTypes as $leaveType) {
            $exists = DB::connection('mysql')
                ->table($db . '.leave_types')
                ->where('tenant_id', $tenantId)
                ->where('code', $leaveType['code'])
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.leave_types')->insert(array_merge($leaveType, [
                    'tenant_id' => $tenantId,
                    'is_paid' => true,
                    'requires_approval' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedEmployees($db, $tenantId, $now): void
    {
        // Get first department, designation, and shift
        $dept = DB::connection('mysql')->table($db . '.departments')->where('tenant_id', $tenantId)->first();
        $desig = DB::connection('mysql')->table($db . '.designations')->where('tenant_id', $tenantId)->first();
        $shift = DB::connection('mysql')->table($db . '.shifts')->where('tenant_id', $tenantId)->first();

        if (!$dept || !$desig || !$shift) {
            $this->command->warn("Skipping employees - missing dependencies");
            return;
        }

        $employees = [
            ['employee_code' => 'EMP002', 'first_name' => 'Alice', 'last_name' => 'Smith', 'email' => 'alice.smith@example.com', 'phone' => '9876543210', 'gender' => 'female', 'date_of_birth' => '1992-05-15', 'joining_date' => '2023-01-15'],
            ['employee_code' => 'EMP003', 'first_name' => 'Bob', 'last_name' => 'Johnson', 'email' => 'bob.johnson@example.com', 'phone' => '9876543211', 'gender' => 'male', 'date_of_birth' => '1988-08-20', 'joining_date' => '2022-06-01'],
            ['employee_code' => 'EMP004', 'first_name' => 'Carol', 'last_name' => 'Williams', 'email' => 'carol.w@example.com', 'phone' => '9876543212', 'gender' => 'female', 'date_of_birth' => '1995-03-10', 'joining_date' => '2024-02-01'],
            ['employee_code' => 'EMP005', 'first_name' => 'David', 'last_name' => 'Brown', 'email' => 'david.brown@example.com', 'phone' => '9876543213', 'gender' => 'male', 'date_of_birth' => '1990-11-25', 'joining_date' => '2023-09-15'],
        ];

        foreach ($employees as $emp) {
            $exists = DB::connection('mysql')
                ->table($db . '.employees')
                ->where('tenant_id', $tenantId)
                ->where('employee_code', $emp['employee_code'])
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.employees')->insert(array_merge($emp, [
                    'tenant_id' => $tenantId,
                    'department_id' => $dept->id,
                    'designation_id' => $desig->id,
                    'shift_id' => $shift->id,
                    'employment_status' => 'confirmed',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedUsers($db, $tenantId, $now): void
    {
        // Create users linked to employees
        $employees = DB::connection('mysql')
            ->table($db . '.employees')
            ->where('tenant_id', $tenantId)
            ->get();

        $roles = ['manager', 'employee', 'employee', 'employee'];

        foreach ($employees as $index => $emp) {
            $exists = DB::connection('mysql')
                ->table($db . '.users')
                ->where('tenant_id', $tenantId)
                ->where('email', $emp->email)
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.users')->insert([
                    'tenant_id' => $tenantId,
                    'name' => $emp->first_name . ' ' . $emp->last_name,
                    'email' => $emp->email,
                    'password' => Hash::make('password123'),
                    'role' => $roles[$index] ?? 'employee',
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedJobPostings($db, $tenantId, $now): void
    {
        $dept = DB::connection('mysql')->table($db . '.departments')->where('tenant_id', $tenantId)->first();
        $desig = DB::connection('mysql')->table($db . '.designations')->where('tenant_id', $tenantId)->first();

        if (!$dept || !$desig)
            return;

        $jobs = [
            ['title' => 'Senior Software Engineer', 'location' => 'Remote', 'employment_type' => 'full_time', 'experience_required' => '5+ years', 'description' => 'Looking for experienced software engineer', 'requirements' => 'Strong programming skills required', 'status' => 'active'],
            ['title' => 'Product Manager', 'location' => 'Hybrid', 'employment_type' => 'full_time', 'experience_required' => '3+ years', 'description' => 'Product management role', 'requirements' => 'PM experience required', 'status' => 'active'],
            ['title' => 'UI/UX Designer', 'location' => 'On-site', 'employment_type' => 'contract', 'experience_required' => '2+ years', 'description' => 'Creative designer needed', 'requirements' => 'Design portfolio required', 'status' => 'active'],
        ];

        foreach ($jobs as $job) {
            $exists = DB::connection('mysql')
                ->table($db . '.job_postings')
                ->where('tenant_id', $tenantId)
                ->where('title', $job['title'])
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.job_postings')->insert(array_merge($job, [
                    'tenant_id' => $tenantId,
                    'department_id' => $dept->id,
                    'designation_id' => $desig->id,
                    'posted_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedCandidates($db, $tenantId, $now): void
    {
        $job = DB::connection('mysql')->table($db . '.job_postings')->where('tenant_id', $tenantId)->first();

        if (!$job)
            return;

        $candidates = [
            ['name' => 'Emma Davis', 'email' => 'emma.d@example.com', 'phone' => '9998887776', 'current_stage' => 'screening'],
            ['name' => 'James Wilson', 'email' => 'james.w@example.com', 'phone' => '9998887777', 'current_stage' => 'interview'],
            ['name' => 'Sophia Martinez', 'email' => 'sophia.m@example.com', 'phone' => '9998887778', 'current_stage' => 'offer'],
        ];

        foreach ($candidates as $candidate) {
            $exists = DB::connection('mysql')
                ->table($db . '.candidates')
                ->where('tenant_id', $tenantId)
                ->where('email', $candidate['email'])
                ->exists();

            if (!$exists) {
                DB::connection('mysql')->table($db . '.candidates')->insert(array_merge($candidate, [
                    'tenant_id' => $tenantId,
                    'job_posting_id' => $job->id,
                    'applied_at' => $now->copy()->subDays(rand(5, 30)),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    private function seedLeaveRequests($db, $tenantId, $now): void
    {
        $employees = DB::connection('mysql')->table($db . '.employees')->where('tenant_id', $tenantId)->limit(2)->get();
        $leaveType = DB::connection('mysql')->table($db . '.leave_types')->where('tenant_id', $tenantId)->first();

        if ($employees->isEmpty() || !$leaveType)
            return;

        foreach ($employees as $emp) {
            DB::connection('mysql')->table($db . '.leaves')->insert([
                'tenant_id' => $tenantId,
                'employee_id' => $emp->id,
                'leave_type_id' => $leaveType->id,
                'start_date' => $now->copy()->addDays(10),
                'end_date' => $now->copy()->addDays(12),
                'days' => 3,
                'reason' => 'Personal work',
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedAttendance($db, $tenantId, $now): void
    {
        $employees = DB::connection('mysql')->table($db . '.employees')->where('tenant_id', $tenantId)->get();

        if ($employees->isEmpty())
            return;

        // Create attendance for last 7 days
        for ($i = 7; $i >= 1; $i--) {
            $date = $now->copy()->subDays($i);

            foreach ($employees as $emp) {
                // Skip weekends
                if ($date->dayOfWeek == 0 || $date->dayOfWeek == 6)
                    continue;

                DB::connection('mysql')->table($db . '.attendance')->insert([
                    'tenant_id' => $tenantId,
                    'employee_id' => $emp->id,
                    'date' => $date->format('Y-m-d'),
                    'check_in' => $date->copy()->setTime(9, rand(0, 30)),
                    'check_out' => $date->copy()->setTime(18, rand(0, 30)),
                    'status' => 'present',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
