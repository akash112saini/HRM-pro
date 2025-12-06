<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminRole;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SuperAdminRoleController extends Controller
{
    public function index()
    {
        $roles = SuperAdminRole::withCount('users')->latest()->get();
        return view('super-admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $availablePermissions = SuperAdminRole::availablePermissions();
        return view('super-admin.roles.create', compact('availablePermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:super_admin_roles,slug|alpha_dash',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $role = SuperAdminRole::create($validated);

        ActivityLog::logActivity(
            'created_role',
            $role,
            null,
            $role->toArray(),
            "Created super admin role: {$role->name}"
        );

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role created successfully!');
    }

    public function edit(SuperAdminRole $role)
    {
        $availablePermissions = SuperAdminRole::availablePermissions();
        return view('super-admin.roles.edit', compact('role', 'availablePermissions'));
    }

    public function update(Request $request, SuperAdminRole $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('super_admin_roles')->ignore($role->id)],
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $oldValues = $role->toArray();
        $role->update($validated);

        ActivityLog::logActivity(
            'updated_role',
            $role,
            $oldValues,
            $role->toArray(),
            "Updated super admin role: {$role->name}"
        );

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role updated successfully!');
    }

    public function destroy(SuperAdminRole $role)
    {
        // Check if role is in use
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        $roleName = $role->name;
        $role->delete();

        ActivityLog::logActivity(
            'deleted_role',
            null,
            $role->toArray(),
            null,
            "Deleted super admin role: {$roleName}"
        );

        return redirect()->route('super-admin.roles.index')
            ->with('success', 'Role deleted successfully!');
    }
}
