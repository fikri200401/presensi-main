# Dokumentasi Sistem Payroll

## Overview
Sistem payroll yang telah diimplementasikan mendukung perhitungan gaji berdasarkan peraturan Ketenagakerjaan Indonesia (KEP. 102/MEN/VI/2004) dengan formula:
**Gaji per jam = 1/173 × Gaji Sebulan**

## Database Structure

### 1. salary_settings (Pengaturan Gaji Global)
Tabel ini menyimpan konfigurasi global untuk perhitungan gaji (singleton pattern).

**Fields:**
- `jam_kerja_per_hari` - Default: 8 jam
- `hari_kerja_per_minggu` - Default: 5 hari
- `hari_kerja_per_bulan` - Default: 21 hari
- `total_jam_per_bulan` - Default: 173 jam (sesuai KEP. 102/MEN/VI/2004)
- `metode_perhitungan_default` - Enum: 'bulanan', 'harian', 'jam'
- `tunjangan_transport_default` - Default tunjangan transportasi
- `tunjangan_makan_default` - Default tunjangan makan
- `potongan_bpjs_kesehatan_persen` - Default: 1% dari gaji
- `potongan_bpjs_ketenagakerjaan_persen` - Default: 2% dari gaji
- `catatan` - Catatan tambahan

### 2. employee_salaries (Konfigurasi Gaji Karyawan)
Tabel ini menyimpan konfigurasi gaji individual untuk setiap karyawan.

**Fields:**
- `user_id` - Foreign key ke users (unique)
- `gaji_pokok_bulanan` - Gaji pokok per bulan
- `gaji_per_jam` - Auto-calculated: (1/173) × gaji_pokok_bulanan
- `gaji_per_hari` - Auto-calculated: (1/21) × gaji_pokok_bulanan
- `tipe_karyawan` - Enum: 'tetap', 'harian', 'paruh_waktu'
- `metode_perhitungan` - Enum: 'bulanan', 'harian', 'jam'
- `tunjangan_transport` - Tunjangan transportasi
- `tunjangan_makan` - Tunjangan makan
- `tunjangan_jabatan` - Tunjangan jabatan
- `tunjangan_keluarga` - Tunjangan keluarga
- `tunjangan_lainnya` - Tunjangan lainnya
- `potongan_bpjs_kesehatan_persen` - Persentase BPJS Kesehatan
- `potongan_bpjs_ketenagakerjaan_persen` - Persentase BPJS Ketenagakerjaan
- `potongan_pph21` - PPH21
- `potongan_lainnya` - Potongan lainnya
- `is_active` - Status aktif/nonaktif
- `berlaku_dari` - Tanggal mulai berlaku

**Auto-calculation:**
Model `EmployeeSalary` otomatis menghitung `gaji_per_jam` dan `gaji_per_hari` saat `gaji_pokok_bulanan` diubah menggunakan method `calculateRates()`.

### 3. payrolls (Data Payroll Bulanan)
Tabel ini menyimpan record payroll per karyawan per bulan.

**Fields:**
- `user_id` - Foreign key ke users
- `periode` - Format: YYYY-MM (e.g., '2026-02')
- `bulan` - Bulan (1-12)
- `tahun` - Tahun
- `total_hari_kerja` - Total hari kerja dalam bulan tersebut
- `total_hari_hadir` - Total hari kehadiran dari attendance
- `total_jam_kerja` - Total jam kerja standar
- `total_jam_hadir` - Total jam hadir dari attendance
- `total_terlambat` - Total keterlambatan
- `gaji_pokok` - Gaji pokok (calculated based on metode_perhitungan)
- `gaji_per_hari` - Gaji per hari
- `gaji_per_jam` - Gaji per jam
- `tunjangan_transport` - Tunjangan transportasi
- `tunjangan_makan` - Tunjangan makan
- `tunjangan_lainnya` - Tunjangan lainnya
- `total_tunjangan` - Total semua tunjangan
- `potongan_bpjs_kesehatan` - Potongan BPJS Kesehatan
- `potongan_bpjs_ketenagakerjaan` - Potongan BPJS Ketenagakerjaan
- `potongan_pph21` - Potongan PPH21
- `potongan_keterlambatan` - Potongan karena terlambat
- `potongan_lainnya` - Potongan lainnya
- `total_potongan` - Total semua potongan
- `gaji_kotor` - Gaji pokok + total tunjangan
- `gaji_bersih` - Gaji kotor - total potongan (take home pay)
- `status` - Enum: 'draft', 'pending', 'approved', 'paid', 'rejected'
- `approved_by` - Foreign key ke users (approver)
- `approved_at` - Tanggal approval
- `catatan` - Catatan (untuk rejection reason atau lainnya)

