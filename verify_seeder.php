<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "==============================================\n";
echo "       VERIFIKASI DATA SEEDER\n";
echo "==============================================\n\n";

// Users
echo "📊 USERS (Total: " . App\Models\User::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$users = App\Models\User::with('roles')->get();
foreach ($users as $user) {
    $role = $user->roles->first()?->name ?? 'no role';
    echo sprintf("%-25s %-25s %s\n", $user->name, $user->email, "[$role]");
}

// Offices
echo "\n📍 OFFICES (Total: " . App\Models\Office::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$offices = App\Models\Office::all();
foreach ($offices as $office) {
    echo sprintf("%-30s Radius: %dm\n", $office->name, $office->radius);
}

// Shifts
echo "\n⏰ SHIFTS (Total: " . App\Models\Shift::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$shifts = App\Models\Shift::all();
foreach ($shifts as $shift) {
    echo sprintf("%-20s %s - %s\n", $shift->name, $shift->start_time, $shift->end_time);
}

// Schedules
echo "\n📅 SCHEDULES (Total: " . App\Models\Schedule::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$schedules = App\Models\Schedule::with(['user', 'shift', 'office'])->get();
$wfaCount = $schedules->where('is_wfa', true)->count();
$bannedCount = $schedules->where('is_banned', true)->count();
echo "WFA Employees: $wfaCount\n";
echo "Banned Employees: $bannedCount\n";
echo "Office Employees: " . ($schedules->count() - $wfaCount) . "\n";

// Attendances  
echo "\n✅ ATTENDANCES (Total: " . App\Models\Attendance::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$attendances = App\Models\Attendance::with('user')->get();
$perUser = $attendances->groupBy('user_id')->map->count();
echo "Rata-rata attendance per user: " . number_format($attendances->count() / App\Models\User::role('employee')->count(), 2) . " hari\n";
echo "User dengan attendance terbanyak: " . $perUser->max() . " hari\n";
echo "User dengan attendance tersedikit: " . $perUser->min() . " hari\n";

// Leaves
echo "\n🏖️  LEAVES (Total: " . App\Models\Leave::count() . ")\n";
echo str_repeat("-", 46) . "\n";
$leaves = App\Models\Leave::all();
$approved = $leaves->where('status', 'approved')->count();
$pending = $leaves->where('status', 'pending')->count();
$rejected = $leaves->where('status', 'rejected')->count();
echo "Approved: $approved\n";
echo "Pending: $pending\n";
echo "Rejected: $rejected\n";

// Roles & Permissions
echo "\n👥 ROLES & PERMISSIONS\n";
echo str_repeat("-", 46) . "\n";
$roles = Spatie\Permission\Models\Role::with('permissions')->get();
foreach ($roles as $role) {
    echo sprintf("%-15s (%d permissions)\n", $role->name, $role->permissions->count());
}

echo "\n==============================================\n";
echo "✅ SEEDER BERHASIL DIJALANKAN!\n";
echo "==============================================\n";
echo "\n📝 Login Credentials:\n";
echo "   Super Admin: superadmin@presensi.com / password\n";
echo "   Admin: admin@presensi.com / password\n";
echo "   Employees: {name}@presensi.com / password\n";
echo "\n";
