<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\EmployeeSalary;
use App\Models\Attendance;
use App\Models\SalarySetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    /**
     * Display a listing of payrolls
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['user', 'approver']);

        // Non-admin users can only see their own payroll
        $user = Auth::user();
        if (!$user->hasRole(['super_admin', 'admin'])) {
            $query->where('user_id', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by periode
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $payrolls = $query->orderBy('periode', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    /**
     * Show the form for generating new payrolls
     */
    public function create()
    {
        $employees = User::whereHas('employeeSalary', function($q) {
            $q->where('is_active', true);
        })->get();

        return view('payroll.create', compact('employees'));
    }

    /**
     * Generate payrolls for selected employees
     */
    public function generate(Request $request)
    {
        $request->validate([
            'periode' => 'required|date_format:Y-m',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $periode = $request->periode;
        [$tahun, $bulan] = explode('-', $periode);
        
        $settings = SalarySetting::getSettings();
        $generatedCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($request->user_ids as $userId) {
                // Check if payroll already exists
                $existing = Payroll::where('user_id', $userId)
                    ->where('periode', $periode)
                    ->first();
                    
                if ($existing) {
                    $errors[] = "Payroll untuk user ID {$userId} periode {$periode} sudah ada.";
                    continue;
                }

                $employeeSalary = EmployeeSalary::where('user_id', $userId)
                    ->where('is_active', true)
                    ->first();

                if (!$employeeSalary) {
                    $errors[] = "User ID {$userId} tidak memiliki konfigurasi gaji aktif.";
                    continue;
                }

                // Get attendance data for the period (using created_at as date)
                $attendances = Attendance::where('user_id', $userId)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->whereNotNull('start_time') // Has checked in
                    ->get();

                // Calculate attendance stats
                $totalHariHadir = $attendances->count();
                $totalJamHadir = 0;
                $totalTerlambat = 0;

                foreach ($attendances as $attendance) {
                    if ($attendance->start_time && $attendance->end_time) {
                        $masuk = \Carbon\Carbon::parse($attendance->start_time);
                        $keluar = \Carbon\Carbon::parse($attendance->end_time);
                        $totalJamHadir += $masuk->diffInHours($keluar);
                    }

                    // Check if late using the model's isLate method
                    if ($attendance->isLate()) {
                        $totalTerlambat++;
                    }
                }

                // Calculate salary based on method
                $gajiPokok = 0;
                $gajiPerHari = $employeeSalary->gaji_per_hari;
                $gajiPerJam = $employeeSalary->gaji_per_jam;

                switch ($employeeSalary->metode_perhitungan) {
                    case 'bulanan':
                        $gajiPokok = $employeeSalary->gaji_pokok_bulanan;
                        break;
                    case 'harian':
                        $gajiPokok = $gajiPerHari * $totalHariHadir;
                        break;
                    case 'jam':
                        $gajiPokok = $gajiPerJam * $totalJamHadir;
                        break;
                }

                // Calculate tunjangan
                $tunjanganTransport = $employeeSalary->tunjangan_transport ?? 0;
                $tunjanganMakan = $employeeSalary->tunjangan_makan ?? 0;
                $tunjanganLainnya = $employeeSalary->tunjangan_lainnya ?? 0;
                $totalTunjangan = $tunjanganTransport + $tunjanganMakan + $tunjanganLainnya;

                // Calculate potongan
                $potonganBpjsKesehatan = $gajiPokok * ($employeeSalary->potongan_bpjs_kesehatan_persen / 100);
                $potonganBpjsKetenagakerjaan = $gajiPokok * ($employeeSalary->potongan_bpjs_ketenagakerjaan_persen / 100);
                $potonganPph21 = $employeeSalary->potongan_pph21 ?? 0;
                $potonganKeterlambatan = 0; // Can be customized based on business rules
                $potonganLainnya = $employeeSalary->potongan_lainnya ?? 0;
                $totalPotongan = $potonganBpjsKesehatan + $potonganBpjsKetenagakerjaan + 
                                $potonganPph21 + $potonganKeterlambatan + $potonganLainnya;

                // Calculate gaji kotor & bersih
                $gajiKotor = $gajiPokok + $totalTunjangan;
                $gajiBersih = $gajiKotor - $totalPotongan;

                // Create payroll record
                Payroll::create([
                    'user_id' => $userId,
                    'periode' => $periode,
                    'bulan' => (int)$bulan,
                    'tahun' => (int)$tahun,
                    'total_hari_kerja' => $settings->hari_kerja_per_bulan,
                    'total_hari_hadir' => $totalHariHadir,
                    'total_jam_kerja' => $settings->total_jam_per_bulan,
                    'total_jam_hadir' => $totalJamHadir,
                    'total_terlambat' => $totalTerlambat,
                    'gaji_pokok' => $gajiPokok,
                    'gaji_per_hari' => $gajiPerHari,
                    'gaji_per_jam' => $gajiPerJam,
                    'tunjangan_transport' => $tunjanganTransport,
                    'tunjangan_makan' => $tunjanganMakan,
                    'tunjangan_lainnya' => $tunjanganLainnya,
                    'total_tunjangan' => $totalTunjangan,
                    'potongan_bpjs_kesehatan' => $potonganBpjsKesehatan,
                    'potongan_bpjs_ketenagakerjaan' => $potonganBpjsKetenagakerjaan,
                    'potongan_pph21' => $potonganPph21,
                    'potongan_keterlambatan' => $potonganKeterlambatan,
                    'potongan_lainnya' => $potonganLainnya,
                    'total_potongan' => $totalPotongan,
                    'gaji_kotor' => $gajiKotor,
                    'gaji_bersih' => $gajiBersih,
                    'status' => 'draft',
                ]);

                $generatedCount++;
            }

            DB::commit();

            if ($generatedCount > 0) {
                session()->flash('success', "Berhasil generate {$generatedCount} payroll untuk periode {$periode}.");
            }

            if (!empty($errors)) {
                session()->flash('errors', $errors);
            }

            return redirect()->route('payroll.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal generate payroll: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified payroll
     */
    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'approver']);
        return view('payroll.show', compact('payroll'));
    }

    /**
     * Approve a payroll
     */
    public function approve(Payroll $payroll)
    {
        if ($payroll->status === 'approved' || $payroll->status === 'paid') {
            return back()->withErrors(['error' => 'Payroll sudah disetujui sebelumnya.']);
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('success', 'Payroll berhasil disetujui.');
        return back();
    }

    /**
     * Reject a payroll
     */
    public function reject(Request $request, Payroll $payroll)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        if ($payroll->status === 'rejected') {
            return back()->withErrors(['error' => 'Payroll sudah ditolak sebelumnya.']);
        }

        $payroll->update([
            'status' => 'rejected',
            'catatan' => $request->rejection_reason,
        ]);

        session()->flash('success', 'Payroll berhasil ditolak.');
        return back();
    }

    /**
     * Mark payroll as paid
     */
    public function markAsPaid(Payroll $payroll)
    {
        if ($payroll->status !== 'approved') {
            return back()->withErrors(['error' => 'Hanya payroll yang sudah disetujui yang bisa ditandai sebagai dibayar.']);
        }

        $payroll->update([
            'status' => 'paid',
        ]);

        session()->flash('success', 'Payroll berhasil ditandai sebagai dibayar.');
        return back();
    }

    /**
     * Delete a payroll (only draft)
     */
    public function destroy(Payroll $payroll)
    {
        if ($payroll->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya payroll dengan status draft yang bisa dihapus.']);
        }

        $payroll->delete();

        session()->flash('success', 'Payroll berhasil dihapus.');
        return redirect()->route('payroll.index');
    }

    /**
     * Export payroll to PDF (Slip Gaji)
     */
    public function exportPdf(Payroll $payroll)
    {
        // Load relationships
        $payroll->load(['user', 'approver']);

        // Get office info for header
        $office = \App\Models\Office::first();

        // Return view for printing
        return view('payroll.pdf', compact('payroll', 'office'));
    }
}
