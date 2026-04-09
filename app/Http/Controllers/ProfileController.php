<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user profile page.
     */
    public function show()
    {
        $user = Auth::user()->load(['roles', 'schedule.shift', 'schedule.office', 'positionHistories']);

        // Attendance statistics for the current month
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Attendance stats last 6 months for chart
        $attendanceChart = Attendance::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill missing months
        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->translatedFormat('M Y');
            $chartLabels[] = $label;
            $found = $attendanceChart->firstWhere('month', $key);
            $chartData[] = $found ? $found->total : 0;
        }

        // Leave statistics
        $totalLeaves = Leave::where('user_id', $user->id)->count();
        $approvedLeaves = Leave::where('user_id', $user->id)->where('status', 'approved')->count();
        $pendingLeaves = Leave::where('user_id', $user->id)->whereIn('status', ['pending', 'approved_kadiv', 'approved_hr'])->count();

        // On-time vs late stats this month (compare start_time vs schedule_start_time)
        $monthAttendances = Attendance::where('user_id', $user->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->whereNotNull('start_time')
            ->get();

        $onTimeCount = 0;
        $lateCount = 0;
        foreach ($monthAttendances as $att) {
            if ($att->schedule_start_time && $att->start_time) {
                if ($att->start_time > $att->schedule_start_time) {
                    $lateCount++;
                } else {
                    $onTimeCount++;
                }
            } else {
                $onTimeCount++;
            }
        }

        // Working days this month (approx)
        $workingDaysInMonth = collect(range(1, now()->daysInMonth))->filter(function ($day) {
            $date = now()->startOfMonth()->addDays($day - 1);
            return !in_array($date->dayOfWeek, [0, 6]); // Exclude Sat & Sun
        })->count();

        $attendanceRate = $workingDaysInMonth > 0
            ? round(($monthlyAttendance / $workingDaysInMonth) * 100)
            : 0;

        return view('profile.show', compact(
            'user',
            'monthlyAttendance',
            'chartLabels',
            'chartData',
            'totalLeaves',
            'approvedLeaves',
            'pendingLeaves',
            'onTimeCount',
            'lateCount',
            'workingDaysInMonth',
            'attendanceRate'
        ));
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal 8 karakter.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('password_success', 'Password berhasil diubah.');
    }
}
