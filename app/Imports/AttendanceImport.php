<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AttendanceImport implements ToCollection
{
    protected $previewMode = false;
    protected $previewData = [];
    
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'skipped' => 0,
        'errors' => [],
        'details' => []
    ];

    protected $dateHeaders = [];

    /**
     * Set preview mode
     */
    public function setPreviewMode($mode = true)
    {
        $this->previewMode = $mode;
    }

    /**
     * Get preview data
     */
    public function getPreviewData()
    {
        return $this->previewData;
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        // Structure based on solution.co.id fingerprint export (actual format):
        // Row 0-2: Headers
        // Row 3: "Nama" with dates (24/11, 25/11, 26/11, etc.)
        // Row 4+: May have company name or empty rows
        // Then: Employee data rows

        if ($rows->count() < 5) {
            $this->results['errors'][] = "File format tidak valid. Minimal harus ada 5 baris. Jumlah baris: " . $rows->count();
            return;
        }

        // Find the header row with "Nama" and dates
        $headerRowIndex = null;
        $headerRow = null;
        
        foreach ($rows as $index => $row) {
            // Check if first column contains "Nama" (case insensitive)
            if (!empty($row[0]) && strtolower(trim($row[0])) === 'nama') {
                $headerRowIndex = $index;
                $headerRow = $row;
                break;
            }
        }

        if (!$headerRow) {
            $this->results['errors'][] = "Tidak ditemukan baris header dengan kolom 'Nama'. Pastikan ada baris dengan 'Nama' di kolom pertama.";
            return;
        }

        // Extract date headers from the header row
        $this->dateHeaders = $this->extractDateHeaders($headerRow);

        if (empty($this->dateHeaders)) {
            $this->results['errors'][] = "Tidak ada tanggal yang valid di baris header (baris " . ($headerRowIndex + 1) . "). Pastikan format tanggal DD/MM atau DD/MM/YYYY";
            return;
        }

        // Process employee rows (after header row)
        $processedCount = 0;
        foreach ($rows as $rowIndex => $row) {
            // Skip rows before and including header
            if ($rowIndex <= $headerRowIndex) {
                continue;
            }

            // Skip empty rows
            if (empty($row[0]) || trim($row[0]) == '') {
                continue;
            }

            // Skip company/organization name rows (usually all caps or contains "PT", "CV", etc)
            $firstCell = trim($row[0]);
            if (preg_match('/^(PT|CV|UD|YAYASAN|FOUNDATION)\s/i', $firstCell)) {
                continue;
            }

            $this->processEmployeeRow($row, $rowIndex);
            $processedCount++;
        }

        if ($processedCount == 0) {
            $this->results['errors'][] = "Tidak ada data karyawan yang ditemukan setelah baris header";
        }
    }

    /**
     * Extract date headers from header row
     */
    protected function extractDateHeaders($headerRow)
    {
        $dates = [];
        
        foreach ($headerRow as $colIndex => $cell) {
            // Skip first column (Nama)
            if ($colIndex == 0) {
                continue;
            }

            if (empty($cell)) {
                continue;
            }

            // Try to parse date from various formats
            $dateString = trim($cell);
            
            try {
                // Check if it's Excel date serial number
                if (is_numeric($dateString) && $dateString > 40000) {
                    $date = Date::excelToDateTimeObject($dateString);
                    $carbonDate = Carbon::instance($date);
                } 
                // DD/MM format (e.g., 24/11, 25/11)
                elseif (preg_match('/^(\d{1,2})\/(\d{1,2})$/', $dateString, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = date('Y');
                    $carbonDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                } 
                // DD/MM/YYYY format (e.g., 24/11/2025)
                elseif (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/', $dateString, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = strlen($matches[3]) == 2 ? '20' . $matches[3] : $matches[3];
                    $carbonDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                }
                // DD-MMM format (e.g., 12-Jan, 12-Feb)
                elseif (preg_match('/^(\d{1,2})-([A-Za-z]{3})$/', $dateString, $matches)) {
                    $day = $matches[1];
                    $monthName = $matches[2];
                    $year = date('Y');
                    $carbonDate = Carbon::createFromFormat('d-M-Y', "{$day}-{$monthName}-{$year}");
                }
                // D/M format (e.g., 1/12, 2/12)
                elseif (preg_match('/^(\d{1,2})\/(\d{1,2})$/', $dateString, $matches)) {
                    $day = $matches[1];
                    $month = $matches[2];
                    $year = date('Y');
                    $carbonDate = Carbon::createFromFormat('Y-m-d', "{$year}-{$month}-{$day}");
                }
                // Other formats - let Carbon try to parse
                else {
                    $carbonDate = Carbon::parse($dateString);
                }

                $dates[$colIndex] = [
                    'date' => $carbonDate,
                    'formatted' => $carbonDate->format('Y-m-d')
                ];
            } catch (\Exception $e) {
                // Skip invalid date columns
                continue;
            }
        }

        return $dates;
    }

    /**
     * Process individual employee row
     */
    protected function processEmployeeRow($row, $rowIndex)
    {
        $employeeName = trim($row[0]);
        
        // Find user by name
        $user = User::where('name', 'LIKE', "%{$employeeName}%")->first();
        
        // If preview mode and user not found, still add to preview with error flag
        if (!$user && $this->previewMode) {
            // Try to get any user as fallback for preview
            $fallbackUser = User::first();
            if (!$fallbackUser) {
                $this->results['errors'][] = "Baris {$rowIndex}: Karyawan '{$employeeName}' tidak ditemukan dan tidak ada user di sistem";
                return;
            }
            $user = $fallbackUser;
            $userNotFound = true;
        } elseif (!$user) {
            $this->results['failed']++;
            $this->results['errors'][] = "Baris {$rowIndex}: Karyawan '{$employeeName}' tidak ditemukan";
            return;
        } else {
            $userNotFound = false;
        }

        // Get user's schedule (no is_active column in schedules table)
        $schedule = Schedule::where('user_id', $user->id)->first();

        // Process each date column
        foreach ($this->dateHeaders as $colIndex => $dateInfo) {
            if (!isset($row[$colIndex]) || empty($row[$colIndex])) {
                continue;
            }

            $timeValue = trim($row[$colIndex]);
            
            // Skip if no time data
            if (empty($timeValue) || $timeValue == '-') {
                continue;
            }

            $this->processAttendance($user, $dateInfo, $timeValue, $schedule, $employeeName, $userNotFound ?? false);
        }
    }

    /**
     * Process individual attendance record
     */
    protected function processAttendance($user, $dateInfo, $timeValue, $schedule, $originalName = null, $userNotFound = false)
    {
        try {
            $date = $dateInfo['date'];
            $times = $this->parseTimeValue($timeValue);

            if (empty($times)) {
                $this->results['skipped']++;
                return;
            }

            // If preview mode, just collect data without saving
            if ($this->previewMode) {
                $this->previewData[] = [
                    'user_id' => $user->id,
                    'user_name' => $originalName ?? $user->name,
                    'user_matched' => !$userNotFound,
                    'date' => $date->format('Y-m-d'),
                    'date_formatted' => $date->format('d/m/Y'),
                    'check_in' => $times['start_time'] ?? null,
                    'check_out' => $times['end_time'] ?? null,
                    'schedule_latitude' => optional($schedule)->office->latitude ?? null,
                    'schedule_longitude' => optional($schedule)->office->longitude ?? null,
                    'raw_time' => $timeValue,
                ];
                return;
            }

            // Check if attendance already exists for this date
            $existing = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->first();

            $attendanceData = [
                'start_time' => $times['start_time'] ?? null,
                'end_time' => $times['end_time'] ?? null,
                'schedule_start_time' => optional($schedule)->shift->start_time ?? null,
                'schedule_end_time' => optional($schedule)->shift->end_time ?? null,
            ];

            if ($existing) {
                // Update existing attendance
                $existing->update($attendanceData);
                
                $this->results['success']++;
                $this->results['details'][] = "Updated: {$user->name} - {$date->format('d/m/Y')} - {$timeValue}";
            } else {
                // Create new attendance with proper created_at timestamp
                $startTime = $times['start_time'] ?? '08:00:00';
                $timeParts = explode(':', $startTime);
                
                $createdAt = $date->copy()
                    ->setHour((int)$timeParts[0])
                    ->setMinute((int)$timeParts[1])
                    ->setSecond((int)($timeParts[2] ?? 0));

                Attendance::create([
                    'user_id' => $user->id,
                    'created_at' => $createdAt,
                    'start_time' => $attendanceData['start_time'],
                    'end_time' => $attendanceData['end_time'],
                    'schedule_start_time' => $attendanceData['schedule_start_time'],
                    'schedule_end_time' => $attendanceData['schedule_end_time'],
                    'schedule_latitude' => optional($schedule)->office->latitude ?? null,
                    'schedule_longitude' => optional($schedule)->office->longitude ?? null,
                    'latlong' => null, // Imported data doesn't have location
                ]);

                $this->results['success']++;
                $this->results['details'][] = "Created: {$user->name} - {$date->format('d/m/Y')} - {$timeValue}";
            }
        } catch (\Exception $e) {
            $this->results['failed']++;
            $this->results['errors'][] = "Error: {$user->name} - {$dateInfo['formatted']}: " . $e->getMessage();
        }
    }

    /**
     * Parse time value from Excel cell
     * Formats: "07:00", "07:00 17:00", "07:00-17:00", "07:00\n17:00", multiple times in one cell
     */
    protected function parseTimeValue($value)
    {
        $value = trim($value);
        
        // Replace newlines and hyphens with spaces
        $value = str_replace(["\n", "\r", '-'], ' ', $value);
        
        // Remove multiple spaces
        $value = preg_replace('/\s+/', ' ', $value);
        
        // Split by whitespace to get individual times
        $times = explode(' ', $value);
        
        // Filter valid times only (HH:MM format)
        $validTimes = array_filter($times, function($time) {
            $time = trim($time);
            return !empty($time) && preg_match('/^\d{1,2}:\d{2}$/', $time);
        });

        // Re-index array
        $validTimes = array_values($validTimes);

        if (empty($validTimes)) {
            return [];
        }

        $result = [];
        
        // First time is check-in
        $result['start_time'] = $this->formatTime($validTimes[0]);
        
        // Last time is check-out (if exists and different from first)
        if (count($validTimes) > 1) {
            $result['end_time'] = $this->formatTime($validTimes[count($validTimes) - 1]);
        }
        
        return $result;
    }

    /**
     * Format time string to HH:MM:SS
     */
    protected function formatTime($time)
    {
        try {
            $time = trim($time);
            
            // If already in HH:MM:SS format
            if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) {
                return $time;
            }
            
            // If in HH:MM format
            if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                return $time . ':00';
            }
            
            // Try parsing with Carbon
            $parsed = Carbon::createFromFormat('H:i', $time);
            return $parsed->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get import results
     */
    public function getResults()
    {
        return $this->results;
    }
}
