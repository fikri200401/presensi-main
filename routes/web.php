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

// Guest routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Authenticated routes
Route::middleware('auth')->group(function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
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
    Route::resource('office', OfficeController::class);
    Route::resource('schedule', ScheduleController::class);
    Route::resource('shift', ShiftController::class);
    Route::resource('user', UserController::class);
    Route::resource('role', RoleController::class);
    
    // Payroll routes
    Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('payroll/create', [PayrollController::class, 'create'])->name('payroll.create');
    Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
    Route::get('payroll/{payroll}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::get('payroll/{payroll}/export-pdf', [PayrollController::class, 'exportPdf'])->name('payroll.exportPdf');
    Route::post('payroll/{payroll}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');
    Route::post('payroll/{payroll}/reject', [PayrollController::class, 'reject'])->name('payroll.reject');
    Route::post('payroll/{payroll}/mark-as-paid', [PayrollController::class, 'markAsPaid'])->name('payroll.markAsPaid');
    Route::delete('payroll/{payroll}', [PayrollController::class, 'destroy'])->name('payroll.destroy');
    
    // Employee Salary routes
    Route::resource('employee-salary', EmployeeSalaryController::class);
    
    // Legacy routes for compatibility
    Route::get('presensi', Presensi::class)->name('presensi');
    Route::get('attendance/export', function () {
        return Excel::download(new AttendanceExport, 'attendances.xlsx');
    })->name('attendance-export');
});

