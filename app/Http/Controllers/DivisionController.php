<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('view_any_division')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data divisi.');
        }

        $search = $request->input('search');

        $divisions = Division::when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount('users')
            ->orderBy('name')
            ->paginate(15)
            ->appends($request->query());

        return view('division.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('create_division')) {
            abort(403, 'Anda tidak memiliki akses untuk menambah divisi.');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.unique' => 'Nama divisi sudah ada.',
            'name.max' => 'Nama divisi maksimal 100 karakter.',
        ]);

        Division::create($request->only('name', 'description'));

        return redirect()->route('division.index')
            ->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Division $division)
    {
        if (!auth()->user()->can('update_division')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah divisi.');
        }

        $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama divisi wajib diisi.',
            'name.unique' => 'Nama divisi sudah ada.',
        ]);

        $oldName = $division->name;
        $division->update($request->only('name', 'description'));

        // Jika nama divisi berubah, update juga di tabel users & position_histories
        if ($oldName !== $division->name) {
            \App\Models\User::where('division', $oldName)->update(['division' => $division->name]);
            \App\Models\PositionHistory::where('division', $oldName)->update(['division' => $division->name]);
        }

        return redirect()->route('division.index')
            ->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        if (!auth()->user()->can('delete_division')) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus divisi.');
        }

        // Cek apakah divisi masih digunakan
        $userCount = \App\Models\User::where('division', $division->name)->count();
        if ($userCount > 0) {
            return redirect()->route('division.index')
                ->with('error', "Divisi \"{$division->name}\" tidak bisa dihapus karena masih digunakan oleh {$userCount} karyawan.");
        }

        $division->delete();

        return redirect()->route('division.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}
