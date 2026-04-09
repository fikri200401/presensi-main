<?php

namespace Database\Seeders;

use App\Models\PositionHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PositionHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Generate riwayat karir & jabatan untuk semua user.
     * Setiap user punya 1-3 riwayat jabatan, yang terakhir (saat ini) end_date = null.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Make sure users are seeded first!');
            return;
        }

        // ── Riwayat khusus: Super Admin ──
        $superAdmin = User::where('email', 'superadmin@presensi.com')->first();
        if ($superAdmin) {
            $this->createHistories($superAdmin, [
                [
                    'position' => 'Staff IT',
                    'division' => 'IT',
                    'role' => 'employee',
                    'start_date' => '2018-01-15',
                    'end_date' => '2020-06-30',
                    'description' => 'Bergabung sebagai staff IT, menangani infrastruktur jaringan.',
                ],
                [
                    'position' => 'Kepala IT',
                    'division' => 'IT',
                    'role' => 'kepala_divisi',
                    'start_date' => '2020-07-01',
                    'end_date' => '2023-12-31',
                    'description' => 'Promosi menjadi Kepala Divisi IT.',
                ],
                [
                    'position' => 'Super Admin',
                    'division' => null,
                    'role' => 'super_admin',
                    'start_date' => '2024-01-01',
                    'end_date' => null,
                    'description' => 'Diangkat sebagai Super Admin sistem HRIS.',
                ],
            ]);
        }

        // ── Riwayat khusus: Admin/HR ──
        $admin = User::where('email', 'admin@presensi.com')->first();
        if ($admin) {
            $this->createHistories($admin, [
                [
                    'position' => 'Staff HRD',
                    'division' => 'HRD',
                    'role' => 'employee',
                    'start_date' => '2019-03-01',
                    'end_date' => '2022-02-28',
                    'description' => 'Bergabung sebagai staff HRD, menangani rekrutmen.',
                ],
                [
                    'position' => 'Admin HRD',
                    'division' => 'HRD',
                    'role' => 'admin',
                    'start_date' => '2022-03-01',
                    'end_date' => null,
                    'description' => 'Promosi menjadi Admin HRD, mengelola sistem HRIS.',
                ],
            ]);
        }

        // ── Riwayat khusus: Direksi ──
        $direksi = User::where('email', 'direksi@presensi.com')->first();
        if ($direksi) {
            $this->createHistories($direksi, [
                [
                    'position' => 'Kepala Keuangan',
                    'division' => 'Keuangan',
                    'role' => 'kepala_divisi',
                    'start_date' => '2015-06-01',
                    'end_date' => '2019-12-31',
                    'description' => 'Memimpin divisi Keuangan selama 4 tahun.',
                ],
                [
                    'position' => 'Wakil Direktur',
                    'division' => null,
                    'role' => 'direksi',
                    'start_date' => '2020-01-01',
                    'end_date' => '2023-05-31',
                    'description' => 'Dipromosikan menjadi Wakil Direktur.',
                ],
                [
                    'position' => 'Direktur Utama',
                    'division' => null,
                    'role' => 'direksi',
                    'start_date' => '2023-06-01',
                    'end_date' => null,
                    'description' => 'Diangkat sebagai Direktur Utama BPRS.',
                ],
            ]);
        }

        // ── Riwayat khusus: Kepala Divisi ──
        $kadiv = User::where('email', 'kadiv@presensi.com')->first();
        if ($kadiv) {
            $this->createHistories($kadiv, [
                [
                    'position' => 'Staff IT',
                    'division' => 'IT',
                    'role' => 'employee',
                    'start_date' => '2019-01-10',
                    'end_date' => '2021-12-31',
                    'description' => 'Bergabung sebagai staff IT, menangani pengembangan sistem.',
                ],
                [
                    'position' => 'Senior Staff IT',
                    'division' => 'IT',
                    'role' => 'employee',
                    'start_date' => '2022-01-01',
                    'end_date' => '2024-06-30',
                    'description' => 'Promosi menjadi Senior Staff, memimpin tim development.',
                ],
                [
                    'position' => 'Kepala Divisi IT',
                    'division' => 'IT',
                    'role' => 'kepala_divisi',
                    'start_date' => '2024-07-01',
                    'end_date' => null,
                    'description' => 'Diangkat sebagai Kepala Divisi IT.',
                ],
            ]);
        }

        // ── Riwayat untuk employees ──
        // Definisi jalur karir per divisi (dari posisi sebelumnya ke posisi sekarang)
        $careerPaths = [
            'Keuangan' => [
                ['position' => 'Magang Keuangan',   'role' => 'employee'],
                ['position' => 'Junior Keuangan',    'role' => 'employee'],
                ['position' => 'Staff Keuangan',     'role' => 'employee'],
                ['position' => 'Senior Keuangan',    'role' => 'employee'],
                ['position' => 'Kepala Keuangan',    'role' => 'employee'],
            ],
            'HRD' => [
                ['position' => 'Magang HRD',         'role' => 'employee'],
                ['position' => 'Junior HRD',          'role' => 'employee'],
                ['position' => 'Staff HRD',           'role' => 'employee'],
                ['position' => 'Senior HRD',          'role' => 'employee'],
                ['position' => 'Kepala HRD',          'role' => 'employee'],
            ],
            'IT' => [
                ['position' => 'Magang IT',           'role' => 'employee'],
                ['position' => 'Junior Developer',    'role' => 'employee'],
                ['position' => 'Staff IT',            'role' => 'employee'],
                ['position' => 'Senior IT',           'role' => 'employee'],
                ['position' => 'Kepala IT',           'role' => 'employee'],
            ],
            'Marketing' => [
                ['position' => 'Magang Marketing',    'role' => 'employee'],
                ['position' => 'Junior Marketing',    'role' => 'employee'],
                ['position' => 'Staff Marketing',     'role' => 'employee'],
                ['position' => 'Senior Marketing',    'role' => 'employee'],
                ['position' => 'Kepala Marketing',    'role' => 'employee'],
            ],
            'Operasional' => [
                ['position' => 'Magang Operasional',  'role' => 'employee'],
                ['position' => 'Junior Operasional',  'role' => 'employee'],
                ['position' => 'Staff Operasional',   'role' => 'employee'],
                ['position' => 'Senior Operasional',  'role' => 'employee'],
                ['position' => 'Kepala Operasional',  'role' => 'employee'],
            ],
        ];

        $descriptions = [
            'Bergabung melalui program magang.',
            'Promosi setelah menunjukkan kinerja yang baik.',
            'Dipromosikan ke posisi saat ini.',
            'Mutasi dari divisi lain.',
            'Naik jabatan berdasarkan evaluasi tahunan.',
        ];

        $employees = User::role('employee')->get();

        foreach ($employees as $index => $employee) {
            $division = $employee->division;
            $currentPosition = $employee->position;

            if (!$division || !isset($careerPaths[$division])) {
                continue;
            }

            $path = $careerPaths[$division];

            // Cari index posisi saat ini di jalur karir
            $currentIndex = null;
            foreach ($path as $i => $step) {
                if ($step['position'] === $currentPosition) {
                    $currentIndex = $i;
                    break;
                }
            }

            // Jika posisi saat ini tidak ada di path, buat posisi terakhir = posisi user saat ini
            if ($currentIndex === null) {
                // Posisi yang belum ada di path, buat riwayat simple
                $currentIndex = 2; // Default Staff level
            }

            // Tentukan berapa banyak riwayat sebelumnya (1-2 posisi sebelumnya)
            $historyDepth = min($currentIndex, rand(1, 2));
            $startIndex = $currentIndex - $historyDepth;

            // Base date: mulai kerja antara 2019-2023
            $baseYear = rand(2019, 2023);
            $baseMonth = rand(1, 6);
            $startDate = Carbon::create($baseYear, $baseMonth, 1);

            $histories = [];

            // Riwayat posisi sebelumnya
            for ($i = $startIndex; $i < $currentIndex; $i++) {
                $endDate = $startDate->copy()->addMonths(rand(10, 24)); // 10-24 bulan per posisi

                $histories[] = [
                    'position' => $path[$i]['position'],
                    'division' => $division,
                    'role' => $path[$i]['role'],
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'description' => $descriptions[array_rand($descriptions)],
                ];

                $startDate = $endDate->copy()->addDay(); // Posisi berikutnya mulai sehari setelahnya
            }

            // Posisi saat ini (end_date = null)
            $histories[] = [
                'position' => $currentPosition,
                'division' => $division,
                'role' => 'employee',
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => null,
                'description' => 'Posisi saat ini.',
            ];

            $this->createHistories($employee, $histories);
        }

        $this->command->info('Position histories created successfully!');
        $this->command->info('Total records: ' . PositionHistory::count());
    }

    /**
     * Helper: Buat beberapa riwayat jabatan untuk satu user.
     */
    private function createHistories(User $user, array $histories): void
    {
        foreach ($histories as $history) {
            $user->positionHistories()->create($history);
        }
    }
}
