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
        ]);
        $superAdmin->assignRole('super_admin');

        // Create Admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@presensi.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // Create Employees
        $employees = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@presensi.com',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@presensi.com',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@presensi.com',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@presensi.com',
            ],
            [
                'name' => 'Rudi Hartono',
                'email' => 'rudi.hartono@presensi.com',
            ],
            [
                'name' => 'Maya Sari',
                'email' => 'maya.sari@presensi.com',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@presensi.com',
            ],
            [
                'name' => 'Rina Kusuma',
                'email' => 'rina.kusuma@presensi.com',
            ],
            [
                'name' => 'Agus Setiawan',
                'email' => 'agus.setiawan@presensi.com',
            ],
            [
                'name' => 'Lina Marlina',
                'email' => 'lina.marlina@presensi.com',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'email' => 'fajar.ramadhan@presensi.com',
            ],
            [
                'name' => 'Indah Permata',
                'email' => 'indah.permata@presensi.com',
            ],
            [
                'name' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@presensi.com',
            ],
            [
                'name' => 'Ratna Sari',
                'email' => 'ratna.sari@presensi.com',
            ],
            [
                'name' => 'Fikri Abdullah',
                'email' => 'fikri.abdullah@presensi.com',
            ],
            [
                'name' => 'Ayu Lestari',
                'email' => 'ayu.lestari@presensi.com',
            ],
            [
                'name' => 'Yoga Pratama',
                'email' => 'yoga.pratama@presensi.com',
            ],
            [
                'name' => 'Dina Mariana',
                'email' => 'dina.mariana@presensi.com',
            ],
            [
                'name' => 'Rizki Fauzi',
                'email' => 'rizki.fauzi@presensi.com',
            ],
            [
                'name' => 'Nadia Putri',
                'email' => 'nadia.putri@presensi.com',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = User::create([
                'name' => $employeeData['name'],
                'email' => $employeeData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $employee->assignRole('employee');
        }

        $this->command->info('Users created successfully!');
        $this->command->info('Total Users: ' . (User::count()));
        $this->command->info('Super Admin: superadmin@presensi.com / password');
        $this->command->info('Admin: admin@presensi.com / password');
        $this->command->info('Employees: ' . count($employees) . ' employees, all use password: password');
    }
}
