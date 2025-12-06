<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Shift;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Holiday;
use App\Models\TaxSlab;
use App\Models\SalaryStructure;
use App\Models\Asset;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FullDemoSeeder extends Seeder
{
    public function run()
    {
        // 1. Setup Tenant
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

        // 2. Organization Structure
        $depts = [
            'Engineering' => Department::firstOrCreate(['name' => 'Engineering', 'tenant_id' => $tenant->id]),
            'HR' => Department::firstOrCreate(['name' => 'HR', 'tenant_id' => $tenant->id]),
            'Sales' => Department::firstOrCreate(['name' => 'Sales', 'tenant_id' => $tenant->id]),
        ];

        $desigs = [
            'Manager' => Designation::firstOrCreate(['name' => 'Manager', 'tenant_id' => $tenant->id], ['department_id' => $depts['Engineering']->id, 'level' => 1]),
            'Senior Dev' => Designation::firstOrCreate(['name' => 'Senior Developer', 'tenant_id' => $tenant->id], ['department_id' => $depts['Engineering']->id, 'level' => 2]),
            'Junior Dev' => Designation::firstOrCreate(['name' => 'Junior Developer', 'tenant_id' => $tenant->id], ['department_id' => $depts['Engineering']->id, 'level' => 3]),
            'HR Exec' => Designation::firstOrCreate(['name' => 'HR Executive', 'tenant_id' => $tenant->id], ['department_id' => $depts['HR']->id, 'level' => 3]),
            'Sales Rep' => Designation::firstOrCreate(['name' => 'Sales Representative', 'tenant_id' => $tenant->id], ['department_id' => $depts['Sales']->id, 'level' => 3]),
        ];

        $shifts = [
            'Morning' => Shift::firstOrCreate(['name' => 'Morning Shift', 'tenant_id' => $tenant->id], [
                'start_time' => '06:00:00',
                'end_time' => '15:00:00',
                'working_days' => [1, 2, 3, 4, 5, 6]
            ]),
            'General' => Shift::firstOrCreate(['name' => 'General Shift', 'tenant_id' => $tenant->id], [
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'working_days' => [1, 2, 3, 4, 5]
            ]),
            'Night' => Shift::firstOrCreate(['name' => 'Night Shift', 'tenant_id' => $tenant->id], [
                'start_time' => '18:00:00',
                'end_time' => '03:00:00',
                'working_days' => [1, 2, 3, 4, 5]
            ]),
        ];

        // 3. Settings
        $leaveTypes = [
            'Sick' => LeaveType::updateOrCreate(['code' => 'SL', 'tenant_id' => $tenant->id], [
                'name' => 'Sick Leave',
                'annual_quota' => 10,
                'accrual_type' => 'yearly',
                'color' => '#EF4444',
                'is_paid' => true
            ]),
            'Casual' => LeaveType::updateOrCreate(['code' => 'CL', 'tenant_id' => $tenant->id], [
                'name' => 'Casual Leave',
                'annual_quota' => 12,
                'accrual_type' => 'monthly',
                'color' => '#3B82F6',
                'is_paid' => true
            ]),
            'Earned' => LeaveType::updateOrCreate(['code' => 'EL', 'tenant_id' => $tenant->id], [
                'name' => 'Earned Leave',
                'annual_quota' => 15,
                'accrual_type' => 'yearly',
                'color' => '#10B981',
                'is_paid' => true
            ]),
        ];

        // Holidays (Last 2 months + future)
        $dates = [
            now()->subMonth()->startOfMonth()->addDays(5), // Past holiday
            now()->addMonth()->startOfMonth()->addDays(10), // Future holiday
        ];
        foreach ($dates as $date) {
            Holiday::updateOrCreate(['date' => $date->format('Y-m-d'), 'tenant_id' => $tenant->id], [
                'name' => 'Public Holiday ' . $date->format('M'),
                'is_optional' => false,
            ]);
        }

        // Tax Slabs (New Regime FY 2024-25)
        TaxSlab::updateOrCreate(['min_amount' => 0, 'financial_year' => '2024-2025', 'tenant_id' => $tenant->id], [
            'max_amount' => 300000,
            'tax_rate' => 0
        ]);
        TaxSlab::updateOrCreate(['min_amount' => 300001, 'financial_year' => '2024-2025', 'tenant_id' => $tenant->id], [
            'max_amount' => 600000,
            'tax_rate' => 5
        ]);
        TaxSlab::updateOrCreate(['min_amount' => 600001, 'financial_year' => '2024-2025', 'tenant_id' => $tenant->id], [
            'max_amount' => 900000,
            'tax_rate' => 10
        ]);

        // Salary Structures
        $structures = [
            'Junior' => SalaryStructure::updateOrCreate(['name' => 'Junior Structure', 'tenant_id' => $tenant->id], [
                'components' => [
                    ['name' => 'Basic', 'type' => 'fixed', 'value' => 25000],
                    ['name' => 'HRA', 'type' => 'percentage', 'value' => 40], // 40% of Basic
                    ['name' => 'Special Allowance', 'type' => 'fixed', 'value' => 5000],
                ],
                'is_active' => true
            ]),
            'Senior' => SalaryStructure::updateOrCreate(['name' => 'Senior Structure', 'tenant_id' => $tenant->id], [
                'components' => [
                    ['name' => 'Basic', 'type' => 'fixed', 'value' => 50000],
                    ['name' => 'HRA', 'type' => 'percentage', 'value' => 40],
                    ['name' => 'Special Allowance', 'type' => 'fixed', 'value' => 15000],
                ],
                'is_active' => true
            ]),
        ];

        // Assets
        $assets = [
            Asset::updateOrCreate(['serial_number' => 'LAP001', 'tenant_id' => $tenant->id], [
                'asset_name' => 'MacBook Air M1',
                'asset_type' => 'Laptop',
                'purchase_price' => 85000,
                'status' => 'available',
                'brand' => 'Apple',
                'model' => 'M1',
                'purchase_date' => now()->subYear(),
                'asset_tag' => 'TAG-LAP001'
            ]),
            Asset::updateOrCreate(['serial_number' => 'PHN001', 'tenant_id' => $tenant->id], [
                'asset_name' => 'iPhone 13',
                'asset_type' => 'Phone',
                'purchase_price' => 50000,
                'status' => 'available',
                'brand' => 'Apple',
                'model' => '13',
                'purchase_date' => now()->subYear(),
                'asset_tag' => 'TAG-PHN001'
            ]),
        ];

        // 4. Employees
        $employees = [];
        $profiles = [
            ['John Doe', 'Engineering', 'Manager', 'General', 'Senior'],
            ['Jane Smith', 'Engineering', 'Senior Dev', 'Morning', 'Senior'],
            ['Bob Wilson', 'Engineering', 'Junior Dev', 'General', 'Junior'],
            ['Alice Brown', 'HR', 'HR Exec', 'General', 'Junior'],
            ['Charlie Davis', 'Sales', 'Sales Rep', 'General', 'Junior'],
            ['Eva Green', 'Engineering', 'Senior Dev', 'Night', 'Senior'],
            ['Frank White', 'Sales', 'Sales Rep', 'General', 'Junior'],
            ['Grace Lee', 'HR', 'HR Exec', 'Morning', 'Junior'],
            ['Henry Ford', 'Engineering', 'Junior Dev', 'Night', 'Junior'],
            ['Ivy Chen', 'Sales', 'Manager', 'General', 'Senior'],
        ];

        foreach ($profiles as $index => $p) {
            $email = strtolower(str_replace(' ', '.', $p[0])) . '@demo.com';
            $empCode = 'EMP' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            $emp = Employee::updateOrCreate(['employee_code' => $empCode, 'tenant_id' => $tenant->id], [
                'email' => $email,
                'first_name' => explode(' ', $p[0])[0],
                'last_name' => explode(' ', $p[0])[1],
                'department_id' => $depts[$p[1]]->id,
                'designation_id' => $desigs[$p[2]]->id,
                'shift_id' => $shifts[$p[3]]->id,
                'joining_date' => now()->subMonths(3),
                'employment_status' => 'confirmed',
            ]);
            $employees[] = $emp;

            // Create User Login
            $user = User::updateOrCreate(['email' => $emp->email], [
                'name' => $p[0],
                'password' => Hash::make('password'),
                'role' => 'employee',
                'tenant_id' => $tenant->id,
            ]);

            // Link User to Employee
            $emp->update(['user_id' => $user->id]);

            // Assign Salary Structure
            $structure = $structures[$p[4]];
            $basic = collect($structure->components)->where('name', 'Basic')->first()['value'] ?? 0;
            $hra = collect($structure->components)->where('name', 'HRA')->first();
            $hraVal = $hra['type'] === 'fixed' ? $hra['value'] : ($basic * $hra['value'] / 100);
            $special = collect($structure->components)->where('name', 'Special Allowance')->first()['value'] ?? 0;
            $gross = $basic + $hraVal + $special;

            EmployeeSalary::updateOrCreate(
                ['employee_id' => $emp->id, 'tenant_id' => $tenant->id],
                [
                    'salary_structure_id' => $structure->id,
                    'effective_from' => now()->subMonths(3),
                    'basic_salary' => $basic,
                    'hra' => $hraVal,
                    'special_allowance' => $special,
                    'gross_salary' => $gross,
                    'is_active' => true,
                ]
            );
        }

        // Assign Assets
        $assets[0]->update(['status' => 'assigned']);
        \App\Models\AssetAssignment::create([
            'tenant_id' => $tenant->id,
            'asset_id' => $assets[0]->id,
            'employee_id' => $employees[0]->id,
            'assigned_at' => now()->subMonths(2),
            'assigned_by' => User::first()->id,
        ]);

        $assets[1]->update(['status' => 'assigned']);
        \App\Models\AssetAssignment::create([
            'tenant_id' => $tenant->id,
            'asset_id' => $assets[1]->id,
            'employee_id' => $employees[4]->id,
            'assigned_at' => now()->subMonths(2),
            'assigned_by' => User::first()->id,
        ]);

        // 5. Attendance (Last 60 days)
        $startDate = now()->subDays(60);
        $endDate = now();

        foreach ($employees as $emp) {
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if ($current->isWeekend()) {
                    $current->addDay();
                    continue;
                }

                // Check if attendance already exists
                if (Attendance::where('employee_id', $emp->id)->where('date', $current->format('Y-m-d'))->exists()) {
                    $current->addDay();
                    continue;
                }

                // 90% chance of being present
                if (rand(1, 100) <= 90) {
                    $shiftStart = Carbon::parse($current->format('Y-m-d') . ' ' . $emp->shift->start_time);
                    $shiftEnd = Carbon::parse($current->format('Y-m-d') . ' ' . $emp->shift->end_time);

                    // Randomize times
                    $punchIn = $shiftStart->copy()->addMinutes(rand(-15, 30)); // -15 to +30 mins
                    $punchOut = $shiftEnd->copy()->addMinutes(rand(-10, 60)); // -10 to +60 mins

                    $isLate = false;
                    if ($punchIn->gt($shiftStart->copy()->addMinutes(15))) {
                        $isLate = true;
                    }

                    Attendance::create([
                        'tenant_id' => $tenant->id,
                        'employee_id' => $emp->id,
                        'date' => $current->format('Y-m-d'),
                        'shift_id' => $emp->shift_id,
                        'punch_in' => $punchIn,
                        'punch_out' => $punchOut,
                        'status' => 'present',
                        'total_work_hours' => $punchOut->diffInHours($punchIn),
                        'is_late' => $isLate,
                    ]);
                } else {
                    // Absent
                    Attendance::create([
                        'tenant_id' => $tenant->id,
                        'employee_id' => $emp->id,
                        'date' => $current->format('Y-m-d'),
                        'shift_id' => $emp->shift_id,
                        'status' => 'absent',
                        'total_work_hours' => 0,
                    ]);
                }
                $current->addDay();
            }
        }

        // 6. Payroll (Last 2 completed months)
        $months = [now()->subMonths(2), now()->subMonth()];
        foreach ($months as $month) {
            foreach ($employees as $emp) {
                if (Payroll::where('employee_id', $emp->id)->where('month', $month->month)->where('year', $month->year)->exists()) {
                    continue;
                }

                $salary = $emp->currentSalary;
                if (!$salary)
                    continue;

                Payroll::create([
                    'tenant_id' => $tenant->id,
                    'employee_id' => $emp->id,
                    'month' => $month->month,
                    'year' => $month->year,
                    'salary_structure_id' => $salary->salary_structure_id,
                    'basic_salary' => $salary->basic_salary,
                    'gross_salary' => $salary->gross_salary,
                    'net_salary' => $salary->gross_salary * 0.9, // Simplified tax/deductions
                    'earnings' => ['Basic' => $salary->basic_salary, 'HRA' => $salary->hra, 'Special' => $salary->special_allowance],
                    'deductions' => ['Tax' => $salary->gross_salary * 0.1],
                    'total_working_days' => 22,
                    'present_days' => 22,
                    'absent_days' => 0,
                    'leave_days' => 0,
                    'loss_of_pay_amount' => 0,
                    'overtime_amount' => 0,
                    'overtime_hours' => 0,
                    'tax_deducted' => $salary->gross_salary * 0.1,
                    'status' => 'paid',
                    'processed_at' => $month->endOfMonth(),
                    'paid_at' => $month->endOfMonth(),
                ]);
            }
        }
    }
}
