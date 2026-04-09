<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PositionHistory;
use Illuminate\Http\Request;

class PositionHistoryController extends Controller
{
    /**
     * Store a new position history entry for a user.
     */
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:500',
        ], [
            'position.required' => 'Jabatan wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $user->positionHistories()->create($validated);

        return back()->with('success', 'Riwayat jabatan berhasil ditambahkan.');
    }

    /**
     * Update a position history entry.
     */
    public function update(Request $request, PositionHistory $positionHistory)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:500',
        ]);

        $positionHistory->update($validated);

        return back()->with('success', 'Riwayat jabatan berhasil diperbarui.');
    }

    /**
     * Delete a position history entry.
     */
    public function destroy(PositionHistory $positionHistory)
    {
        $positionHistory->delete();

        return back()->with('success', 'Riwayat jabatan berhasil dihapus.');
    }
}
