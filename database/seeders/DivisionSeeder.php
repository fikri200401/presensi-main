<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Seed tabel divisions.
     * Divisi harus sesuai dengan yang digunakan di UserSeeder & PositionHistorySeeder.
     */
    public function run(): void
    {
        $divisions = [
            [
                'name' => 'HRD',
                'description' => 'Human Resource Development - mengelola SDM, rekrutmen, dan administrasi kepegawaian.',
            ],
            [
                'name' => 'IT',
                'description' => 'Information Technology - mengelola infrastruktur teknologi, pengembangan sistem, dan support.',
            ],
            [
                'name' => 'Keuangan',
                'description' => 'Divisi Keuangan - mengelola anggaran, akuntansi, dan pelaporan keuangan.',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Divisi Marketing - mengelola pemasaran produk, branding, dan hubungan nasabah.',
            ],
            [
                'name' => 'Operasional',
                'description' => 'Divisi Operasional - mengelola operasional harian, teller, dan layanan nasabah.',
            ],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(
                ['name' => $division['name']],
                ['description' => $division['description']]
            );
        }

        $this->command->info('Divisions created successfully!');
        $this->command->info('Total Divisions: ' . Division::count());
    }
}
