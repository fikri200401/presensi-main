<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Shift;
use App\Models\Office;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Schedule::with(['user', 'shift', 'office']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $schedules = $query->latest()->paginate(15);

        return view('schedule.index', compact('schedules'));
    }

    public function create()
    {
        $users = User::all();
        $shifts = Shift::all();
        $offices = Office::all();
        return view('schedule.create', compact('users', 'shifts', 'offices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'office_id' => 'required|exists:offices,id',
            'is_wfa' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
        ]);

        $validated['is_wfa'] = $request->has('is_wfa');
        $validated['is_banned'] = $request->has('is_banned');

        Schedule::create($validated);

        return redirect()->route('schedule.index')->with('success', 'Schedule created successfully');
    }

    public function edit(Schedule $schedule)
    {
        $users = User::all();
        $shifts = Shift::all();
        $offices = Office::all();
        return view('schedule.edit', compact('schedule', 'users', 'shifts', 'offices'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
            'office_id' => 'required|exists:offices,id',
            'is_wfa' => 'nullable|boolean',
            'is_banned' => 'nullable|boolean',
        ]);

        $validated['is_wfa'] = $request->has('is_wfa');
        $validated['is_banned'] = $request->has('is_banned');

        $schedule->update($validated);

        return redirect()->route('schedule.index')->with('success', 'Schedule updated successfully');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('schedule.index')->with('success', 'Schedule deleted successfully');
    }
}
