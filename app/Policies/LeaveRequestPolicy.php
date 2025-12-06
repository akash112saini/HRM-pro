<?php

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    /**
     * Determine if the user can view any leave requests.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view leave requests
    }

    /**
     * Determine if the user can view the leave request.
     */
    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        // Super admin and company admin can view all
        if (in_array($user->role, ['super_admin', 'company_admin'])) {
            return true;
        }

        // Managers can view their team's requests
        if ($user->role === 'manager' && $user->employee) {
            return $leaveRequest->employee->manager_id === $user->employee->id;
        }

        // Employees can view their own requests
        if ($user->employee) {
            return $leaveRequest->employee_id === $user->employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can create leave requests.
     */
    public function create(User $user): bool
    {
        return $user->employee !== null; // Must be an employee
    }

    /**
     * Determine if the user can approve/reject the leave request.
     */
    public function approve(User $user, LeaveRequest $leaveRequest): bool
    {
        // Super admin and company admin can approve all
        if (in_array($user->role, ['super_admin', 'company_admin'])) {
            return true;
        }

        // Managers can approve their team's requests
        if ($user->role === 'manager' && $user->employee) {
            return $leaveRequest->employee->manager_id === $user->employee->id;
        }

        return false;
    }

    /**
     * Determine if the user can cancel the leave request.
     */
    public function cancel(User $user, LeaveRequest $leaveRequest): bool
    {
        // Only the employee who applied can cancel
        if ($user->employee) {
            return $leaveRequest->employee_id === $user->employee->id
                && in_array($leaveRequest->status, ['pending', 'approved']);
        }

        return false;
    }
}
