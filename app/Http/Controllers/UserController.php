<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        $users = $query->latest()->paginate(15);

        return view('user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|string',
            'role' => 'nullable|exists:roles,name',
            'division' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'image' => $validated['image'] ?? null,
            'division' => $validated['division'] ?? null,
            'position' => $validated['position'] ?? null,
            'nip' => $validated['nip'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        if (isset($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        return redirect()->route('user.index')->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('positionHistories');
        return view('user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|string',
            'role' => 'nullable|exists:roles,name',
            'division' => 'nullable|string|max:100',
            'position' => 'nullable|string|max:255',
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'image' => $validated['image'] ?? $user->image,
            'division' => $validated['division'] ?? $user->division,
            'position' => $validated['position'] ?? $user->position,
            'nip' => $validated['nip'] ?? $user->nip,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }
}
