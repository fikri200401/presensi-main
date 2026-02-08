<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SalarySetting;

class SalarySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SalarySetting::create([
            'jam_kerja_per_hari' => 8,
            'hari_kerja_per_bulan' => 21,
            'total_jam_per_bulan' => 173,
            'metode_perhitungan_default' => 'bulanan',
            'tunjangan_transport_default' => 500000,
            'tunjangan_makan_default' => 750000,
            'potongan_bpjs_kesehatan_persen' => 1.00,
            'potongan_bpjs_ketenagakerjaan_persen' => 2.00,
        ]);
    }
}
