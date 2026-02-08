<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('user');

        // Employee hanya bisa lihat leave mereka sendiri
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest()->paginate(15);

        return view('leave.index', compact('leaves'));
    }

    public function create()
    {
        // Admin dan Super Admin bisa memilih user, employee hanya untuk diri sendiri
        $users = auth()->user()->hasRole(['super_admin', 'admin']) 
            ? User::all() 
            : User::where('id', auth()->id())->get();
        
        return view('leave.create', compact('users'));
    }

    public function store(Request $request)
    {
        // Jika employee, paksa user_id = auth user
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $request->merge([
                'user_id' => auth()->id(),
                'status' => 'pending'
            ]);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        Leave::create($validated);

        return redirect()->route('leave.index')->with('success', 'Leave request created successfully');
    }

    public function edit(Leave $leave)
    {
        // Employee hanya bisa edit leave mereka sendiri
        if (!auth()->user()->hasRole(['super_admin', 'admin']) && $leave->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Admin dan Super Admin bisa memilih user, employee hanya untuk diri sendiri
        $users = auth()->user()->hasRole(['super_admin', 'admin']) 
            ? User::all() 
            : User::where('id', auth()->id())->get();
            
        return view('leave.edit', compact('leave', 'users'));
    }

    public function update(Request $request, Leave $leave)
    {
        // Employee hanya bisa update leave mereka sendiri
        if (!auth()->user()->hasRole(['super_admin', 'admin']) && $leave->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Jika employee, paksa user_id tetap sama dan status tidak berubah jika sudah approved/rejected
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $request->merge([
                'user_id' => $leave->user_id,  // Tidak bisa ganti user
            ]);
            
            // Jika status sudah approved/rejected, employee tidak bisa mengubah
            if (in_array($leave->status, ['approved', 'rejected'])) {
                $request->merge(['status' => $leave->status]);
            } else {
                // Jika masih pending, tetap pending
                $request->merge(['status' => 'pending']);
            }
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave->update($validated);

        return redirect()->route('leave.index')->with('success', 'Leave request updated successfully');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leave.index')->with('success', 'Leave deleted successfully');
    }

    public function approve(Leave $leave)
    {
        // Hanya admin/super_admin yang bisa approve
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $leave->update([
            'status' => 'approved',
            'note' => null
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request approved successfully');
    }

    public function reject(Request $request, Leave $leave)
    {
        // Hanya admin/super_admin yang bisa reject
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|min:10'
        ]);

        $leave->update([
            'status' => 'rejected',
            'note' => $validated['rejection_reason']
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave request rejected');
    }
}
