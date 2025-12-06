<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::forTenant(auth()->user()->tenant_id)
            ->orderBy('is_system_role', 'desc')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $availablePermissions = Role::availablePermissions();

        return view('roles.create', compact('availablePermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string'],
        ]);

        // Generate slug from name
        $slug = Str::slug($request->name, '_');

        // Ensure slug is unique for this tenant
        $originalSlug = $slug;
        $counter = 1;
        while (Role::forTenant(auth()->user()->tenant_id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '_' . $counter;
            $counter++;
        }

        $role = Role::create([
            'tenant_id' => auth()->user()->tenant_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'permissions' => $request->permissions,
            'is_system_role' => false,
            'is_active' => true,
        ]);

        return redirect(roleRoute('roles.index'))
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        if ($role->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $availablePermissions = Role::availablePermissions();

        return view('roles.edit', compact('role', 'availablePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        // Ensure role belongs to tenant
        if ($role->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['string'],
            'is_active' => ['boolean'],
        ]);

        // If name changed, regenerate slug
        if ($role->name !== $request->name) {
            $slug = Str::slug($request->name, '_');

            // Ensure slug is unique for this tenant (excluding current role)
            $originalSlug = $slug;
            $counter = 1;
            while (
                Role::forTenant(auth()->user()->tenant_id)
                    ->where('slug', $slug)
                    ->where('id', '!=', $role->id)
                    ->exists()
            ) {
                $slug = $originalSlug . '_' . $counter;
                $counter++;
            }

            $role->slug = $slug;
        }

        $role->update([
            'name' => $request->name,
            'slug' => $role->slug,
            'description' => $request->description,
            'permissions' => $request->permissions,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect(roleRoute('roles.index'))
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // Ensure role belongs to tenant
        if ($role->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }

        // Check if role is a system role
        if ($role->is_system_role) {
            return back()->with('error', 'Cannot delete system roles.');
        }

        // Check if any users have this role
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that is assigned to users.');
        }

        $role->delete();

        return redirect(roleRoute('roles.index'))
            ->with('success', 'Role deleted successfully.');
    }
}
