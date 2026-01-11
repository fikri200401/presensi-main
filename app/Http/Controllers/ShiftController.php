<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $shifts = $query->latest()->paginate(15);

        return view('shift.index', compact('shifts'));
    }

    public function create()
    {
        return view('shift.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::create($validated);

        return redirect()->route('shift.index')->with('success', 'Shift created successfully');
    }

    public function edit(Shift $shift)
    {
        return view('shift.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shift->update($validated);

        return redirect()->route('shift.index')->with('success', 'Shift updated successfully');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('shift.index')->with('success', 'Shift deleted successfully');
    }
}
