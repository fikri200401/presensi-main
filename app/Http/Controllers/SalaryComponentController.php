<?php

namespace App\Http\Controllers;

use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryComponentController extends Controller
{
    public function store(Request $request)
    {
        abort_unless($this->canManageSalaryComponents(), 403);

        SalaryComponent::create($this->validatedPayload($request));

        return redirect('/salary-settings')->with('success', 'Jenis tunjangan/potongan berhasil ditambahkan.');
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        abort_unless($this->canManageSalaryComponents(), 403);

        $salaryComponent->update($this->validatedPayload($request));

        return redirect('/salary-settings')->with('success', 'Jenis tunjangan/potongan berhasil diperbarui.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        abort_unless($this->canManageSalaryComponents(), 403);

        $salaryComponent->update(['is_active' => false]);

        return redirect('/salary-settings')->with('success', 'Jenis tunjangan/potongan berhasil dinonaktifkan.');
    }

    private function validatedPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:allowance,deduction',
            'calculation_type' => 'required|in:fixed,percentage,manual',
            'default_amount' => 'nullable|numeric|min:0',
            'default_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        return [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'calculation_type' => $validated['calculation_type'],
            'default_amount' => $validated['default_amount'] ?? 0,
            'default_percentage' => $validated['default_percentage'] ?? 0,
            'notes' => $validated['notes'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : true,
        ];
    }

    private function canManageSalaryComponents(): bool
    {
        $user = Auth::user();

        return $user->can('update_salary_setting')
            || $user->hasAnyRole(['super_admin', 'admin']);
    }
}
