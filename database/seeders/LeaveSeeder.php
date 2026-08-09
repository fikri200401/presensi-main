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
     *
     * Status flow sesuai 3-layer approval:
     * pending → approved_kadiv → approved_hr → approved (final direksi)
     * Reject di tahap manapun: rejected_kadiv / rejected_hr / rejected_direksi
     */
    public function run(): void
    {
        $employees = User::role('employee')->get();

        if ($employees->isEmpty()) {
            $this->command->warn('Make sure users are seeded first!');
            return;
        }

        // Ambil approver per layer
        $kadiv = User::role('kepala_divisi')->first();
        $hr = User::role('admin')->first();
        $direksi = User::role('direksi')->first();

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

        foreach ($employees as $index => $employee) {
            $leaveCount = rand(1, 3);

            for ($i = 0; $i < $leaveCount; $i++) {
                $startDate = Carbon::now()->subDays(rand(0, 60))->addDays(rand(0, 90));
                $duration = rand(1, 7);
                $endDate = $startDate->copy()->addDays($duration);
                $reason = $leaveReasons[array_rand($leaveReasons)];
                $createdAt = $startDate->copy()->subDays(rand(7, 14));

                // Distribusi status realistis:
                // 30% approved (final), 20% approved_hr (menunggu direksi),
                // 15% approved_kadiv (menunggu HR), 20% pending,
                // 5% rejected_kadiv, 5% rejected_hr, 5% rejected_direksi
                $statusRand = rand(1, 100);

                $data = [
                    'user_id' => $employee->id,
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'reason' => $reason,
                    'note' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                    // Default null approval fields
                    'approved_by_kadiv' => null,
                    'approved_at_kadiv' => null,
                    'note_kadiv' => null,
                    'approved_by_hr' => null,
                    'approved_at_hr' => null,
                    'note_hr' => null,
                    'approved_by_direksi' => null,
                    'approved_at_direksi' => null,
                    'note_direksi' => null,
                ];

                if ($statusRand <= 30) {
                    // ── APPROVED (final - semua 3 layer approve) ──
                    $data['status'] = 'approved';
                    $data['note'] = 'Pengajuan cuti disetujui semua pihak.';
                    // Layer 1
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Disetujui. Silakan koordinasi dengan tim.';
                    // Layer 2
                    $data['approved_by_hr'] = $hr?->id;
                    $data['approved_at_hr'] = $createdAt->copy()->addDays(2);
                    $data['note_hr'] = 'Disetujui oleh HR.';
                    // Layer 3
                    $data['approved_by_direksi'] = $direksi?->id;
                    $data['approved_at_direksi'] = $createdAt->copy()->addDays(3);
                    $data['note_direksi'] = 'Final approval. Disetujui.';
                    $data['updated_at'] = $createdAt->copy()->addDays(3);

                } elseif ($statusRand <= 50) {
                    // ── APPROVED_HR (menunggu approval direksi) ──
                    $data['status'] = 'approved_hr';
                    // Layer 1
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Disetujui oleh Kepala Divisi.';
                    // Layer 2
                    $data['approved_by_hr'] = $hr?->id;
                    $data['approved_at_hr'] = $createdAt->copy()->addDays(2);
                    $data['note_hr'] = 'Disetujui. Menunggu approval Direksi.';
                    $data['updated_at'] = $createdAt->copy()->addDays(2);

                } elseif ($statusRand <= 65) {
                    // ── APPROVED_KADIV (menunggu approval HR) ──
                    $data['status'] = 'approved_kadiv';
                    // Layer 1 only
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Disetujui. Menunggu approval HR.';
                    $data['updated_at'] = $createdAt->copy()->addDays(1);

                } elseif ($statusRand <= 85) {
                    // ── PENDING (belum ada approval) ──
                    $data['status'] = 'pending';

                } elseif ($statusRand <= 90) {
                    // ── REJECTED_KADIV ──
                    $data['status'] = 'rejected_kadiv';
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Ditolak. Periode cuti bertepatan dengan deadline project.';
                    $data['updated_at'] = $createdAt->copy()->addDays(1);

                } elseif ($statusRand <= 95) {
                    // ── REJECTED_HR ──
                    $data['status'] = 'rejected_hr';
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Disetujui oleh Kadiv.';
                    $data['approved_by_hr'] = $hr?->id;
                    $data['approved_at_hr'] = $createdAt->copy()->addDays(2);
                    $data['note_hr'] = 'Ditolak. Kuota cuti bulan ini sudah penuh.';
                    $data['updated_at'] = $createdAt->copy()->addDays(2);

                } else {
                    // ── REJECTED_DIREKSI ──
                    $data['status'] = 'rejected_direksi';
                    $data['approved_by_kadiv'] = $kadiv?->id;
                    $data['approved_at_kadiv'] = $createdAt->copy()->addDays(1);
                    $data['note_kadiv'] = 'Disetujui oleh Kadiv.';
                    $data['approved_by_hr'] = $hr?->id;
                    $data['approved_at_hr'] = $createdAt->copy()->addDays(2);
                    $data['note_hr'] = 'Disetujui oleh HR.';
                    $data['approved_by_direksi'] = $direksi?->id;
                    $data['approved_at_direksi'] = $createdAt->copy()->addDays(3);
                    $data['note_direksi'] = 'Ditolak oleh Direksi. Harap reschedule.';
                    $data['updated_at'] = $createdAt->copy()->addDays(3);
                }

                Leave::create($data);
            }
        }

        $total = Leave::count();
        $this->command->info("Leaves created successfully! Total: {$total}");
        $this->command->info('Status breakdown:');
        foreach (['pending', 'approved_kadiv', 'approved_hr', 'approved', 'rejected_kadiv', 'rejected_hr', 'rejected_direksi'] as $status) {
            $count = Leave::where('status', $status)->count();
            $this->command->info("  {$status}: {$count}");
        }
    }
}
