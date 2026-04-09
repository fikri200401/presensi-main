<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        // Cari user admin/super_admin
        $admin = User::role(['super_admin', 'admin'])->first();
        // Cari user employee biasa
        $karyawan = User::role('employee')->first() ?? User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin']))->first();

        if ($admin) {
            Notification::insert([
                [
                    'user_id'    => $admin->id,
                    'type'       => 'leave_request',
                    'title'      => 'Pengajuan Izin Baru',
                    'message'    => 'Agus Setiawan mengajukan izin sakit selama 2 hari (7–8 Mar 2026).',
                    'url'        => '/leave',
                    'read_at'    => null,
                    'created_at' => now()->subMinutes(5),
                    'updated_at' => now()->subMinutes(5),
                ],
                [
                    'user_id'    => $admin->id,
                    'type'       => 'leave_request',
                    'title'      => 'Pengajuan Cuti Baru',
                    'message'    => 'Siti Nurhaliza mengajukan cuti tahunan selama 3 hari (10–12 Mar 2026).',
                    'url'        => '/leave',
                    'read_at'    => null,
                    'created_at' => now()->subHours(1),
                    'updated_at' => now()->subHours(1),
                ],
                [
                    'user_id'    => $admin->id,
                    'type'       => 'attendance_alert',
                    'title'      => 'Peringatan Ketidakhadiran',
                    'message'    => '3 karyawan belum melakukan check-in hari ini hingga pukul 09.30.',
                    'url'        => '/attendance',
                    'read_at'    => null,
                    'created_at' => now()->subHours(3),
                    'updated_at' => now()->subHours(3),
                ],
                [
                    'user_id'    => $admin->id,
                    'type'       => 'info',
                    'title'      => 'Laporan Payroll Siap',
                    'message'    => 'Laporan penggajian bulan Februari 2026 telah selesai diproses.',
                    'url'        => '/payroll',
                    'read_at'    => now()->subDay(),
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ],
            ]);
        }

        if ($karyawan) {
            Notification::insert([
                [
                    'user_id'    => $karyawan->id,
                    'type'       => 'leave_approved',
                    'title'      => 'Izin Anda Disetujui',
                    'message'    => 'Pengajuan izin Anda tanggal 5 Mar 2026 telah disetujui oleh admin.',
                    'url'        => '/leave',
                    'read_at'    => null,
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ],
                [
                    'user_id'    => $karyawan->id,
                    'type'       => 'info',
                    'title'      => 'Slip Gaji Tersedia',
                    'message'    => 'Slip gaji Anda untuk bulan Februari 2026 sudah bisa diunduh.',
                    'url'        => '/payroll',
                    'read_at'    => null,
                    'created_at' => now()->subDays(1),
                    'updated_at' => now()->subDays(1),
                ],
            ]);
        }
    }
}
