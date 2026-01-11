<?php

namespace App\Http\Controllers;

use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    public function index(Request $request)
    {
        $query = Office::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('address', 'like', "%{$request->search}%");
        }

        $offices = $query->latest()->paginate(15);

        return view('office.index', compact('offices'));
    }

    public function create()
    {
        return view('office.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        Office::create($validated);

        return redirect()->route('office.index')->with('success', 'Office created successfully');
    }

    public function edit(Office $office)
    {
        return view('office.edit', compact('office'));
    }

    public function update(Request $request, Office $office)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric|min:1',
        ]);

        $office->update($validated);

        return redirect()->route('office.index')->with('success', 'Office updated successfully');
    }

    public function destroy(Office $office)
    {
        $office->delete();
        return redirect()->route('office.index')->with('success', 'Office deleted successfully');
    }
}
