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
            'today_attendance' => Attendance::whereDate('created_at', today())->count(),
            'pending_leaves' => Leave::where('status', 'pending')->count(),
        ];

        $recentAttendances = Attendance::with(['user.schedule.shift', 'user.schedule.office'])
            ->latest()
            ->limit(10)
            ->get();

        $attendanceChart = Attendance::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get today's attendance for current employee
        $todayAttendance = null;
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $todayAttendance = Attendance::where('user_id', auth()->id())
                ->whereDate('created_at', today())
                ->first();
        }

        return view('dashboard', compact('stats', 'recentAttendances', 'attendanceChart', 'todayAttendance'));
    }
}
