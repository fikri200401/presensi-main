<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use App\Models\SalaryComponent;
use App\Models\SalarySetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with(['user', 'approver']);

        $user = Auth::user();
        if (!$this->canViewPayrollAsManager()) {
            $query->where('user_id', $user->id)
                ->where('status', 'approved');
        }

        if ($request->filled('status') && $this->canViewPayrollAsManager()) {
            $query->where('status', $request->status);
        }

        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }

        $payrolls = $query->orderBy('periode', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    public function create()
    {
        abort_unless($this->canManagePayrollDrafts(), 403);

        $employees = User::whereHas('employeeSalary', function ($q) {
            $q->where('is_active', true);
        })->get();

        return view('payroll.create', compact('employees'));
    }

    public function generate(Request $request)
    {
        abort_unless($this->canManagePayrollDrafts(), 403);

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
                $payrollUser = User::find($userId);

                $existing = Payroll::where('user_id', $userId)
                    ->where('periode', $periode)
                    ->first();

                if ($existing) {
                    $errors[] = "Payroll untuk user ID {$userId} periode {$periode} sudah ada.";
                    continue;
                }

                $employeeSalary = EmployeeSalary::where('user_id', $userId)
                    ->where('is_active', true)
                    ->with('salaryComponents.component')
                    ->first();

                if (!$employeeSalary || !$payrollUser) {
                    $errors[] = "User ID {$userId} tidak memiliki konfigurasi gaji aktif.";
                    continue;
                }

                $attendances = Attendance::where('user_id', $userId)
                    ->where(function ($query) use ($tahun, $bulan) {
                        $query->where(function ($dateQuery) use ($tahun, $bulan) {
                            $dateQuery->whereNotNull('date')
                                ->whereYear('date', $tahun)
                                ->whereMonth('date', $bulan);
                        })->orWhere(function ($fallbackQuery) use ($tahun, $bulan) {
                            $fallbackQuery->whereNull('date')
                                ->whereYear('created_at', $tahun)
                                ->whereMonth('created_at', $bulan);
                        });
                    })
                    ->whereNotNull('start_time')
                    ->get();

                $totalHariHadir = $attendances->count();
                $totalJamHadir = 0;
                $totalTerlambat = 0;

                foreach ($attendances as $attendance) {
                    if ($attendance->start_time && $attendance->end_time) {
                        $masuk = Carbon::parse($attendance->start_time);
                        $keluar = Carbon::parse($attendance->end_time);
                        $totalJamHadir += $masuk->diffInHours($keluar);
                    }

                    if ($attendance->isLate()) {
                        $totalTerlambat++;
                    }
                }

                $gajiPokok = 0;
                $gajiPerHari = $employeeSalary->gaji_per_hari;
                $gajiPerJam = $employeeSalary->gaji_per_jam;

                switch ($employeeSalary->metode_perhitungan) {
                    case 'harian':
                        $gajiPokok = $gajiPerHari * $totalHariHadir;
                        break;
                    case 'jam':
                        $gajiPokok = $gajiPerJam * $totalJamHadir;
                        break;
                    case 'bulanan':
                    default:
                        $gajiPokok = $employeeSalary->gaji_pokok_bulanan;
                        break;
                }

                $tunjanganTransport = $employeeSalary->tunjangan_transport ?? 0;
                $tunjanganMakan = $employeeSalary->tunjangan_makan ?? 0;
                $tunjanganJabatan = $payrollUser->hasRole('kepala_divisi')
                    ? (float) $settings->tunjangan_jabatan_kepala_divisi
                    : 0;
                $tunjanganLainnya = $employeeSalary->tunjangan_lainnya ?? 0;
                $totalTunjangan = $tunjanganTransport + $tunjanganMakan + $tunjanganJabatan + $tunjanganLainnya;

                $potonganJht = round($gajiPokok * ((float) $settings->potongan_jht_persen / 100), 2);
                $potonganJks = round($gajiPokok * ((float) $settings->potongan_jks_persen / 100), 2);
                $potonganPph21 = $employeeSalary->potongan_pph21 ?? 0;
                $potonganKeterlambatan = 0;
                $potonganManual = 0;
                $potonganLainnya = $employeeSalary->potongan_lainnya ?? 0;
                $totalPotongan = $potonganJht + $potonganJks + $potonganPph21 + $potonganKeterlambatan + $potonganManual + $potonganLainnya;

                $payrollComponents = [];
                foreach ($employeeSalary->salaryComponents as $employeeComponent) {
                    $component = $employeeComponent->component;
                    if (!$component || !$component->is_active || !$employeeComponent->is_active) {
                        continue;
                    }

                    $percentage = $employeeComponent->percentage ?? $component->default_percentage;
                    $amount = match ($component->calculation_type) {
                        SalaryComponent::CALCULATION_PERCENTAGE => round($gajiPokok * ((float) $percentage / 100), 2),
                        SalaryComponent::CALCULATION_MANUAL => (float) ($employeeComponent->amount ?? $component->default_amount ?? 0),
                        default => (float) ($employeeComponent->amount ?? $component->default_amount ?? 0),
                    };

                    if ($amount <= 0) {
                        continue;
                    }

                    if ($component->type === SalaryComponent::TYPE_ALLOWANCE) {
                        $totalTunjangan += $amount;
                    } else {
                        $totalPotongan += $amount;
                    }

                    $payrollComponents[] = [
                        'salary_component_id' => $component->id,
                        'name' => $component->name,
                        'type' => $component->type,
                        'calculation_type' => $component->calculation_type,
                        'amount' => $amount,
                        'percentage' => $component->calculation_type === SalaryComponent::CALCULATION_PERCENTAGE ? $percentage : null,
                        'notes' => $employeeComponent->notes,
                    ];
                }

                $gajiKotor = $gajiPokok + $totalTunjangan;
                $gajiBersih = $gajiKotor - $totalPotongan;

                $payroll = Payroll::create([
                    'user_id' => $userId,
                    'periode' => $periode,
                    'bulan' => (int) $bulan,
                    'tahun' => (int) $tahun,
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
                    'tunjangan_jabatan' => $tunjanganJabatan,
                    'tunjangan_lainnya' => $tunjanganLainnya,
                    'total_tunjangan' => $totalTunjangan,
                    'potongan_bpjs_kesehatan' => 0,
                    'potongan_bpjs_ketenagakerjaan' => 0,
                    'potongan_jht' => $potonganJht,
                    'potongan_jks' => $potonganJks,
                    'potongan_pph21' => $potonganPph21,
                    'potongan_keterlambatan' => $potonganKeterlambatan,
                    'potongan_manual' => $potonganManual,
                    'potongan_lainnya' => $potonganLainnya,
                    'total_potongan' => $totalPotongan,
                    'gaji_kotor' => $gajiKotor,
                    'gaji_bersih' => $gajiBersih,
                    'status' => 'draft',
                ]);

                if (!empty($payrollComponents)) {
                    $payroll->components()->createMany($payrollComponents);
                }

                $generatedCount++;
            }

            DB::commit();

            if ($generatedCount > 0) {
                session()->flash('success', "Berhasil generate {$generatedCount} payroll draft untuk periode {$periode}.");
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

    public function show(Payroll $payroll)
    {
        $this->authorizePayrollVisibility($payroll);

        $payroll->load(['user', 'approver', 'components']);
        return view('payroll.show', compact('payroll'));
    }

    public function updateDeductions(Request $request, Payroll $payroll)
    {
        abort_unless($this->canManagePayrollDrafts(), 403);
        abort_unless($payroll->status === 'draft', 403);

        $validated = $request->validate([
            'potongan_manual' => 'nullable|numeric|min:0',
            'keterangan_potongan_manual' => 'nullable|string|max:500',
        ]);

        $payroll->potongan_manual = $validated['potongan_manual'] ?? 0;
        $payroll->keterangan_potongan_manual = $validated['keterangan_potongan_manual'] ?? null;
        $this->recalculatePayrollTotals($payroll);
        $payroll->save();

        return redirect()->route('payroll.show', $payroll)->with('success', 'Potongan manual berhasil disimpan.');
    }

    public function submit(Payroll $payroll)
    {
        abort_unless($this->canManagePayrollDrafts(), 403);

        if (!in_array($payroll->status, ['draft', 'rejected'], true)) {
            return back()->withErrors(['error' => 'Hanya payroll draft atau rejected yang bisa diajukan.']);
        }

        $payroll->update([
            'status' => 'pending',
            'catatan' => null,
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll berhasil diajukan untuk approval.');
    }

    public function approve(Payroll $payroll)
    {
        abort_unless($this->canApprovePayroll(), 403);

        if ($payroll->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya payroll pending yang bisa disetujui.']);
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll berhasil disetujui. Slip sudah bisa dilihat karyawan.');
    }

    public function reject(Request $request, Payroll $payroll)
    {
        abort_unless($this->canApprovePayroll(), 403);

        if ($payroll->status !== 'pending') {
            return back()->withErrors(['error' => 'Hanya payroll pending yang bisa ditolak.']);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:1000',
        ]);

        $payroll->update([
            'status' => 'rejected',
            'catatan' => $validated['catatan'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()->route('payroll.show', $payroll)->with('success', 'Payroll ditolak dan dikembalikan untuk koreksi.');
    }

    public function destroy(Payroll $payroll)
    {
        abort_unless($this->canManagePayrollDrafts(), 403);

        if ($payroll->status !== 'draft') {
            return back()->withErrors(['error' => 'Hanya payroll dengan status draft yang bisa dihapus.']);
        }

        $payroll->delete();

        session()->flash('success', 'Payroll berhasil dihapus.');
        return redirect()->route('payroll.index');
    }

    public function exportPdf(Payroll $payroll)
    {
        $this->authorizePayrollVisibility($payroll);

        $payroll->load(['user', 'approver', 'components']);
        $office = \App\Models\Office::first();

        return view('payroll.pdf', compact('payroll', 'office'));
    }

    private function recalculatePayrollTotals(Payroll $payroll): void
    {
        $payroll->total_tunjangan =
            (float) $payroll->tunjangan_transport +
            (float) $payroll->tunjangan_makan +
            (float) $payroll->tunjangan_jabatan +
            (float) $payroll->tunjangan_lainnya +
            (float) $payroll->components()
                ->where('type', SalaryComponent::TYPE_ALLOWANCE)
                ->sum('amount');

        $payroll->total_potongan =
            (float) $payroll->potongan_bpjs_kesehatan +
            (float) $payroll->potongan_bpjs_ketenagakerjaan +
            (float) $payroll->potongan_jht +
            (float) $payroll->potongan_jks +
            (float) $payroll->potongan_pph21 +
            (float) $payroll->potongan_keterlambatan +
            (float) $payroll->potongan_manual +
            (float) $payroll->potongan_lainnya +
            (float) $payroll->components()
                ->where('type', SalaryComponent::TYPE_DEDUCTION)
                ->sum('amount');

        $payroll->gaji_kotor = (float) $payroll->gaji_pokok + (float) $payroll->total_tunjangan;
        $payroll->gaji_bersih = (float) $payroll->gaji_kotor - (float) $payroll->total_potongan;
    }

    private function authorizePayrollVisibility(Payroll $payroll): void
    {
        if ($this->canViewPayrollAsManager()) {
            return;
        }

        $userId = Auth::id();

        abort_unless($userId !== null && (int) $payroll->user_id === (int) $userId && $payroll->status === 'approved', 403);
    }

    private function canViewPayrollAsManager(): bool
    {
        $user = Auth::user();

        return $user->hasAnyRole(['super_admin', 'admin', 'direksi'])
            || $user->can('view_any_payroll');
    }

    private function canManagePayrollDrafts(): bool
    {
        $user = Auth::user();

        return $user->hasAnyRole(['super_admin', 'admin'])
            || $user->can('create_payroll');
    }

    private function canApprovePayroll(): bool
    {
        $user = Auth::user();

        return $user->hasAnyRole(['super_admin', 'admin', 'direksi'])
            || $user->can('view_any_payroll');
    }
}
