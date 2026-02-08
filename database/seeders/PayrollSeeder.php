<?php

namespace Database\Seeders;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use App\Models\EmployeeSalary;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = User::role('employee')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('Make sure users are seeded first!');
            return;
        }

        // Create payroll for last 3 months
        $months = [
            Carbon::now()->subMonths(2)->startOfMonth(), // 2 bulan lalu
            Carbon::now()->subMonth()->startOfMonth(),   // Bulan lalu
            Carbon::now()->startOfMonth(),               // Bulan ini
        ];

        $statuses = ['draft', 'pending', 'approved', 'paid'];
        $adminUsers = User::role(['super_admin', 'admin'])->get();

        foreach ($employees as $employee) {
            $employeeSalary = EmployeeSalary::where('user_id', $employee->id)->first();

            if (!$employeeSalary) {
                continue;
            }

            foreach ($months as $index => $monthStart) {
                $monthEnd = $monthStart->copy()->endOfMonth();
                $periode = $monthStart->format('Y-m');
                $bulan = $monthStart->month;
                $tahun = $monthStart->year;

                // Get attendance for this period
                $attendances = Attendance::where('user_id', $employee->id)
                    ->whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])
                    ->get();

                $totalHariHadir = $attendances->count();
                $totalJamHadir = 0;
                $totalTerlambat = 0; // dalam menit

                foreach ($attendances as $attendance) {
                    // Calculate work hours
                    $startTime = Carbon::parse($attendance->start_time);
                    $endTime = Carbon::parse($attendance->end_time);
                    $jamKerja = $startTime->diffInHours($endTime);
                    $totalJamHadir += $jamKerja;

                    // Calculate lateness
                    $scheduleStartTime = Carbon::parse($attendance->schedule_start_time);
                    if ($startTime->greaterThan($scheduleStartTime)) {
                        $terlambat = $scheduleStartTime->diffInMinutes($startTime);
                        $totalTerlambat += $terlambat;
                    }
                }

                // Calculate total working days in this month
                $totalHariKerja = 0;
                for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
                    if (!$date->isWeekend()) {
                        $totalHariKerja++;
                    }
                }

                // Calculate salary
                $gajiPokok = $employeeSalary->gaji_pokok_bulanan;
                $gajiPerHari = $employeeSalary->gaji_per_hari;
                $gajiPerJam = $employeeSalary->gaji_per_jam;

                // Tunjangan
                $tunjanganTransport = $employeeSalary->tunjangan_transport;
                $tunjanganMakan = $employeeSalary->tunjangan_makan;
                $tunjanganLainnya = $employeeSalary->tunjangan_lainnya;
                $totalTunjangan = $tunjanganTransport + $tunjanganMakan + $tunjanganLainnya;

                // Potongan
                $potonganBpjsKesehatan = $employeeSalary->potongan_bpjs_kesehatan;
                $potonganBpjsKetenagakerjaan = $employeeSalary->potongan_bpjs_ketenagakerjaan;
                $potonganPph21 = $employeeSalary->potongan_pph21;
                
                // Potongan keterlambatan (Rp 10.000 per jam terlambat)
                $potonganKeterlambatan = round(($totalTerlambat / 60) * 10000, 2);
                
                $potonganLainnya = $employeeSalary->potongan_lainnya;
                $totalPotongan = $potonganBpjsKesehatan + $potonganBpjsKetenagakerjaan + 
                                $potonganPph21 + $potonganKeterlambatan + $potonganLainnya;

                // Total
                $gajiKotor = $gajiPokok + $totalTunjangan;
                $gajiBersih = $gajiKotor - $totalPotongan;

                // Status based on month
                // Past months: paid
                // Current month: draft/pending
                if ($index === 0) {
                    $status = 'paid';
                    $approvedBy = $adminUsers->random()->id;
                    $approvedAt = $monthEnd->copy()->subDays(5);
                    $catatan = 'Pembayaran telah dilakukan via transfer bank.';
                } elseif ($index === 1) {
                    $status = 'approved';
                    $approvedBy = $adminUsers->random()->id;
                    $approvedAt = $monthEnd->copy()->subDays(2);
                    $catatan = 'Gaji telah disetujui, menunggu proses pembayaran.';
                } else {
                    $status = $totalHariHadir > 0 ? 'pending' : 'draft';
                    $approvedBy = null;
                    $approvedAt = null;
                    $catatan = $status === 'pending' ? 'Menunggu approval dari admin.' : null;
                }

                Payroll::create([
                    'user_id' => $employee->id,
                    'periode' => $periode,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    
                    // Data Kehadiran
                    'total_hari_kerja' => $totalHariKerja,
                    'total_hari_hadir' => $totalHariHadir,
                    'total_jam_kerja' => $totalHariKerja * 8, // Asumsi 8 jam per hari
                    'total_jam_hadir' => $totalJamHadir,
                    'total_terlambat' => $totalTerlambat,
                    
                    // Perhitungan Gaji
                    'gaji_pokok' => $gajiPokok,
                    'gaji_per_hari' => $gajiPerHari,
                    'gaji_per_jam' => $gajiPerJam,
                    
                    // Tunjangan
                    'tunjangan_transport' => $tunjanganTransport,
                    'tunjangan_makan' => $tunjanganMakan,
                    'tunjangan_lainnya' => $tunjanganLainnya,
                    'total_tunjangan' => $totalTunjangan,
                    
                    // Potongan
                    'potongan_bpjs_kesehatan' => $potonganBpjsKesehatan,
                    'potongan_bpjs_ketenagakerjaan' => $potonganBpjsKetenagakerjaan,
                    'potongan_pph21' => $potonganPph21,
                    'potongan_keterlambatan' => $potonganKeterlambatan,
                    'potongan_lainnya' => $potonganLainnya,
                    'total_potongan' => $totalPotongan,
                    
                    // Total
                    'gaji_kotor' => $gajiKotor,
                    'gaji_bersih' => $gajiBersih,
                    
                    // Status & Approval
                    'status' => $status,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt,
                    'catatan' => $catatan,
                    
                    'created_at' => $monthEnd->copy()->subDays(10),
                    'updated_at' => $approvedAt ?? $monthEnd->copy()->subDays(10),
                ]);
            }
        }

        $this->command->info('Payrolls created successfully!');
        $this->command->info('Created payroll for ' . $employees->count() . ' employees');
        $this->command->info('Periods: ' . implode(', ', array_map(fn($m) => $m->format('Y-m'), $months)));
    }
}
