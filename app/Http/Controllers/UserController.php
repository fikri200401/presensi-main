<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PositionHistory;
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
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
        ]);

        // Auto-generate position dari role + division
        $role = $validated['role'] ?? null;
        $division = $validated['division'] ?? null;
        $position = User::generatePosition($role, $division);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'image' => $validated['image'] ?? null,
            'division' => $division,
            'position' => $position,
            'nip' => $validated['nip'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($role) {
            $user->assignRole($role);

            // Otomatis buat entry pertama di riwayat jabatan
            $user->positionHistories()->create([
                'position' => $position,
                'division' => $division,
                'role' => $role,
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'description' => "Diangkat sebagai {$position}.",
            ]);
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
            'nip' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:20',
        ]);

        // Simpan data lama sebelum update
        $oldRole = $user->getRoleNames()->first();
        $oldDivision = $user->division;
        $oldPosition = $user->position;

        // Tentukan role & divisi baru
        $newRole = $validated['role'] ?? $oldRole;
        $newDivision = $validated['division'] ?? $oldDivision;

        // Auto-generate position dari role + division
        $position = User::generatePosition($newRole, $newDivision);

        // Cek apakah role atau divisi berubah
        $positionChanged = ($oldRole !== $newRole) || ($oldDivision !== $newDivision);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'image' => $validated['image'] ?? $user->image,
            'division' => $newDivision,
            'position' => $position,
            'nip' => $validated['nip'] ?? $user->nip,
            'phone' => $validated['phone'] ?? $user->phone,
        ]);

        if (!empty($validated['password'])) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        // Jika role atau divisi berubah, otomatis update riwayat jabatan
        if ($positionChanged) {
            // Tutup entry aktif yang lama (set end_date = hari ini)
            $user->positionHistories()
                ->whereNull('end_date')
                ->update(['end_date' => now()->toDateString()]);

            // Buat entry baru untuk jabatan yang baru
            $user->positionHistories()->create([
                'position' => $position,
                'division' => $newDivision,
                'role' => $newRole,
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'description' => "Dipindahkan dari {$oldPosition} ke {$position}.",
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User deleted successfully');
    }
}
