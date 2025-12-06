<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends TenantBaseModel
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'permissions',
        'is_system_role',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system_role' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Available permissions grouped by module
     */
    public static function availablePermissions(): array
    {
        return [
            'employees' => [
                'view_employees' => 'View Employees',
                'create_employees' => 'Create Employees',
                'edit_employees' => 'Edit Employees',
                'delete_employees' => 'Delete Employees',
            ],
            'attendance' => [
                'view_attendance' => 'View Attendance',
                'manage_attendance' => 'Manage Attendance',
                'approve_corrections' => 'Approve Attendance Corrections',
            ],
            'leaves' => [
                'view_leaves' => 'View Leave Requests',
                'apply_leave' => 'Apply for Leave',
                'approve_leaves' => 'Approve Leave Requests',
                'manage_leave_types' => 'Manage Leave Types',
            ],
            'payroll' => [
                'view_payroll' => 'View Payroll',
                'manage_payroll' => 'Manage Payroll',
                'view_own_payslip' => 'View Own Payslip',
                'generate_payroll' => 'Generate Payroll',
            ],
            'recruitment' => [
                'view_jobs' => 'View Job Postings',
                'manage_jobs' => 'Manage Job Postings',
                'view_candidates' => 'View Candidates',
                'manage_candidates' => 'Manage Candidates',
                'schedule_interviews' => 'Schedule Interviews',
            ],
            'performance' => [
                'view_appraisals' => 'View Appraisals',
                'conduct_appraisals' => 'Conduct Appraisals',
                'manage_goals' => 'Manage Goals',
            ],
            'assets' => [
                'view_assets' => 'View Assets',
                'manage_assets' => 'Manage Assets',
                'assign_assets' => 'Assign Assets',
            ],
            'settings' => [
                'manage_departments' => 'Manage Departments',
                'manage_designations' => 'Manage Designations',
                'manage_shifts' => 'Manage Shifts',
                'manage_salary_structures' => 'Manage Salary Structures',
                'manage_tax_slabs' => 'Manage Tax Slabs',
                'manage_holidays' => 'Manage Holidays',
                'manage_users' => 'Manage Users',
                'manage_roles' => 'Manage Roles',
            ],
            'reports' => [
                'view_reports' => 'View Reports',
                'export_reports' => 'Export Reports',
            ],
        ];
    }

    /**
     * Scope to get only active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by tenant
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'custom_role_id');
    }

    /**
     * Prevent deletion of system roles
     */
    protected static function booted()
    {
        static::deleting(function ($role) {
            if ($role->is_system_role) {
                throw new \Exception('Cannot delete system roles.');
            }
        });
    }
}
