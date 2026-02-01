# Database Seeders - Sistem Presensi

Dokumentasi lengkap untuk database seeders sistem presensi karyawan.

## 📋 Daftar Isi

- [Overview](#overview)
- [Struktur Seeder](#struktur-seeder)
- [Data yang Dibuat](#data-yang-dibuat)
- [Cara Menjalankan](#cara-menjalankan)
- [Testing & Verifikasi](#testing--verifikasi)

## 🎯 Overview

Sistem seeding ini telah dirancang untuk membuat data dummy yang realistis untuk aplikasi presensi, mencakup:
- User dengan berbagai role (Super Admin, Admin, Employee)
- Kantor cabang di berbagai kota
- Shift kerja yang bervariasi
- Jadwal kerja karyawan (termasuk WFA)
- Data presensi 30 hari terakhir
- Pengajuan cuti dengan berbagai status

## 📁 Struktur Seeder

### 1. **RolePermissionSeeder.php**
Membuat roles dan permissions menggunakan Spatie Laravel Permission:
- **Roles**: `super_admin`, `admin`, `employee`
- **Permissions**: 42 permissions untuk CRUD operations pada semua resources
- Super Admin memiliki semua permissions
- Admin memiliki permissions terbatas
- Employee hanya bisa view dan create data sendiri

### 2. **UserSeeder.php**
Membuat 22 users:
- 1 Super Admin
- 1 Admin
- 20 Employees

**Credentials:**
```
Super Admin: superadmin@presensi.com / password
Admin: admin@presensi.com / password
Employees: {firstname}.{lastname}@presensi.com / password
```

### 3. **OfficeSeeder.php**
Membuat 8 kantor cabang:
- Kantor Pusat Jakarta (Radius: 100m)
- Kantor Cabang Bandung (Radius: 150m)
- Kantor Cabang Surabaya (Radius: 100m)
- Kantor Cabang Medan (Radius: 120m)
- Kantor Cabang Semarang (Radius: 100m)
- Kantor Cabang Yogyakarta (Radius: 100m)
- Kantor Cabang Bali (Radius: 150m)
- Kantor Cabang Makassar (Radius: 100m)

Setiap kantor memiliki koordinat GPS yang akurat.

### 4. **ShiftSeeder.php**
Membuat 6 shift kerja:
- Shift Pagi: 08:00 - 16:00
- Shift Siang: 13:00 - 21:00
- Shift Malam: 21:00 - 05:00
- Shift Full Day: 09:00 - 18:00
- Shift Reguler: 07:00 - 15:00
- Shift Fleksibel: 10:00 - 19:00

### 5. **ScheduleSeeder.php**
Membuat jadwal untuk setiap employee:
- Distribusi merata shift dan kantor
- 7 employees (35%) bekerja WFA (Work From Anywhere)
- 1 employee di-banned
- Setiap user hanya memiliki 1 schedule (unique constraint)

### 6. **AttendanceSeeder.php**
Membuat data presensi realistis untuk 30 hari terakhir:
- **Attendance rate**: ~85% (skip beberapa hari secara random)
- **Weekend**: Otomatis diskip
- **Late arrival**: 20% kemungkinan terlambat 5-60 menit
- **Early departure**: 10% kemungkinan pulang lebih awal
- **Overtime**: 30% kemungkinan lembur 10-120 menit
- **GPS variance**: Simulasi variasi GPS ±5 meter
- **Total records**: ~355 attendance records

### 7. **LeaveSeeder.php**
Membuat pengajuan cuti untuk setiap employee:
- 1-3 pengajuan per employee
- Durasi: 1-7 hari
- **Status distribution**:
  - 70% Approved
  - 20% Pending
  - 10% Rejected
- Tanggal: Random dalam 60 hari terakhir hingga 30 hari ke depan

**Jenis Cuti:**
- Cuti Tahunan
- Sakit
- Keperluan Keluarga
- Cuti Melahirkan
- Umroh/Haji
- Pernikahan
- Kematian Keluarga
- Keperluan Mendesak

## 📊 Data yang Dibuat

Setelah seeding berhasil, database akan berisi:

| Table | Jumlah Records |
|-------|----------------|
| Users | 22 |
| Roles | 3 |
| Permissions | 42 |
| Offices | 8 |
| Shifts | 6 |
| Schedules | 20 |
| Attendances | ~355 |
| Leaves | ~42 |

## 🚀 Cara Menjalankan

### Fresh Installation (Reset Database)

```bash
# Refresh database dan jalankan semua migrations
php artisan migrate:fresh

# Jalankan semua seeders
php artisan db:seed
```

### Seeder Spesifik

Jika ingin menjalankan seeder tertentu saja:

```bash
# Jalankan satu seeder
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=OfficeSeeder
php artisan db:seed --class=ShiftSeeder
php artisan db:seed --class=ScheduleSeeder
php artisan db:seed --class=AttendanceSeeder
php artisan db:seed --class=LeaveSeeder
```

### One-liner (Migration + Seed)

```bash
php artisan migrate:fresh --seed
```

## 🧪 Testing & Verifikasi

### Method 1: Menggunakan Script Verifikasi

```bash
php verify_seeder.php
```

Output akan menampilkan:
- ✅ Total records per table
- ✅ Daftar users dengan roles
- ✅ Daftar offices dengan radius
- ✅ Daftar shifts
- ✅ Statistik schedules (WFA, Banned, Office)
- ✅ Statistik attendances
- ✅ Statistik leaves (Approved, Pending, Rejected)
- ✅ Daftar roles dengan jumlah permissions
- ✅ Login credentials

### Method 2: Manual Query

```bash
php artisan tinker
```

Kemudian jalankan:

```php
// Count records
User::count();
Office::count();
Shift::count();
Schedule::count();
Attendance::count();
Leave::count();

// View specific data
User::with('roles')->get();
Schedule::with(['user', 'shift', 'office'])->where('is_wfa', true)->get();
Attendance::whereDate('created_at', today())->get();
Leave::where('status', 'pending')->get();
```

### Method 3: Database Client

Gunakan database client favorit Anda (TablePlus, phpMyAdmin, DBeaver, dll) untuk melihat data langsung di database.

## 📝 Catatan Penting

1. **Urutan Seeding**: Urutan di `DatabaseSeeder.php` sudah disesuaikan dengan foreign key dependencies:
   ```
   RolePermissionSeeder → UserSeeder → OfficeSeeder → ShiftSeeder → ScheduleSeeder → AttendanceSeeder → LeaveSeeder
   ```

2. **Password Default**: Semua user menggunakan password `password` (di-hash dengan bcrypt)

3. **Email Verification**: Semua user sudah ter-verifikasi (`email_verified_at` sudah diisi)

4. **Soft Deletes**: Tables `offices`, `shifts`, `attendances`, dan `leaves` menggunakan soft deletes

5. **Constraints**:
   - Schedule: user_id harus unique (1 user = 1 schedule)
   - User dengan role employee harus punya schedule sebelum bisa attendance

## 🔧 Troubleshooting

### Error: "SQLSTATE[23000]: Integrity constraint violation"

**Solusi**: Pastikan menjalankan seeder sesuai urutan atau gunakan `php artisan migrate:fresh --seed`

### Error: "Call to undefined method assignRole()"

**Solusi**: Pastikan Spatie Permission sudah terinstall dan migration-nya sudah dijalankan

### Tidak ada data Attendance

**Solusi**: Pastikan sudah ada Users dengan role 'employee' dan sudah ada Schedules

### Data Leave kosong

**Solusi**: Sama seperti di atas, pastikan ada Users dengan role 'employee'

## 🎨 Customization

### Mengubah Jumlah Employees

Edit `UserSeeder.php`, tambahkan/kurangi array `$employees`

### Mengubah Attendance Rate

Edit `AttendanceSeeder.php`, ubah nilai di:
```php
if (rand(1, 100) > 85) { // 85% attendance rate
```

### Mengubah Late Probability

Edit `AttendanceSeeder.php`, ubah nilai di:
```php
$isLate = rand(1, 100) <= 20; // 20% chance
```

### Menambah Office Baru

Edit `OfficeSeeder.php`, tambahkan ke array `$offices`:
```php
[
    'name' => 'Kantor Cabang Baru',
    'latitude' => -6.123456,
    'longitude' => 106.123456,
    'radius' => 100,
]
```

## 📜 License

Seeder ini adalah bagian dari sistem presensi dan mengikuti license yang sama dengan aplikasi utama.

## 👨‍💻 Author

Database Seeders untuk Sistem Presensi
Created: January 2026

---

**Happy Seeding! 🌱**
