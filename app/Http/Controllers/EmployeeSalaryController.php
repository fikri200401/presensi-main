<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\User;
use App\Models\SalarySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of employee salaries
     */
    public function index()
    {
        $employeeSalaries = EmployeeSalary::with('user')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee-salary.index', compact('employeeSalaries'));
    }

    /**
     * Show the form for creating a new employee salary
     */
    public function create()
    {
        // Get users who don't have active salary configuration yet
        $users = User::whereDoesntHave('employeeSalary', function($q) {
            $q->where('is_active', true);
        })->get();

        $settings = SalarySetting::getSettings();

        return view('employee-salary.create', compact('users', 'settings'));
    }

    /**
     * Store a newly created employee salary
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'gaji_pokok_bulanan' => 'required|numeric|min:0',
            'tipe_karyawan' => 'required|in:tetap,harian,paruh_waktu',
            'metode_perhitungan' => 'required|in:bulanan,harian,jam',
            'tunjangan_transport' => 'nullable|numeric|min:0',
            'tunjangan_makan' => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'tunjangan_keluarga' => 'nullable|numeric|min:0',
            'tunjangan_lainnya' => 'nullable|numeric|min:0',
            'potongan_bpjs_kesehatan_persen' => 'nullable|numeric|min:0|max:100',
            'potongan_bpjs_ketenagakerjaan_persen' => 'nullable|numeric|min:0|max:100',
            'potongan_pph21' => 'nullable|numeric|min:0',
            'potongan_lainnya' => 'nullable|numeric|min:0',
            'berlaku_dari' => 'nullable|date',
        ]);

        // Deactivate old salary configuration if exists
        EmployeeSalary::where('user_id', $request->user_id)
            ->update(['is_active' => false]);

        // Create new salary configuration
        $employeeSalary = EmployeeSalary::create([
            'user_id' => $request->user_id,
            'gaji_pokok_bulanan' => $request->gaji_pokok_bulanan,
            'tipe_karyawan' => $request->tipe_karyawan,
            'metode_perhitungan' => $request->metode_perhitungan,
            'tunjangan_transport' => $request->tunjangan_transport ?? 0,
            'tunjangan_makan' => $request->tunjangan_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'tunjangan_keluarga' => $request->tunjangan_keluarga ?? 0,
            'tunjangan_lainnya' => $request->tunjangan_lainnya ?? 0,
            'potongan_bpjs_kesehatan_persen' => $request->potongan_bpjs_kesehatan_persen ?? 1,
            'potongan_bpjs_ketenagakerjaan_persen' => $request->potongan_bpjs_ketenagakerjaan_persen ?? 2,
            'potongan_pph21' => $request->potongan_pph21 ?? 0,
            'potongan_lainnya' => $request->potongan_lainnya ?? 0,
            'is_active' => true,
            'berlaku_dari' => $request->berlaku_dari ?? now(),
        ]);

        // Auto-calculate rates
        $employeeSalary->calculateRates();

        session()->flash('success', 'Konfigurasi gaji karyawan berhasil dibuat.');
        return redirect()->route('employee-salary.index');
    }

    /**
     * Show the form for editing employee salary
     */
    public function edit(EmployeeSalary $employeeSalary)
    {
        $settings = SalarySetting::getSettings();
        return view('employee-salary.edit', compact('employeeSalary', 'settings'));
    }

    /**
     * Update the specified employee salary
     */
    public function update(Request $request, EmployeeSalary $employeeSalary)
    {
        $request->validate([
            'gaji_pokok_bulanan' => 'required|numeric|min:0',
            'tipe_karyawan' => 'required|in:tetap,harian,paruh_waktu',
            'metode_perhitungan' => 'required|in:bulanan,harian,jam',
            'tunjangan_transport' => 'nullable|numeric|min:0',
            'tunjangan_makan' => 'nullable|numeric|min:0',
            'tunjangan_jabatan' => 'nullable|numeric|min:0',
            'tunjangan_keluarga' => 'nullable|numeric|min:0',
            'tunjangan_lainnya' => 'nullable|numeric|min:0',
            'potongan_bpjs_kesehatan_persen' => 'nullable|numeric|min:0|max:100',
            'potongan_bpjs_ketenagakerjaan_persen' => 'nullable|numeric|min:0|max:100',
            'potongan_pph21' => 'nullable|numeric|min:0',
            'potongan_lainnya' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $employeeSalary->update([
            'gaji_pokok_bulanan' => $request->gaji_pokok_bulanan,
            'tipe_karyawan' => $request->tipe_karyawan,
            'metode_perhitungan' => $request->metode_perhitungan,
            'tunjangan_transport' => $request->tunjangan_transport ?? 0,
            'tunjangan_makan' => $request->tunjangan_makan ?? 0,
            'tunjangan_jabatan' => $request->tunjangan_jabatan ?? 0,
            'tunjangan_keluarga' => $request->tunjangan_keluarga ?? 0,
            'tunjangan_lainnya' => $request->tunjangan_lainnya ?? 0,
            'potongan_bpjs_kesehatan_persen' => $request->potongan_bpjs_kesehatan_persen ?? 1,
            'potongan_bpjs_ketenagakerjaan_persen' => $request->potongan_bpjs_ketenagakerjaan_persen ?? 2,
            'potongan_pph21' => $request->potongan_pph21 ?? 0,
            'potongan_lainnya' => $request->potongan_lainnya ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        // Auto-calculate rates
        $employeeSalary->calculateRates();

        session()->flash('success', 'Konfigurasi gaji karyawan berhasil diupdate.');
        return redirect()->route('employee-salary.index');
    }

    /**
     * Remove the specified employee salary
     */
    public function destroy(EmployeeSalary $employeeSalary)
    {
        $employeeSalary->delete();
        
        session()->flash('success', 'Konfigurasi gaji karyawan berhasil dihapus.');
        return redirect()->route('employee-salary.index');
    }
}
