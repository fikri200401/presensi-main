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
        $users = User::all();
        return view('leave.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        Leave::create($validated);

        return redirect()->route('leave.index')->with('success', 'Leave created successfully');
    }

    public function edit(Leave $leave)
    {
        $users = User::all();
        return view('leave.edit', compact('leave', 'users'));
    }

    public function update(Request $request, Leave $leave)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave->update($validated);

        return redirect()->route('leave.index')->with('success', 'Leave updated successfully');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leave.index')->with('success', 'Leave deleted successfully');
    }
}
