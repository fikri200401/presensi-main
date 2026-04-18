<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Schedule;
use App\Models\Leave;
use App\Models\Attendance;
use Auth;
use Illuminate\Support\Carbon;

class Presensi extends Component
{
    public $latitude;
    public $longitude;
    public $insideRadius = false;
    
    public function mount()
    {
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        
        // Redirect jika tidak punya schedule
        if (!$schedule) {
            session()->flash('error', 'Anda tidak memiliki jadwal kerja. Fitur presensi hanya untuk karyawan.');
            $this->redirect(route('dashboard'));
        }
    }

    public function render()
    {
        $schedule = Schedule::where('user_id', Auth::user()->id)->first();
        
        if (!$schedule) {
            return '';
        }
        
        $attendance = Attendance::where('user_id', Auth::user()->id)
                        ->where(function($q) {
                            $q->whereDate('date', date('Y-m-d'))
                              ->orWhere(function($q2) {
                                  $q2->whereNull('date')
                                     ->whereDate('created_at', date('Y-m-d'));
                              });
                        })->first();
        return view('livewire.presensi',[
            'schedule' => $schedule,
            'insideRadius' => $this->insideRadius,
            'attendance' => $attendance
        ]);
    }

    public function store()
    {
        $this->validate([
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $schedule = Schedule::where('user_id', Auth::user()->id)->first();

        $today = Carbon::today()->format('Y-m-d');
         $approvedLeave = Leave::where('user_id', Auth::user()->id)
                              ->where('status', 'approved')
                              ->whereDate('start_date', '<=', $today)
                              ->whereDate('end_date', '>=', $today)
                              ->exists();

        if ($approvedLeave) {
            session()->flash('error', 'Anda tidak dapat melakukan presensi karena sedang cuti.');
            return;
        }

        if ($schedule) {
            $attedance = Attendance::where('user_id', Auth::user()->id)
                        ->where(function($q) {
                            $q->whereDate('date', date('Y-m-d'))
                              ->orWhere(function($q2) {
                                  $q2->whereNull('date')
                                     ->whereDate('created_at', date('Y-m-d'));
                              });
                        })->first();
            if (!$attedance) {
                $attedance = Attendance::create([
                    'user_id' => Auth::user()->id,
                    'date' => Carbon::today()->format('Y-m-d'),
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'start_latitude' => $this->latitude,
                    'start_longitude' => $this->longitude,
                    'start_time' => Carbon::now()->toTimeString(),
                    'end_time' => null,
                ]);
                session()->flash('success', '✅ Check-in berhasil! Selamat bekerja.');
            } else {
                $attedance->update([
                    'end_latitude' => $this->latitude,
                    'end_longitude' => $this->longitude,
                    'end_time' => Carbon::now()->toTimeString(),
                ]);
                session()->flash('success', '✅ Check-out berhasil! Terima kasih atas kerja keras Anda hari ini.');
            }

            // Reset state
            $this->insideRadius = false;
            $this->latitude = null;
            $this->longitude = null;

            return redirect()->route('presensi');
        }
    }
}
