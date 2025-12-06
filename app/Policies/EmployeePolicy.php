<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin', 'manager']);
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Super admin and company admin can view all
        if (in_array($user->role, ['super_admin', 'company_admin'])) {
            return true;
        }

        // Managers can view their team members
        if ($user->role === 'manager' && $user->employee) {
            return $employee->manager_id === $user->employee->id;
        }

        // Employees can view their own profile
        if ($user->employee) {
            return $employee->id === $user->employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return in_array($user->role, ['super_admin', 'company_admin']);
    }
}
