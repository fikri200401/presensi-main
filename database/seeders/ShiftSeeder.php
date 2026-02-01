<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Shift Pagi',
                'start_time' => '08:00:00',
                'end_time' => '16:00:00',
            ],
            [
                'name' => 'Shift Siang',
                'start_time' => '13:00:00',
                'end_time' => '21:00:00',
            ],
            [
                'name' => 'Shift Malam',
                'start_time' => '21:00:00',
                'end_time' => '05:00:00',
            ],
            [
                'name' => 'Shift Full Day',
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
            ],
            [
                'name' => 'Shift Reguler',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
            ],
            [
                'name' => 'Shift Fleksibel',
                'start_time' => '10:00:00',
                'end_time' => '19:00:00',
            ],
        ];

        foreach ($shifts as $shift) {
            Shift::create($shift);
        }

        $this->command->info('Shifts created successfully!');
        $this->command->info('Total Shifts: ' . count($shifts));
    }
}