**Unique constraint:** `user_id` + `periode` (satu karyawan hanya bisa punya 1 payroll per bulan)

## Models

### 1. SalarySetting Model
- **Location:** `app/Models/SalarySetting.php`
- **Features:**
  - Singleton pattern dengan method `getSettings()`
  - Auto-create default settings jika belum ada
  - Cast semua field decimal ke tipe `decimal:2`

### 2. EmployeeSalary Model
- **Location:** `app/Models/EmployeeSalary.php`
- **Features:**
  - Method `calculateRates()` - Auto-calculate gaji per jam dan per hari
  - Accessor `getTotalTunjanganAttribute()` - Hitung total tunjangan
  - Accessor `getTotalPotonganAttribute()` - Hitung total potongan
  - Relationship `user()` - Belongs to User

### 3. Payroll Model
- **Location:** `app/Models/Payroll.php`
- **Features:**
  - Relationship `user()` - Belongs to User
  - Relationship `approver()` - Belongs to User (approved_by)
  - Accessor `getPeriodeNameAttribute()` - Format periode (e.g., "Januari 2026")
  - Cast semua field currency ke `decimal:2`

## Controller: PayrollController

### Routes
```php
GET  /payroll                      - payroll.index       - Daftar semua payroll
GET  /payroll/create               - payroll.create      - Form generate payroll
POST /payroll/generate             - payroll.generate    - Generate payroll untuk karyawan
GET  /payroll/{payroll}            - payroll.show        - Detail slip gaji
POST /payroll/{payroll}/approve    - payroll.approve     - Approve payroll
POST /payroll/{payroll}/reject     - payroll.reject      - Reject payroll (dengan alasan)
POST /payroll/{payroll}/mark-as-paid - payroll.markAsPaid - Tandai sebagai sudah dibayar
DELETE /payroll/{payroll}          - payroll.destroy     - Hapus draft payroll
```

### Methods

#### 1. `index(Request $request)`
Menampilkan daftar payroll dengan filter status dan periode.

#### 2. `create()`
Menampilkan form untuk memilih karyawan dan periode untuk generate payroll.

#### 3. `generate(Request $request)`
**Process:**
1. Validasi input (periode dan user_ids)
2. Loop setiap karyawan yang dipilih:
   - Cek apakah payroll sudah ada untuk periode tersebut
   - Ambil konfigurasi gaji karyawan dari `employee_salaries`
   - Ambil data attendance untuk periode tersebut
   - Hitung total hari hadir, jam hadir, dan keterlambatan
   - Hitung gaji pokok berdasarkan metode perhitungan:
     - **Bulanan:** Gaji pokok = gaji_pokok_bulanan
     - **Harian:** Gaji pokok = gaji_per_hari × total_hari_hadir
     - **Jam:** Gaji pokok = gaji_per_jam × total_jam_hadir
   - Hitung total tunjangan (transport + makan + lainnya)
   - Hitung potongan:
     - BPJS Kesehatan = gaji_pokok × (persen / 100)
     - BPJS Ketenagakerjaan = gaji_pokok × (persen / 100)
     - PPH21 (dari konfigurasi karyawan)
     - Keterlambatan (dapat di-customize)
     - Lainnya
   - Hitung gaji kotor = gaji_pokok + total_tunjangan
   - Hitung gaji bersih = gaji_kotor - total_potongan
   - Simpan payroll dengan status 'draft'

#### 4. `show(Payroll $payroll)`
Menampilkan detail slip gaji.

#### 5. `approve(Payroll $payroll)`
Approve payroll (ubah status menjadi 'approved', simpan approver dan waktu approval).

#### 6. `reject(Request $request, Payroll $payroll)`
Reject payroll dengan alasan (minimum 10 karakter).

#### 7. `markAsPaid(Payroll $payroll)`
Tandai payroll sebagai sudah dibayar (hanya untuk status 'approved').

#### 8. `destroy(Payroll $payroll)`
Hapus payroll (hanya untuk status 'draft').

## Workflow Status Payroll

```
draft → (approve) → approved → (mark as paid) → paid
  ↓
(reject) → rejected
```

- **draft** - Payroll baru di-generate, belum disetujui
- **pending** - Menunggu approval (opsional, bisa skip)
- **approved** - Sudah disetujui, siap dibayar
- **paid** - Sudah dibayar
- **rejected** - Ditolak (dengan catatan alasan)

## Formula Perhitungan

### 1. Gaji Per Jam (KEP. 102/MEN/VI/2004)
```
Gaji per jam = (1/173) × Gaji Pokok Bulanan
```

### 2. Gaji Per Hari
```
Gaji per hari = (1/21) × Gaji Pokok Bulanan
```

### 3. Gaji Bulanan
**Metode Bulanan:**
```
Gaji Pokok = Gaji Pokok Bulanan (tetap, tidak peduli hari hadir)
```

