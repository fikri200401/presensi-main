<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Leave;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_offices' => Office::count(),
            'today_attendance' => Attendance::where(function($q) {
                $q->whereDate('date', today())
                  ->orWhere(function($q2) {
                      $q2->whereNull('date')
                         ->whereDate('created_at', today());
                  });
            })->count(),
            'pending_leaves' => Leave::where('status', 'pending')->count(),
        ];

        $recentAttendances = Attendance::with(['user.schedule.shift', 'user.schedule.office'])
            ->latest()
            ->limit(10)
            ->get();

        $attendanceChart = Attendance::select(
            DB::raw('COALESCE(DATE(date), DATE(created_at)) as att_date'),
            DB::raw('COUNT(*) as count')
        )
            ->where(function($q) {
                $q->where('date', '>=', now()->subDays(7))
                  ->orWhere(function($q2) {
                      $q2->whereNull('date')
                         ->where('created_at', '>=', now()->subDays(7));
                  });
            })
            ->groupBy(DB::raw('COALESCE(DATE(date), DATE(created_at))'))
            ->orderBy('att_date', 'ASC')
            ->get();

        // Get today's attendance for non-admin roles (employee, kadiv, direksi)
        $todayAttendance = null;
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $todayAttendance = Attendance::where('user_id', auth()->id())
                ->where(function($q) {
                    $q->whereDate('date', today())
                      ->orWhere(function($q2) {
                          $q2->whereNull('date')
                             ->whereDate('created_at', today());
                      });
                })
                ->first();
        }

        return view('dashboard', compact('stats', 'recentAttendances', 'attendanceChart', 'todayAttendance'));
    }
}
