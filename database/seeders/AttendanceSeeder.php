<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
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

        // Create attendance records for the last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();

        foreach ($employees as $employee) {
            $schedule = Schedule::where('user_id', $employee->id)
                ->where('is_banned', false)
                ->first();

            if (!$schedule) {
                continue;
            }

            $shift = $schedule->shift;
            $office = $schedule->office;

            // Create attendance for workdays (skip some days randomly)
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Skip weekends
                if ($date->isWeekend()) {
                    continue;
                }

                // Random attendance (85% attendance rate)
                if (rand(1, 100) > 85) {
                    continue;
                }

                // Random late arrival (20% chance)
                $isLate = rand(1, 100) <= 20;
                $startDelay = $isLate ? rand(5, 60) : rand(-5, 5); // Late 5-60 mins or early/on-time
                
                $scheduleStartTime = Carbon::parse($shift->start_time);
                $scheduleEndTime = Carbon::parse($shift->end_time);
                
                $actualStartTime = $scheduleStartTime->copy()->addMinutes($startDelay);
                
                // Random early/late departure (10% leave early, 30% overtime)
                $endVariance = rand(1, 100);
                if ($endVariance <= 10) {
                    $endDelay = rand(-60, -10); // Leave 10-60 mins early
                } elseif ($endVariance <= 40) {
                    $endDelay = rand(10, 120); // Overtime 10-120 mins
                } else {
                    $endDelay = rand(-5, 5); // On time
                }
                
                $actualEndTime = $scheduleEndTime->copy()->addMinutes($endDelay);

                // Add small GPS variance (simulate real GPS coordinates)
                $latVariance = (rand(-50, 50) / 100000); // ~0.5-5 meters variance
                $lngVariance = (rand(-50, 50) / 100000);

                Attendance::create([
                    'user_id' => $employee->id,
                    'schedule_latitude' => $office->latitude,
                    'schedule_longitude' => $office->longitude,
                    'schedule_start_time' => $shift->start_time,
                    'schedule_end_time' => $shift->end_time,
                    'start_latitude' => $office->latitude + $latVariance,
                    'start_longitude' => $office->longitude + $lngVariance,
                    'end_latitude' => $office->latitude + (rand(-50, 50) / 100000),
                    'end_longitude' => $office->longitude + (rand(-50, 50) / 100000),
                    'start_time' => $actualStartTime->format('H:i:s'),
                    'end_time' => $actualEndTime->format('H:i:s'),
                    'created_at' => $date->copy()->setTime($actualStartTime->hour, $actualStartTime->minute),
                    'updated_at' => $date->copy()->setTime($actualEndTime->hour, $actualEndTime->minute),
                ]);
            }
        }

        $this->command->info('Attendances created successfully!');
    }
}
