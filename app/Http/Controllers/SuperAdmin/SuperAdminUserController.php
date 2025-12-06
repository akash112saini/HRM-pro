<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdminRole;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserController extends Controller
{
    public function index()
    {
        $superAdmins = User::where('role', 'super_admin')
            ->with('superAdminRole')
            ->latest()
            ->get();
        $roles = SuperAdminRole::active()->get();

        return view('super-admin.super-admins.index', compact('superAdmins', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'super_admin_role_id' => 'nullable|exists:super_admin_roles,id',
        ]);

        $superAdmin = User::create([
            'tenant_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'super_admin',
            'super_admin_role_id' => $validated['super_admin_role_id'] ?? null,
            'is_active' => true,
        ]);

        ActivityLog::logActivity(
            'created_super_admin',
            $superAdmin,
            null,
            $superAdmin->toArray(),
            "Created super admin: {$superAdmin->email}"
        );

        return redirect()->route('super-admin.super-admins.index')
            ->with('success', 'Super admin created successfully!');
    }

    public function destroy(User $superAdmin)
    {
        // Prevent deletion of own account
        if ($superAdmin->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        // Ensure user is super admin
        if ($superAdmin->role !== 'super_admin') {
            return back()->with('error', 'This is not a super admin account!');
        }

        $email = $superAdmin->email;
        $superAdmin->delete();

        ActivityLog::logActivity(
            'deleted_super_admin',
            null,
            $superAdmin->toArray(),
            null,
            "Deleted super admin: {$email}"
        );

        return redirect()->route('super-admin.super-admins.index')
            ->with('success', 'Super admin deleted successfully!');
    }
}
