<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Presensi;
use App\Exports\AttendanceExport;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

// Guest routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Authenticated routes
Route::middleware('auth')->group(function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('attendance', AttendanceController::class);
    Route::resource('leave', LeaveController::class);
    Route::resource('office', OfficeController::class);
    Route::resource('schedule', ScheduleController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    
    // Legacy routes for compatibility
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return Excel::download(new AttendanceExport, 'attendances.xlsx');
    })->name('attendance-export');
});

