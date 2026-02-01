<?php

namespace Database\Seeders;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class LeaveSeeder extends Seeder
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

        $leaveReasons = [
            'Cuti Tahunan',
            'Sakit',
            'Keperluan Keluarga',
            'Cuti Melahirkan',
            'Umroh/Haji',
            'Pernikahan',
            'Kematian Keluarga',
            'Keperluan Mendesak',
        ];

        $statuses = ['pending', 'approved', 'rejected'];

        foreach ($employees as $index => $employee) {
            // Create 1-3 leave records per employee
            $leaveCount = rand(1, 3);

            for ($i = 0; $i < $leaveCount; $i++) {
                // Random date in the last 60 days or future 30 days
                $startDate = Carbon::now()->subDays(rand(0, 60))->addDays(rand(0, 90));
                $duration = rand(1, 7); // 1-7 days leave
                $endDate = $startDate->copy()->addDays($duration);

                // 70% approved, 20% pending, 10% rejected
                $statusRand = rand(1, 100);
                if ($statusRand <= 70) {
                    $status = 'approved';
                } elseif ($statusRand <= 90) {
                    $status = 'pending';
                } else {
                    $status = 'rejected';
                }

                $reason = $leaveReasons[array_rand($leaveReasons)];
                
                $note = null;
                if ($status === 'approved') {
                    $note = 'Pengajuan cuti disetujui. Harap menyelesaikan pekerjaan sebelum cuti.';
                } elseif ($status === 'rejected') {
                    $note = 'Pengajuan cuti ditolak. Periode cuti bertepatan dengan deadline project penting.';
                }

                Leave::create([
                    'user_id' => $employee->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'reason' => $reason,
                    'status' => $status,
                    'note' => $note,
                    'created_at' => $startDate->copy()->subDays(rand(7, 14)), // Applied 7-14 days before
                    'updated_at' => $status === 'pending' ? $startDate->copy()->subDays(rand(7, 14)) : $startDate->copy()->subDays(rand(1, 5)),
                ]);
            }
        }

        $this->command->info('Leaves created successfully!');
    }
}
