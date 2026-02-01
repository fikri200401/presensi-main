<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user.schedule.shift', 'user.schedule.office']);

        // Employee hanya bisa lihat attendance mereka sendiri
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $attendances = $query->latest()->paginate(15);

        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::all();
        $schedules = Schedule::with(['shift', 'office'])->get();
        return view('attendance.create', compact('users', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_latitude' => 'required|numeric',
            'schedule_longitude' => 'required|numeric',
            'schedule_start_time' => 'required',
            'schedule_end_time' => 'required',
            'start_latitude' => 'nullable|numeric',
            'start_longitude' => 'nullable|numeric',
            'end_latitude' => 'nullable|numeric',
            'end_longitude' => 'nullable|numeric',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance created successfully');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        $schedules = Schedule::with(['shift', 'office'])->get();
        return view('attendance.edit', compact('attendance', 'users', 'schedules'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_latitude' => 'required|numeric',
            'schedule_longitude' => 'required|numeric',
            'schedule_start_time' => 'required',
            'schedule_end_time' => 'required',
            'start_latitude' => 'nullable|numeric',
            'start_longitude' => 'nullable|numeric',
            'end_latitude' => 'nullable|numeric',
            'end_longitude' => 'nullable|numeric',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully');
    }
}
