<?php

if (!function_exists('roleRoute')) {
    /**
     * Generate a URL for a named route based on the authenticated user's role.
     *
     * @param  string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     * @return string
     */
    function roleRoute($name, $parameters = [], $absolute = true)
    {
        $user = auth()->user();
        $role = $user ? $user->role : 'employee';

        // Map roles to route prefixes
        $prefix = match ($role) {
            'super_admin' => 'super-admin',
            'company_admin' => 'admin',
            'manager' => 'manager',
            'employee' => 'employee',
            default => 'employee',
        };

        // Handle special cases
        if ($name === 'profile') {
            if ($role === 'super_admin') {
                return route('super-admin.profile.show', $parameters, $absolute);
            }
            if ($role === 'company_admin') {
                return route('employee.profile', $parameters, $absolute);
            }
        }

        return route($prefix . '.' . $name, $parameters, $absolute);
    }
}
