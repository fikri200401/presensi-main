<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Office;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\Leave;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "\n";
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║           🧪 DATABASE SEEDER TEST SUITE                     ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";

$testsPassed = 0;
$testsFailed = 0;

function test($name, $condition, $expected = null, $actual = null) {
    global $testsPassed, $testsFailed;
    
    if ($condition) {
        echo "✅ PASS: $name\n";
        $testsPassed++;
    } else {
        echo "❌ FAIL: $name\n";
        if ($expected !== null && $actual !== null) {
            echo "   Expected: $expected, Got: $actual\n";
        }
        $testsFailed++;
    }
}

echo "📋 TEST 1: Basic Data Counts\n";
echo str_repeat("─", 60) . "\n";
test("Users count is 22", User::count() === 22, 22, User::count());
test("Offices count is 8", Office::count() === 8, 8, Office::count());
test("Shifts count is 6", Shift::count() === 6, 6, Shift::count());
test("Schedules count is 20", Schedule::count() === 20, 20, Schedule::count());
test("Attendances count > 300", Attendance::count() > 300, ">300", Attendance::count());
test("Leaves count > 30", Leave::count() > 30, ">30", Leave::count());
test("Roles count is 3", Role::count() === 3, 3, Role::count());
test("Permissions count is 42", Permission::count() === 42, 42, Permission::count());
echo "\n";

echo "👥 TEST 2: User Role Assignments\n";
echo str_repeat("─", 60) . "\n";
$superAdmins = User::role('super_admin')->count();
$admins = User::role('admin')->count();
$employees = User::role('employee')->count();
test("Super Admins count is 1", $superAdmins === 1, 1, $superAdmins);
test("Admins count is 1", $admins === 1, 1, $admins);
test("Employees count is 20", $employees === 20, 20, $employees);
test("All users have roles", User::whereDoesntHave('roles')->count() === 0);
test("Super Admin email is correct", User::role('super_admin')->first()->email === 'superadmin@presensi.com');
echo "\n";

echo "📅 TEST 3: Schedule Validations\n";
echo str_repeat("─", 60) . "\n";
$wfaSchedules = Schedule::where('is_wfa', true)->count();
$bannedSchedules = Schedule::where('is_banned', true)->count();
test("WFA schedules count is 7", $wfaSchedules === 7, 7, $wfaSchedules);
test("Banned schedules count is 1", $bannedSchedules === 1, 1, $bannedSchedules);
test("All schedules have valid user", Schedule::whereDoesntHave('user')->count() === 0);
test("All schedules have valid shift", Schedule::whereDoesntHave('shift')->count() === 0);
test("All schedules have valid office", Schedule::whereDoesntHave('office')->count() === 0);
test("User_id is unique in schedules", Schedule::count() === Schedule::distinct('user_id')->count());
echo "\n";

echo "✅ TEST 4: Attendance Validations\n";
echo str_repeat("─", 60) . "\n";
$attendanceWithUser = Attendance::whereHas('user')->count();
$totalAttendances = Attendance::count();
test("All attendances have valid user", $attendanceWithUser === $totalAttendances);
test("Attendances have start_latitude", Attendance::whereNull('start_latitude')->count() === 0);
test("Attendances have start_longitude", Attendance::whereNull('start_longitude')->count() === 0);
test("Attendances have start_time", Attendance::whereNull('start_time')->count() === 0);
test("Attendances have end_time", Attendance::whereNull('end_time')->count() === 0);

// Check average attendances per employee
$avgAttendance = Attendance::count() / User::role('employee')->count();
test("Average attendance per employee > 15 days", $avgAttendance > 15, ">15", number_format($avgAttendance, 2));
echo "\n";

echo "🏖️  TEST 5: Leave Validations\n";
echo str_repeat("─", 60) . "\n";
$approvedLeaves = Leave::where('status', 'approved')->count();
$pendingLeaves = Leave::where('status', 'pending')->count();
$rejectedLeaves = Leave::where('status', 'rejected')->count();
$totalLeaves = Leave::count();

test("Approved leaves exist", $approvedLeaves > 0, ">0", $approvedLeaves);
test("Pending leaves exist", $pendingLeaves > 0, ">0", $pendingLeaves);
test("Rejected leaves exist", $rejectedLeaves > 0, ">0", $rejectedLeaves);
test("All leaves accounted", ($approvedLeaves + $pendingLeaves + $rejectedLeaves) === $totalLeaves);
test("All leaves have valid user", Leave::whereDoesntHave('user')->count() === 0);
test("All leaves have start_date", Leave::whereNull('start_date')->count() === 0);
test("All leaves have end_date", Leave::whereNull('end_date')->count() === 0);
test("All leaves have reason", Leave::whereNull('reason')->count() === 0);
echo "\n";

