<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = User::with('employee')->orderBy('name');

        if (auth()->user()->tenant_id) {
            $query->where('tenant_id', auth()->user()->tenant_id);
        }

        $users = $query->get();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get employees who don't have a user account yet
        $employees = Employee::whereDoesntHave('user')
            ->orderBy('first_name')
            ->get();

        // Fetch roles for the current tenant
        $roles = auth()->user()->tenant->runOnTenant(function () {
            return \App\Models\Role::active()->get();
        });

        return view('users.create', compact('employees', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => ['required', 'integer'], // Validate role_id
            'employee_id' => ['nullable', 'exists:employees,id'],
        ]);

        // Fetch the selected role to get its slug
        $role = auth()->user()->tenant->runOnTenant(function () use ($request) {
            return \App\Models\Role::findOrFail($request->role_id);
        });

        $user = User::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role->slug, // Store slug for backward compatibility
            'custom_role_id' => $role->id,
            'is_active' => true,
        ]);

        // Link to employee if selected
        if ($request->employee_id) {
            $employee = Employee::find($request->employee_id);
            $employee->update(['user_id' => $user->id]);
        }

        return redirect(roleRoute('users.index'))
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        // Super admins can edit any user, others can only edit users in their tenant
        if (!auth()->user()->isSuperAdmin()) {
            if ($user->tenant_id !== auth()->user()->tenant_id) {
                abort(403);
            }
        }

        $employees = Employee::where(function ($query) use ($user) {
            $query->whereDoesntHave('user')
                ->orWhere('user_id', $user->id);
        })
            ->orderBy('first_name')
            ->get();

        // Fetch roles for the current tenant
        $roles = auth()->user()->tenant->runOnTenant(function () {
            return \App\Models\Role::active()->get();
        });

        return view('users.edit', compact('user', 'employees', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // Super admins can update any user, others can only update users in their tenant
        if (!auth()->user()->isSuperAdmin()) {
            if ($user->tenant_id !== auth()->user()->tenant_id) {
                abort(403);
            }
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'integer'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'is_active' => ['boolean'],
        ]);

        // Fetch the selected role to get its slug
        $role = auth()->user()->tenant->runOnTenant(function () use ($request) {
            return \App\Models\Role::findOrFail($request->role_id);
        });

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $role->slug,
            'custom_role_id' => $role->id,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Update employee link
        if ($request->employee_id) {
            // Remove old link if any
            Employee::where('user_id', $user->id)->update(['user_id' => null]);

            // Add new link
            $employee = Employee::find($request->employee_id);
            $employee->update(['user_id' => $user->id]);
        } elseif ($request->has('employee_id') && is_null($request->employee_id)) {
            Employee::where('user_id', $user->id)->update(['user_id' => null]);
        }

        return redirect(roleRoute('users.index'))
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        // Super admins can delete any user, others can only delete users in their tenant
        if (!auth()->user()->isSuperAdmin()) {
            if ($user->tenant_id !== auth()->user()->tenant_id) {
                abort(403);
            }
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect(roleRoute('users.index'))
            ->with('success', 'User deleted successfully.');
    }
}