**Metode Harian:**
```
Gaji Pokok = Gaji per hari × Total Hari Hadir
```

**Metode Jam:**
```
Gaji Pokok = Gaji per jam × Total Jam Hadir
```

### 4. Total Tunjangan
```
Total Tunjangan = Tunjangan Transport + Tunjangan Makan + Tunjangan Lainnya
```

### 5. Total Potongan
```
Potongan BPJS Kesehatan = Gaji Pokok × (1% / 100)
Potongan BPJS Ketenagakerjaan = Gaji Pokok × (2% / 100)
Total Potongan = BPJS Kesehatan + BPJS Ketenagakerjaan + PPH21 + Keterlambatan + Lainnya
```

### 6. Gaji Kotor dan Bersih
```
Gaji Kotor = Gaji Pokok + Total Tunjangan
Gaji Bersih (Take Home Pay) = Gaji Kotor - Total Potongan
```

## Integrasi dengan Attendance System

Payroll system terintegrasi dengan tabel `attendances` untuk mengambil data:
- Total hari hadir (count attendance dengan status 'hadir')
- Total jam hadir (sum dari selisih jam_masuk dan jam_keluar)
- Total keterlambatan (count attendance dengan is_late = true)

Data ini digunakan untuk perhitungan gaji (terutama untuk metode harian dan jam).

## Next Steps (Belum Diimplementasikan)

1. **Views/UI:**
   - `resources/views/payroll/index.blade.php` - Daftar payroll
   - `resources/views/payroll/create.blade.php` - Form generate payroll
   - `resources/views/payroll/show.blade.php` - Slip gaji detail
   - `resources/views/salary-settings/edit.blade.php` - Edit global settings
   - `resources/views/employee-salary/index.blade.php` - Manage employee salaries

2. **Controllers untuk Settings:**
   - `SalarySettingController` - Manage global salary settings
   - `EmployeeSalaryController` - Manage individual employee salaries

3. **PDF Generation:**
   - Generate slip gaji dalam format PDF
   - Download atau email slip gaji ke karyawan

4. **Permissions/Policies:**
   - PayrollPolicy - Control access to payroll features
   - Only HR/admin can generate, approve, or reject payrolls
   - Employees can only view their own payroll

5. **Additional Features:**
   - Export payroll to Excel
   - Bulk approval
   - Email notification saat payroll approved
   - History perubahan salary settings
   - Report: Total gaji per bulan, per departemen, dll.

## Usage Example

### 1. Setup Salary Settings (One-time)
```bash
php artisan db:seed --class=SalarySettingSeeder
```

### 2. Create Employee Salary Configuration
```php
EmployeeSalary::create([
    'user_id' => 1,
    'gaji_pokok_bulanan' => 5000000,
    'tipe_karyawan' => 'tetap',
    'metode_perhitungan' => 'bulanan',
    'tunjangan_transport' => 500000,
    'tunjangan_makan' => 750000,
    'potongan_bpjs_kesehatan_persen' => 1.00,
    'potongan_bpjs_ketenagakerjaan_persen' => 2.00,
    'is_active' => true,
    'berlaku_dari' => '2026-01-01',
]);
```

Model akan otomatis menghitung:
- `gaji_per_jam` = 5000000 / 173 = 28,901.73
- `gaji_per_hari` = 5000000 / 21 = 238,095.24

### 3. Generate Payroll
Akses `/payroll/create`, pilih karyawan dan periode (e.g., 2026-02), submit form.

System akan:
- Mengambil attendance data untuk Februari 2026
- Menghitung gaji berdasarkan konfigurasi
- Membuat record payroll dengan status 'draft'

### 4. Approve Payroll
Akses detail payroll, klik tombol "Approve". Status berubah menjadi 'approved'.

### 5. Mark as Paid
Setelah transfer gaji, klik "Mark as Paid". Status berubah menjadi 'paid'.

## Database Migrations Status

✅ Migrations berhasil dijalankan:
- `2026_02_08_030504_create_salary_settings_table`
- `2026_02_08_030517_create_employee_salaries_table`
- `2026_02_08_030530_create_payrolls_table`

✅ Seeder berhasil dijalankan:
- `SalarySettingSeeder` - Default settings telah dibuat

## Notes

- Formula 1/173 sesuai dengan KEP. 102/MEN/VI/2004
- Default 173 jam = 21 hari × 8 jam + 5 jam (hari Sabtu)
- BPJS Kesehatan: 1% dari gaji (ditanggung karyawan)
- BPJS Ketenagakerjaan: 2% dari gaji (ditanggung karyawan)
- Potongan keterlambatan dapat di-customize sesuai kebijakan perusahaan
- Status workflow: draft → approved → paid (atau rejected)
