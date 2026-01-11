<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::withCount('permissions');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $roles = $query->latest()->paginate(15);

        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('_', $permission->name);
            return end($parts);
        });
        
        return view('role.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('role.index')->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            $parts = explode('_', $permission->name);
            return end($parts);
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('role.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role->update(['name' => $validated['name']]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('role.index')->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super_admin') {
            return redirect()->route('role.index')->with('error', 'Cannot delete super_admin role');
        }

        $role->delete();
        return redirect()->route('role.index')->with('success', 'Role deleted successfully');
    }
}
