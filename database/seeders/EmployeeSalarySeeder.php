<?php

namespace Database\Seeders;

use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmployeeSalarySeeder extends Seeder
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

        $salaryRanges = [
            ['min' => 5000000, 'max' => 7000000], // Junior
            ['min' => 7000000, 'max' => 10000000], // Mid-level
            ['min' => 10000000, 'max' => 15000000], // Senior
            ['min' => 15000000, 'max' => 25000000], // Manager
        ];

        foreach ($employees as $index => $employee) {
            // Distribute employees across salary ranges
            $rangeIndex = $index % count($salaryRanges);
            $range = $salaryRanges[$rangeIndex];
            
            $gajiPokokBulanan = rand($range['min'], $range['max']);
            $tunjanganTransport = rand(300000, 700000);
            $tunjanganMakan = rand(500000, 1000000);
            
            // Higher salary = higher allowances
            if ($gajiPokokBulanan >= 10000000) {
                $tunjanganLainnya = rand(1000000, 3000000);
            } elseif ($gajiPokokBulanan >= 7000000) {
                $tunjanganLainnya = rand(500000, 1500000);
            } else {
                $tunjanganLainnya = rand(0, 500000);
            }

            // Calculate BPJS
            $bpjsKesehatan = $gajiPokokBulanan * 0.01; // 1%
            $bpjsKetenagakerjaan = $gajiPokokBulanan * 0.02; // 2%

            EmployeeSalary::create([
                'user_id' => $employee->id,
                'gaji_pokok_bulanan' => $gajiPokokBulanan,
                'gaji_per_jam' => round($gajiPokokBulanan / 173, 2), // 173 jam per bulan
                'gaji_per_hari' => round($gajiPokokBulanan / 21, 2), // 21 hari kerja per bulan
                'tipe_karyawan' => 'tetap',
                'metode_perhitungan' => 'bulanan',
                'tunjangan_transport' => $tunjanganTransport,
                'tunjangan_makan' => $tunjanganMakan,
                'tunjangan_lainnya' => $tunjanganLainnya,
                'potongan_bpjs_kesehatan' => $bpjsKesehatan,
                'potongan_bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
                'is_active' => true,
                'berlaku_dari' => now()->startOfMonth(),
            ]);
        }

        $this->command->info('Employee salaries created successfully!');
        $this->command->info('Total employees with salary: ' . $employees->count());
    }
}
