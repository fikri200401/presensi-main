<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            DivisionSeeder::class,
            UserSeeder::class,
            PositionHistorySeeder::class,
            OfficeSeeder::class,
            ShiftSeeder::class,
            ScheduleSeeder::class,
            SalarySettingSeeder::class,
            EmployeeSalarySeeder::class,
            AttendanceSeeder::class,
            LeaveSeeder::class,
            PayrollSeeder::class,
            NotificationSeeder::class,
        ]);
        
        $this->command->info('====================================');
        $this->command->info('All seeders completed successfully!');
        $this->command->info('====================================');
    }
}
