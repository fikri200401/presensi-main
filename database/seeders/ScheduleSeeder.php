<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Shift;
use App\Models\Office;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees (users with employee role)
        $employees = User::role('employee')->get();
        $shifts = Shift::all();
        $offices = Office::all();

        if ($employees->isEmpty() || $shifts->isEmpty() || $offices->isEmpty()) {
            $this->command->warn('Make sure users, shifts, and offices are seeded first!');
            return;
        }

        foreach ($employees as $index => $employee) {
            // Assign different shifts and offices to employees
            $shiftIndex = $index % $shifts->count();
            $officeIndex = $index % $offices->count();
            
            // Some employees work from office, some WFA
            $isWfa = $index % 3 === 0; // Every 3rd employee is WFA
            
            // Last employee will be banned
            $isBanned = ($index === $employees->count() - 1);
            
            Schedule::create([
                'user_id' => $employee->id,
                'shift_id' => $shifts[$shiftIndex]->id,
                'office_id' => $offices[$officeIndex]->id,
                'is_wfa' => $isWfa,
                'is_banned' => $isBanned,
            ]);
        }

        $this->command->info('Schedules created successfully!');
        $this->command->info('WFA employees: ' . $employees->filter(fn($e, $i) => $i % 3 === 0)->count());
        $this->command->info('Banned employees: 1');
    }
}