echo "🔐 TEST 6: Role & Permission Validations\n";
echo str_repeat("─", 60) . "\n";
$superAdminRole = Role::where('name', 'super_admin')->first();
$adminRole = Role::where('name', 'admin')->first();
$employeeRole = Role::where('name', 'employee')->first();

test("Super admin role exists", $superAdminRole !== null);
test("Admin role exists", $adminRole !== null);
test("Employee role exists", $employeeRole !== null);
test("Super admin has 42 permissions", $superAdminRole->permissions->count() === 42, 42, $superAdminRole->permissions->count());
test("Admin has 21 permissions", $adminRole->permissions->count() === 21, 21, $adminRole->permissions->count());
test("Employee has 5 permissions", $employeeRole->permissions->count() === 5, 5, $employeeRole->permissions->count());
echo "\n";

echo "📍 TEST 7: Office Validations\n";
echo str_repeat("─", 60) . "\n";
test("All offices have name", Office::whereNull('name')->count() === 0);
test("All offices have latitude", Office::whereNull('latitude')->count() === 0);
test("All offices have longitude", Office::whereNull('longitude')->count() === 0);
test("All offices have radius", Office::whereNull('radius')->count() === 0);
test("Jakarta office exists", Office::where('name', 'like', '%Jakarta%')->exists());
test("Bandung office exists", Office::where('name', 'like', '%Bandung%')->exists());
echo "\n";

echo "⏰ TEST 8: Shift Validations\n";
echo str_repeat("─", 60) . "\n";
test("All shifts have name", Shift::whereNull('name')->count() === 0);
test("All shifts have start_time", Shift::whereNull('start_time')->count() === 0);
test("All shifts have end_time", Shift::whereNull('end_time')->count() === 0);
test("Shift Pagi exists", Shift::where('name', 'Shift Pagi')->exists());
test("Shift Malam exists", Shift::where('name', 'Shift Malam')->exists());
echo "\n";

echo "🔗 TEST 9: Relationship Tests\n";
echo str_repeat("─", 60) . "\n";
$testUser = User::role('employee')->first();
test("Employee has schedule", $testUser->schedule !== null);

$testSchedule = Schedule::with(['user', 'shift', 'office'])->first();
test("Schedule has user relationship", $testSchedule->user !== null);
test("Schedule has shift relationship", $testSchedule->shift !== null);
test("Schedule has office relationship", $testSchedule->office !== null);

$testAttendance = Attendance::with('user')->first();
test("Attendance has user relationship", $testAttendance->user !== null);

$testLeave = Leave::with('user')->first();
test("Leave has user relationship", $testLeave->user !== null);
echo "\n";

echo "🔍 TEST 10: Data Integrity\n";
echo str_repeat("─", 60) . "\n";
test("No orphaned schedules", Schedule::whereNotIn('user_id', User::pluck('id'))->count() === 0);
test("No orphaned attendances", Attendance::whereNotIn('user_id', User::pluck('id'))->count() === 0);
test("No orphaned leaves", Leave::whereNotIn('user_id', User::pluck('id'))->count() === 0);
test("All employees have unique emails", User::count() === User::distinct('email')->count());
test("All passwords are hashed", User::where('password', 'password')->count() === 0);
echo "\n";

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                    📊 TEST SUMMARY                          ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "  ✅ Tests Passed: $testsPassed\n";
echo "  ❌ Tests Failed: $testsFailed\n";
echo "  📈 Success Rate: " . number_format(($testsPassed / ($testsPassed + $testsFailed)) * 100, 2) . "%\n";
echo "\n";

if ($testsFailed === 0) {
    echo "  🎉 ALL TESTS PASSED! 🎉\n";
    echo "  ✅ Database seeding is successful and data is valid!\n";
} else {
    echo "  ⚠️  SOME TESTS FAILED!\n";
    echo "  ❌ Please review the failed tests above.\n";
}

echo "\n";
echo "══════════════════════════════════════════════════════════════\n";
echo "\n";

exit($testsFailed === 0 ? 0 : 1);
