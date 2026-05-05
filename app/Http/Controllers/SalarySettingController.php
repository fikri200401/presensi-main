<?php

namespace App\Http\Controllers;

use App\Models\SalarySetting;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalarySettingController extends Controller
{
    public function edit()
    {
        abort_unless($this->canViewSalarySettings(), 403);

        return view('salary-settings.edit', [
            'settings' => SalarySetting::getSettings(),
            'salaryComponents' => SalaryComponent::orderBy('type')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request)
    {
        abort_unless($this->canUpdateSalarySettings(), 403);

        $validated = $request->validate([
            'jam_kerja_per_hari' => 'required|integer|min:1|max:24',
            'hari_kerja_per_minggu' => 'required|integer|min:1|max:7',
            'hari_kerja_per_bulan' => 'required|integer|min:1|max:31',
            'total_jam_per_bulan' => 'required|integer|min:1|max:744',
            'metode_perhitungan_default' => 'required|in:bulanan,harian,jam',
            'tunjangan_transport_default' => 'nullable|numeric|min:0',
            'tunjangan_makan_default' => 'nullable|numeric|min:0',
            'tunjangan_jabatan_kepala_divisi' => 'nullable|numeric|min:0',
            'potongan_jht_persen' => 'nullable|numeric|min:0|max:100',
            'potongan_jks_persen' => 'nullable|numeric|min:0|max:100',
            'catatan' => 'nullable|string|max:1000',
        ]);

        $settings = SalarySetting::getSettings();
        $settings->update([
            'jam_kerja_per_hari' => $validated['jam_kerja_per_hari'],
            'hari_kerja_per_minggu' => $validated['hari_kerja_per_minggu'],
            'hari_kerja_per_bulan' => $validated['hari_kerja_per_bulan'],
            'total_jam_per_bulan' => $validated['total_jam_per_bulan'],
            'metode_perhitungan_default' => $validated['metode_perhitungan_default'],
            'tunjangan_transport_default' => $validated['tunjangan_transport_default'] ?? 0,
            'tunjangan_makan_default' => $validated['tunjangan_makan_default'] ?? 0,
            'tunjangan_jabatan_kepala_divisi' => $validated['tunjangan_jabatan_kepala_divisi'] ?? 0,
            'potongan_jht_persen' => $validated['potongan_jht_persen'] ?? 0,
            'potongan_jks_persen' => $validated['potongan_jks_persen'] ?? 0,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect('/salary-settings')->with('success', 'Pengaturan gaji berhasil disimpan.');
    }

    private function canViewSalarySettings(): bool
    {
        $user = Auth::user();

        return $user->can('view_salary_setting')
            || $user->can('update_salary_setting')
            || $user->hasAnyRole(['super_admin', 'admin']);
    }

    private function canUpdateSalarySettings(): bool
    {
        $user = Auth::user();

        return $user->can('update_salary_setting')
            || $user->hasAnyRole(['super_admin', 'admin']);
    }
}
