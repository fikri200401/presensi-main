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
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PositionHistoryController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\InfoPageController;

// Landing page (public)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Info pages (public)
Route::get('/info/dokumentasi', [InfoPageController::class, 'documentation'])->name('info.documentation');
Route::get('/info/it-support', [InfoPageController::class, 'itSupport'])->name('info.it-support');
Route::get('/info/status-sistem', [InfoPageController::class, 'systemStatus'])->name('info.system-status');
Route::get('/info/privacy-policy', [InfoPageController::class, 'privacyPolicy'])->name('info.privacy-policy');
Route::get('/info/panduan', [InfoPageController::class, 'userGuide'])->name('info.user-guide');

// Guest routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Authenticated routes
Route::middleware('auth')->group(function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('attendance', AttendanceController::class);
    Route::get('attendance-import', [AttendanceController::class, 'showImportForm'])->name('attendance.import.form');
    Route::post('attendance-import', [AttendanceController::class, 'import'])->name('attendance.import');
    Route::get('attendance-import-preview', [AttendanceController::class, 'previewImport'])->name('attendance.import.preview');
    Route::post('attendance-import-confirm', [AttendanceController::class, 'confirmImport'])->name('attendance.import.confirm');
    Route::post('attendance-import-cancel', [AttendanceController::class, 'cancelImport'])->name('attendance.import.cancel');
    Route::get('attendance-import-drafts', [AttendanceController::class, 'showDrafts'])->name('attendance.import.drafts');
    Route::get('attendance-import-draft/{draft}', [AttendanceController::class, 'loadDraft'])->name('attendance.import.draft.load');
    Route::delete('attendance-import-draft/{draft}', [AttendanceController::class, 'deleteDraft'])->name('attendance.import.draft.delete');
    
    Route::resource('leave', LeaveController::class);
    Route::post('leave/{leave}/approve', [LeaveController::class, 'approve'])->name('leave.approve');
    Route::post('leave/{leave}/reject', [LeaveController::class, 'reject'])->name('leave.reject');
    // Multi-layer leave approval routes
    Route::post('leave/{leave}/approve-kadiv', [LeaveController::class, 'approveKadiv'])->name('leave.approve.kadiv');
    Route::post('leave/{leave}/reject-kadiv', [LeaveController::class, 'rejectKadiv'])->name('leave.reject.kadiv');
    Route::post('leave/{leave}/approve-hr', [LeaveController::class, 'approveHr'])->name('leave.approve.hr');
    Route::post('leave/{leave}/reject-hr', [LeaveController::class, 'rejectHr'])->name('leave.reject.hr');
    Route::post('leave/{leave}/approve-direksi', [LeaveController::class, 'approveDireksi'])->name('leave.approve.direksi');
    Route::post('leave/{leave}/reject-direksi', [LeaveController::class, 'rejectDireksi'])->name('leave.reject.direksi');
    Route::resource('office', OfficeController::class);
    Route::resource('schedule', ScheduleController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    Route::resource('division', DivisionController::class)->except(['create', 'show', 'edit']);
    
    // Payroll routes
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::get('payroll/{payroll}/export-pdf', [PayrollController::class, 'exportPdf'])->name('payroll.exportPdf');
    Route::post('payroll/{payroll}/mark-as-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.markAsPaid');
    Route::delete('payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    
    // Employee Salary routes
    Route::resource('employee-salary', EmployeeSalaryController::class);
    
    // Legacy routes for compatibility
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return Excel::download(new AttendanceExport, 'attendances.xlsx');
    })->name('attendance-export');

    // Notification routes
    Route::get('notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');

    // Profile routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Position History routes (admin manages for users)
    Route::post('user/{user}/position-history', [PositionHistoryController::class, 'store'])->name('position-history.store');
    Route::put('position-history/{positionHistory}', [PositionHistoryController::class, 'update'])->name('position-history.update');
    Route::delete('position-history/{positionHistory}', [PositionHistoryController::class, 'destroy'])->name('position-history.destroy');
});

