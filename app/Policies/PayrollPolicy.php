<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;

class PayrollPolicy
{
    /**
     * Determine if the user can view any payrolls.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine if the user can view the payroll.
     */
    public function view(User $user, Payroll $payroll): bool
    {
        // Super admin and company admin can view all
        if (in_array($user->role, ['super_admin', 'company_admin'])) {
            return true;
        }

        // Employees can view their own payslips
        if ($user->employee) {
            return $payroll->employee_id === $user->employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can generate payroll.
     */
    public function generate(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine if the user can mark payroll as paid.
     */
    public function markAsPaid(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }
}
