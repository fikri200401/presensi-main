<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ImportDraft;
use App\Imports\AttendanceImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user.schedule.shift', 'user.schedule.office']);

        // Employee hanya bisa lihat attendance mereka sendiri
        if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $attendances = $query->latest()->paginate(15);

        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::all();
        $schedules = Schedule::with(['shift', 'office'])->get();
        return view('attendance.create', compact('users', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_latitude' => 'required|numeric',
            'schedule_longitude' => 'required|numeric',
            'schedule_start_time' => 'required',
            'schedule_end_time' => 'required',
            'start_latitude' => 'nullable|numeric',
            'start_longitude' => 'nullable|numeric',
            'end_latitude' => 'nullable|numeric',
            'end_longitude' => 'nullable|numeric',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance created successfully');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        $schedules = Schedule::with(['shift', 'office'])->get();
        return view('attendance.edit', compact('attendance', 'users', 'schedules'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'schedule_latitude' => 'required|numeric',
            'schedule_longitude' => 'required|numeric',
            'schedule_start_time' => 'required',
            'schedule_end_time' => 'required',
            'start_latitude' => 'nullable|numeric',
            'start_longitude' => 'nullable|numeric',
            'end_latitude' => 'nullable|numeric',
            'end_longitude' => 'nullable|numeric',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('attendance.import');
    }

    /**
     * Import attendance from Excel file (fingerprint machine format)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240' // Max 10MB
        ]);

        try {
            $file = $request->file('file');
            
            // Create import instance with preview mode
            $import = new AttendanceImport();
            $import->setPreviewMode(true);
            
            // Import the file
            Excel::import($import, $file);
            
            // Get preview data
            $previewData = $import->getPreviewData();
            
            // Debug: Check if data exists
            if (empty($previewData)) {
                $errors = $import->getResults()['errors'] ?? [];
                $errorMessage = !empty($errors) 
                    ? 'Error: ' . implode(', ', $errors) 
                    : 'Tidak ada data yang dapat diimport dari file Excel. Pastikan format file sesuai.';
                
                return redirect()->route('attendance.import.form')
                    ->with('error', $errorMessage);
            }
            
            // Save to session for preview
            Session::put('import_preview_data', $previewData);
            
            return redirect()->route('attendance.import.preview')
                ->with('success', 'File berhasil diproses. Silakan review data sebelum import.');
                
        } catch (\Exception $e) {
            return redirect()->route('attendance.import.form')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Show preview of import data
     */
    public function previewImport()
    {
        $previewData = Session::get('import_preview_data', []);
        
        if (empty($previewData)) {
            return redirect()->route('attendance.import.form')
                ->with('error', 'Tidak ada data preview. Silakan upload file terlebih dahulu.');
        }

        return view('attendance.preview', compact('previewData'));
    }

    /**
     * Confirm and save imported data
     */
    public function confirmImport(Request $request)
    {
        $previewData = Session::get('import_preview_data', []);
        
        if (empty($previewData)) {
            return redirect()->route('attendance.import.form')
                ->with('error', 'Tidak ada data untuk diimport.');
        }

        // Get edited data from form
        $editedData = $request->input('attendance', []);

        $successCount = 0;
        $failedCount = 0;
        $errors = [];
        $details = [];
        $failedData = [];

        foreach ($editedData as $index => $row) {
            // Skip if checkbox not checked
            if (!isset($row['import']) || $row['import'] != '1') {
                continue;
            }

            try {
                // Find user
                $user = User::find($row['user_id']);
                
                if (!$user) {
                    $failedCount++;
                    $error = "User tidak ditemukan";
                    $errors[] = "Baris " . ($index + 1) . ": " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => 'Unknown',
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'user_id'
                    ]);
                    continue;
                }

                // Validate date
                if (empty($row['date'])) {
                    $failedCount++;
                    $error = "Tanggal tidak boleh kosong";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'date'
                    ]);
                    continue;
                }

                // Validate date format
                try {
                    $dateObj = \Carbon\Carbon::parse($row['date']);
                } catch (\Exception $e) {
                    $failedCount++;
                    $error = "Format tanggal tidak valid";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'date'
                    ]);
                    continue;
                }

                // Validate check in time
                if (empty($row['check_in'])) {
                    $failedCount++;
                    $error = "Waktu check in tidak boleh kosong";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'check_in'
                    ]);
                    continue;
                }

                // Validate check in time format
                try {
                    $checkInTime = \Carbon\Carbon::parse($row['check_in']);
                } catch (\Exception $e) {
                    $failedCount++;
                    $error = "Format waktu check in tidak valid";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'check_in'
                    ]);
                    continue;
                }

                // Validate check out time format if provided
                if (!empty($row['check_out'])) {
                    try {
                        $checkOutTime = \Carbon\Carbon::parse($row['check_out']);
                    } catch (\Exception $e) {
                        $failedCount++;
                        $error = "Format waktu check out tidak valid";
                        $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                        $failedData[] = array_merge($row, [
                            'user_name' => $user->name,
                            'error' => $error,
                            'row_index' => $index,
                            'error_field' => 'check_out'
                        ]);
                        continue;
                    }
                }

                // Get user's schedule for latitude/longitude
                $schedule = $user->schedule()->first();
                
                if (!$schedule) {
                    $failedCount++;
                    $error = "User tidak memiliki jadwal. Silakan atur jadwal terlebih dahulu";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'schedule'
                    ]);
                    continue;
                }

                // Check if office exists
                if (!$schedule->office) {
                    $failedCount++;
                    $error = "Jadwal user tidak memiliki office. Silakan atur office terlebih dahulu";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'office'
                    ]);
                    continue;
                }

                // Check if shift exists
                if (!$schedule->shift) {
                    $failedCount++;
                    $error = "Jadwal user tidak memiliki shift. Silakan atur shift terlebih dahulu";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'shift'
                    ]);
                    continue;
                }

                // All validations passed, now check if attendance exists
                try {
                    $attendance = Attendance::where('user_id', $user->id)
                        ->where('date', $row['date'])
                        ->first();
                } catch (\Exception $e) {
                    $failedCount++;
                    $error = "Gagal memeriksa data: Format tanggal tidak valid";
                    $errors[] = "Baris " . ($index + 1) . " ({$user->name}): " . $error;
                    $failedData[] = array_merge($row, [
                        'user_name' => $user->name,
                        'error' => $error,
                        'row_index' => $index,
                        'error_field' => 'date'
                    ]);
                    continue;
                }

                $data = [
                    'user_id' => $user->id,
                    'date' => $row['date'],
                    'schedule_latitude' => $schedule->office->latitude,
                    'schedule_longitude' => $schedule->office->longitude,
                    'schedule_start_time' => $schedule->shift->start_time,
                    'schedule_end_time' => $schedule->shift->end_time,
                    'start_latitude' => $row['schedule_latitude'] ?? 0,
                    'start_longitude' => $row['schedule_longitude'] ?? 0,
                    'end_latitude' => $row['schedule_latitude'] ?? 0,
                    'end_longitude' => $row['schedule_longitude'] ?? 0,
                    'start_time' => $row['check_in'],
                    'end_time' => $row['check_out'] ?? null,
                ];

                if ($attendance) {
                    $attendance->update($data);
                    $details[] = "Updated: {$user->name} - " . date('d/m/Y', strtotime($row['date']));
                } else {
                    Attendance::create($data);
                    $details[] = "Created: {$user->name} - " . date('d/m/Y', strtotime($row['date']));
                }

                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                // Parse error message to make it user-friendly
                $errorMsg = $e->getMessage();
                
                if (str_contains($errorMsg, 'Column not found')) {
                    $error = "Terjadi kesalahan struktur database. Hubungi administrator";
                } elseif (str_contains($errorMsg, 'Duplicate entry')) {
                    $error = "Data absensi untuk tanggal ini sudah ada";
                } elseif (str_contains($errorMsg, 'Data too long')) {
                    $error = "Data terlalu panjang untuk disimpan";
                } elseif (str_contains($errorMsg, 'Incorrect datetime')) {
                    $error = "Format tanggal atau waktu tidak valid";
                } else {
                    $error = "Gagal menyimpan: " . $errorMsg;
                }
                
                $errors[] = "Baris " . ($index + 1) . " (" . ($user->name ?? 'Unknown') . "): " . $error;
                $failedData[] = array_merge($row, [
                    'user_name' => $user->name ?? 'Unknown',
                    'error' => $error,
                    'row_index' => $index
                ]);
            }
        }

        // If there are failures, save as draft and redirect back to preview
        if ($failedCount > 0) {
            // Save draft
            $draft = ImportDraft::create([
                'user_id' => auth()->id(),
                'name' => 'Import Attendance ' . now()->format('d/m/Y H:i'),
                'type' => 'attendance',
                'preview_data' => $failedData,
                'failed_data' => $failedData,
                'errors' => $errors,
                'total_rows' => $successCount + $failedCount,
                'success_rows' => $successCount,
                'failed_rows' => $failedCount,
                'status' => $successCount > 0 ? 'partial' : 'draft',
            ]);

            // Keep failed data in session for preview
            Session::put('import_preview_data', $failedData);
            Session::put('import_draft_id', $draft->id);

            return redirect()->route('attendance.import.preview')->with([
                'error' => "Import selesai dengan {$failedCount} data gagal. Silakan perbaiki data yang gagal di bawah ini.",
                'import_success' => $successCount,
                'import_failed' => $failedCount,
                'import_errors' => $errors,
            ]);
        }

        // Clear session if all success
        Session::forget('import_preview_data');
        Session::forget('import_draft_id');

        return redirect()->route('attendance.index')->with([
            'success' => "Import selesai! Berhasil: {$successCount}, Gagal: {$failedCount}",
            'import_details' => $details,
        ]);
    }

    /**
     * Cancel import and clear session
     */
    public function cancelImport()
    {
        Session::forget('import_preview_data');
        Session::forget('import_draft_id');
        return redirect()->route('attendance.import.form')
            ->with('success', 'Import dibatalkan.');
    }

    /**
     * Show import drafts
     */
    public function showDrafts()
    {
        $drafts = ImportDraft::where('user_id', auth()->id())
            ->where('type', 'attendance')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('attendance.drafts', compact('drafts'));
    }

    /**
     * Load draft for editing
     */
    public function loadDraft(ImportDraft $draft)
    {
        if ($draft->user_id !== auth()->id()) {
            abort(403);
        }

        Session::put('import_preview_data', $draft->failed_data ?? $draft->preview_data);
        Session::put('import_draft_id', $draft->id);

        return redirect()->route('attendance.import.preview')
            ->with('success', 'Draft berhasil dimuat. Silakan perbaiki dan import ulang.');
    }

    /**
     * Delete draft
     */
    public function deleteDraft(ImportDraft $draft)
    {
        if ($draft->user_id !== auth()->id()) {
            abort(403);
        }

        $draft->delete();

        return redirect()->route('attendance.import.drafts')
            ->with('success', 'Draft berhasil dihapus.');
    }
}
