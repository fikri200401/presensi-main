<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@presensi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'division' => null,
            'phone' => '081200000001',
            'address' => 'Jl. Kantor Pusat No. 1, Jakarta',
            'position' => User::generatePosition('super_admin'),
            'nip' => 'SA-001',
            'birth_date' => '1985-01-15',
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@presensi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'division' => 'HRD',
            'phone' => '081200000002',
            'address' => 'Jl. Kantor Pusat No. 2, Jakarta',
            'position' => User::generatePosition('admin', 'HRD'),
            'nip' => 'ADM-001',
            'birth_date' => '1988-05-20',
        ]);
        $admin->assignRole('admin');

        // Create Direksi
        $direksi = User::create([
            'name' => 'Ahmad Direktur',
            'email' => 'direksi@presensi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'division' => null,
            'phone' => '081200000003',
            'address' => 'Jl. Kantor Pusat No. 3, Jakarta',
            'position' => User::generatePosition('direksi'),
            'nip' => 'DIR-001',
            'birth_date' => '1980-03-10',
        ]);
        $direksi->assignRole('direksi');

        // Create Kepala Divisi
        $kadiv = User::create([
            'name' => 'Bambang Kadiv',
            'email' => 'kadiv@presensi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'division' => 'IT',
            'phone' => '081200000004',
            'address' => 'Jl. Kantor Pusat No. 4, Jakarta',
            'position' => User::generatePosition('kepala_divisi', 'IT'),
            'nip' => 'KDV-001',
            'birth_date' => '1983-07-25',
        ]);
        $kadiv->assignRole('kepala_divisi');

        // Create Employees
        $employees = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@presensi.com',
                'division' => 'Keuangan',
                'phone' => '081300000001',
                'address' => 'Jl. Merdeka No. 10, Jakarta',
                'nip' => 'EMP-001',
                'birth_date' => '1990-03-12',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@presensi.com',
                'division' => 'HRD',
                'phone' => '081300000002',
                'address' => 'Jl. Sudirman No. 25, Jakarta',
                'nip' => 'EMP-002',
                'birth_date' => '1992-07-08',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@presensi.com',
                'division' => 'IT',
                'phone' => '081300000003',
                'address' => 'Jl. Gatot Subroto No. 5, Jakarta',
                'nip' => 'EMP-003',
                'birth_date' => '1991-11-22',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@presensi.com',
                'division' => 'Marketing',
                'phone' => '081300000004',
                'address' => 'Jl. Thamrin No. 15, Jakarta',
                'nip' => 'EMP-004',
                'birth_date' => '1993-02-14',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@presensi.com',
                'division' => 'Operasional',
                'phone' => '081300000005',
                'address' => 'Jl. Kuningan No. 8, Jakarta',
                'nip' => 'EMP-005',
                'birth_date' => '1989-06-30',
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@presensi.com',
                'division' => 'Keuangan',
                'phone' => '081300000006',
                'address' => 'Jl. Rasuna Said No. 12, Jakarta',
                'nip' => 'EMP-006',
                'birth_date' => '1994-09-05',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@presensi.com',
                'division' => 'IT',
                'phone' => '081300000007',
                'address' => 'Jl. HR Rasuna Said No. 20, Jakarta',
                'nip' => 'EMP-007',
                'birth_date' => '1990-12-18',
            ],
            [
                'name' => 'Rina Kusuma',
                'email' => 'rina.kusuma@presensi.com',
                'division' => 'HRD',
                'phone' => '081300000008',
                'address' => 'Jl. Casablanca No. 3, Jakarta',
                'nip' => 'EMP-008',
                'birth_date' => '1991-04-25',
            ],
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@presensi.com',
                'division' => 'Marketing',
                'phone' => '081300000009',
                'address' => 'Jl. Tendean No. 7, Jakarta',
                'nip' => 'EMP-009',
                'birth_date' => '1988-08-10',
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@presensi.com',
                'division' => 'Operasional',
                'phone' => '081300000010',
                'address' => 'Jl. Fatmawati No. 18, Jakarta',
                'nip' => 'EMP-010',
                'birth_date' => '1993-01-28',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar.ramadhan@presensi.com',
                'division' => 'IT',
                'phone' => '081300000011',
                'address' => 'Jl. Antasari No. 9, Jakarta',
                'nip' => 'EMP-011',
                'birth_date' => '1992-05-17',
            ],
            [
                'name' => 'Indah Permata',
                'email' => 'indah.permata@presensi.com',
                'division' => 'Keuangan',
                'phone' => '081300000012',
                'address' => 'Jl. Kemang No. 22, Jakarta',
                'nip' => 'EMP-012',
                'birth_date' => '1994-10-03',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@presensi.com',
                'division' => 'Operasional',
                'phone' => '081300000013',
                'address' => 'Jl. Bangka No. 14, Jakarta',
                'nip' => 'EMP-013',
                'birth_date' => '1987-07-21',
            ],
            [
                'name' => 'Ratna Sari',
                'email' => 'ratna.sari@presensi.com',
                'division' => 'Marketing',
                'phone' => '081300000014',
                'address' => 'Jl. Blok M No. 6, Jakarta',
                'nip' => 'EMP-014',
                'birth_date' => '1989-03-09',
            ],
            [
                'name' => 'Fikri Abdullah',
                'email' => 'fikri.abdullah@presensi.com',
                'division' => 'IT',
                'phone' => '081300000015',
                'address' => 'Jl. Senopati No. 11, Jakarta',
                'nip' => 'EMP-015',
                'birth_date' => '1990-06-15',
            ],
            [
                'name' => 'Ayu Lestari',
                'email' => 'ayu.lestari@presensi.com',
                'division' => 'HRD',
                'phone' => '081300000016',
                'address' => 'Jl. Wolter Monginsidi No. 4, Jakarta',
                'nip' => 'EMP-016',
                'birth_date' => '1991-12-01',
            ],
            [
                'name' => 'Yoga Pratama',
                'email' => 'yoga.pratama@presensi.com',
                'division' => 'Keuangan',
                'phone' => '081300000017',
                'address' => 'Jl. Prapanca No. 16, Jakarta',
                'nip' => 'EMP-017',
                'birth_date' => '1988-09-27',
            ],
            [
                'name' => 'Dina Mariana',
                'email' => 'dina.mariana@presensi.com',
                'division' => 'Marketing',
                'phone' => '081300000018',
                'address' => 'Jl. Dharmawangsa No. 2, Jakarta',
                'nip' => 'EMP-018',
                'birth_date' => '1995-04-13',
            ],
            [
                'name' => 'Rizki Fauzi',
                'email' => 'rizki.fauzi@presensi.com',
                'division' => 'Operasional',
                'phone' => '081300000019',
                'address' => 'Jl. Panglima Polim No. 19, Jakarta',
                'nip' => 'EMP-019',
                'birth_date' => '1993-08-06',
            ],
            [
                'name' => 'Nadia Putri',
                'email' => 'nadia.putri@presensi.com',
                'division' => 'Keuangan',
                'phone' => '081300000020',
                'address' => 'Jl. Cipete No. 21, Jakarta',
                'nip' => 'EMP-020',
                'birth_date' => '1994-11-19',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = User::create(array_merge($employeeData, [
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'position' => User::generatePosition('employee', $employeeData['division']),
            ]));
            $employee->assignRole('employee');
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Total Users: ' . (User::count()));
        $this->command->info('Super Admin: superadmin@presensi.com / password');
        $this->command->info('Admin: admin@presensi.com / password');
        $this->command->info('Direksi: direksi@presensi.com / password');
        $this->command->info('Kepala Divisi: kadiv@presensi.com / password');
        $this->command->info('Employees: ' . count($employees) . ' employees, all use password: password');
    }
}
