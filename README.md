# HRIS BPRS — Human Resource Information System

Sistem informasi manajemen SDM (HRIS) berbasis web untuk BPRS (Bank Pembiayaan Rakyat Syariah). Dibangun dengan Laravel 11, Filament 3, Livewire, dan TailwindCSS.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Instalasi](#-instalasi)
- [Role & Permission](#-role--permission)
- [Flow Sistem](#-flow-sistem)
  - [Autentikasi](#1-autentikasi)
  - [Presensi / Attendance](#2-presensi--attendance)
  - [Pengajuan Cuti (Leave)](#3-pengajuan-cuti-leave)
  - [Penggajian (Payroll)](#4-penggajian-payroll)
  - [Master Data](#5-master-data)
  - [Profil Karyawan](#6-profil-karyawan)
  - [Notifikasi](#7-notifikasi)
- [Struktur Database](#-struktur-database)
- [Akun Demo](#-akun-demo)
- [Seeders](#-seeders)

---

## 🚀 Fitur Utama

| Modul | Fitur |
|-------|-------|
| **Presensi** | GPS-based check-in/out, radius validasi, WFA support, import Excel, export |
| **Cuti** | Pengajuan cuti 3 layer approval (Kadiv → HR → Direksi), filter per divisi |
| **Penggajian** | Generate otomatis dari attendance, gaji pokok + tunjangan + potongan, slip PDF |
| **Karyawan** | CRUD user, riwayat jabatan/karir, profil lengkap, foto |
| **Master Data** | Lokasi kantor, shift/jam kerja, jadwal, jabatan/role, divisi |
| **Dashboard** | Statistik kehadiran, chart bulanan, ringkasan per role |
| **Notifikasi** | Real-time notification untuk approval cuti & payroll |
| **Admin Panel** | Filament 3 admin panel dengan Shield permission management |

---

## 🛠 Tech Stack

- **Backend**: PHP 8.2+, Laravel 11
- **Admin Panel**: Filament 3 + Shield (permission management)
- **Frontend**: Blade, TailwindCSS, Alpine.js, Chart.js
- **Realtime**: Livewire 3
- **Database**: MySQL 8.0
- **Auth**: Spatie Permission (RBAC)
- **Export/Import**: Maatwebsite Excel
- **PDF**: DomPDF (slip gaji)

---

## ⚙ Instalasi

```bash
# 1. Clone repository
git clone https://github.com/fikri200401/presensi-main.git
cd presensi-main

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_DATABASE=hris_bprs
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Migrasi & seed database
php artisan migrate --seed

# 6. Storage link (untuk foto profil)
php artisan storage:link

# 7. Build assets
npm run dev        # development
npm run build      # production

# 8. Jalankan server
php artisan serve
```

---

## 👥 Role & Permission

Sistem menggunakan **5 role** dengan **53 permission** berbasis Spatie Permission:

| Role | Akses | Jumlah Permission |
|------|-------|-------------------|
| **Super Admin** | Akses penuh ke semua fitur + Filament Admin Panel | 53 (semua) |
| **Admin / HR** | Kelola master data, approve cuti layer 2, kelola payroll & divisi | 30 |
| **Direksi** | Final approval cuti, lihat laporan attendance & payroll | 9 |
| **Kepala Divisi** | Approve cuti layer 1 (divisi sendiri), lihat tim | 9 |
| **Employee** | Self-service: presensi, ajukan cuti, lihat gaji & jadwal | 6 |

### Permission Groups

```
User        : view, view_any, create, update, delete, delete_any
Office      : view, view_any, create, update, delete, delete_any
Shift       : view, view_any, create, update, delete, delete_any
Schedule    : view, view_any, create, update, delete, delete_any
Attendance  : view, view_any, create, update, delete, delete_any
Leave       : view, view_any, create, update, delete, delete_any,
              approve_leave_kadiv, approve_leave_hr, approve_leave_direksi
Role        : view, view_any, create, update, delete, delete_any
Payroll     : view, view_any, create
Division    : view, view_any, create, update, delete
```

---

## 🔄 Flow Sistem

### 1. Autentikasi

```
┌─────────────┐     ┌──────────┐     ┌────────────────┐
│  Login Page │────▶│  Auth    │────▶│   Dashboard    │
│  (5 demo)   │     │  Check   │     │  (per role)    │
└─────────────┘     └──────────┘     └────────────────┘
                         │
                    ┌────▼─────┐
                    │ Filament │  (super_admin only)
                    │  Panel   │
                    └──────────┘
```

- Halaman login menampilkan **5 akun demo** untuk testing
- Setelah login, redirect ke dashboard sesuai role
- Super Admin punya akses tambahan ke **Filament Admin Panel** (`/admin`)

---

### 2. Presensi / Attendance

```
┌──────────┐     ┌──────────────┐     ┌───────────────┐
│ Employee │────▶│  GPS Check   │────▶│  Validasi     │
│ (mobile) │     │  Location    │     │  • Radius     │
└──────────┘     └──────────────┘     │  • Jadwal     │
                                      │  • WFA flag   │
                                      └───────┬───────┘
                                              │
                                    ┌─────────▼──────────┐
                                    │   Attendance Log   │
                                    │  • check_in time   │
                                    │  • check_out time  │
                                    │  • GPS coordinates │
                                    │  • status (late?)  │
                                    └─────────┬──────────┘
                                              │
                              ┌───────────────┼───────────────┐
                              ▼               ▼               ▼
                        ┌──────────┐   ┌──────────┐   ┌──────────┐
                        │  Import  │   │  Export   │   │  Report  │
                        │  Excel   │   │  Excel   │   │  (Admin) │
                        └──────────┘   └──────────┘   └──────────┘
```

**Flow detail:**
1. Karyawan buka halaman presensi (Livewire component dengan peta)
2. Sistem deteksi lokasi GPS via browser
3. Validasi jarak dengan radius kantor yang terdaftar
4. Jika WFA (Work From Anywhere), skip validasi radius
5. Catat waktu check-in / check-out
6. Admin bisa import data presensi dari Excel (dengan preview & draft)
7. Data presensi bisa di-export ke Excel

---

### 3. Pengajuan Cuti (Leave)

```
                           ┌─────────────────────────────────────┐
                           │    3-LAYER APPROVAL FLOW            │
                           └─────────────────────────────────────┘

┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐     ┌──────────┐
│ Employee │────▶│ PENDING  │────▶│ APPROVED │────▶│ APPROVED │────▶│ APPROVED │
│  Submit  │     │          │     │  KADIV   │     │    HR    │     │ (Final)  │
└──────────┘     └────┬─────┘     └────┬─────┘     └────┬─────┘     └──────────┘
                      │                │                │
                      ▼                ▼                ▼
                 ┌──────────┐    ┌──────────┐    ┌──────────┐
                 │ REJECTED │    │ REJECTED │    │ REJECTED │
                 │ (Kadiv)  │    │  (HR)    │    │(Direksi) │
                 └──────────┘    └──────────┘    └──────────┘
```

**Flow detail:**
1. **Employee** mengajukan cuti (jenis, tanggal mulai, tanggal selesai, alasan)
2. **Kepala Divisi** melihat cuti dari **divisi sendiri saja** → approve/reject
   - 🔒 Security: Kadiv hanya bisa approve/reject karyawan **satu divisi**
3. **Admin/HR** melihat semua cuti yang sudah disetujui Kadiv → approve/reject
4. **Direksi** memberikan final approval → status menjadi `approved`
5. Setiap tahap mengirim **notifikasi** ke user terkait
6. Reject di tahap manapun langsung menghentikan flow

**Status flow:**
```
pending → approved_kadiv → approved_hr → approved
    ↓           ↓               ↓
 rejected   rejected_kadiv   rejected_hr   rejected_direksi
```

---

### 4. Penggajian (Payroll)

```
┌──────────────┐     ┌────────────────┐     ┌──────────────────┐
│ Admin pilih  │────▶│  Auto-Generate │────▶│  Payroll Record  │
│ bulan/tahun  │     │  • Gaji Pokok  │     │  status: approved │
│ + karyawan   │     │  • Tunjangan   │     │  (auto-approve)  │
└──────────────┘     │  • Potongan    │     └────────┬─────────┘
                     │  • Lembur      │              │
                     │  • Keterlambat │     ┌────────▼─────────┐
                     └────────────────┘     │   Mark as Paid   │
                                            │  status: paid    │
                                            └────────┬─────────┘
                                                     │
                                            ┌────────▼─────────┐
                                            │  Export Slip PDF  │
                                            └──────────────────┘
```

**Flow detail:**
1. Admin pilih bulan, tahun, dan karyawan
2. Sistem otomatis menghitung dari data attendance + salary setting:
   - Gaji pokok (dari `employee_salaries`)
   - Tunjangan tetap & tidak tetap
   - Potongan (keterlambatan, absen, dll)
   - Lembur
3. Payroll langsung **auto-approved** saat generate (tidak perlu approval manual)
4. Admin bisa **mark as paid** setelah transfer
5. Slip gaji bisa di-**export PDF** per karyawan
6. Karyawan bisa melihat slip gaji sendiri di menu Payroll

---

### 5. Master Data

```
┌─────────────────────────────────────────────┐
│            MASTER DATA (Tab Navigation)     │
├──────┬────────┬─────────┬────────┬──────────┤
│Karya-│ Lokasi │Jam Kerja│ Jadwal │  Jabatan │  Divisi
│ wan  │        │         │        │          │
└──┬───┴───┬────┴────┬────┴───┬────┴────┬─────┴────┬──┐
   │       │         │        │         │          │
   ▼       ▼         ▼        ▼         ▼          ▼
 Users   Offices   Shifts  Schedules  Roles    Divisions
 CRUD    CRUD      CRUD    CRUD       CRUD     CRUD
 +foto   +GPS      +jam    +assign    +perm    +cascade
 +divisi +radius   masuk/  ke user    assign   rename
 +posisi           keluar  +WFA                +protect
 history                                       delete
```

**Modul detail:**
- **Karyawan**: CRUD user lengkap + upload foto + assign divisi & role + riwayat jabatan
- **Lokasi Kantor**: Nama, alamat, koordinat GPS, radius validasi presensi
- **Jam Kerja (Shift)**: Nama shift, jam masuk, jam keluar, toleransi keterlambatan
- **Jadwal**: Assign shift ke karyawan per hari, support WFA (Work From Anywhere)
- **Jabatan/Role**: Manage role dan assign permission menggunakan Spatie Permission
- **Divisi**: CRUD divisi, otomatis cascade rename ke users & position_histories, proteksi hapus jika masih ada karyawan

---

### 6. Profil Karyawan

```
┌─────────────────────────────────────────────┐
│              PROFIL SAYA                    │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────┐  Nama, Email, NIP, Telepon    │
│  │  Foto   │  Divisi, Jabatan, Alamat      │
│  │ Profil  │  Tanggal Lahir                │
│  └─────────┘                               │
│                                             │
│  ── Riwayat Karir (Timeline) ────────────  │
│  │ 2026 - Sekarang : Manager IT            │
│  │ 2024 - 2026     : Staff IT              │
│  │ 2022 - 2024     : Junior Developer      │
│                                             │
│  ── Jadwal Kerja Hari Ini ───────────────  │
│  │ Shift Pagi: 08:00 - 17:00              │
│                                             │
│  ── Statistik Kehadiran ─────────────────  │
│  │ Chart 6 bulan terakhir                  │
│  │ Total hadir / terlambat / izin          │
│                                             │
│  ── Edit Profil & Ganti Password ────────  │
└─────────────────────────────────────────────┘
```

- Semua role bisa akses profil sendiri
- Admin bisa kelola riwayat jabatan karyawan dari halaman edit user
- Chart kehadiran 6 bulan terakhir menggunakan Chart.js

---

### 7. Notifikasi

```
Employee submit cuti ──▶ Notifikasi ke Kadiv (divisi sama)
Kadiv approve ─────────▶ Notifikasi ke Admin/HR
HR approve ────────────▶ Notifikasi ke Direksi
Final approve/reject ──▶ Notifikasi ke Employee
Payroll generated ─────▶ Notifikasi ke Employee
```

- Badge counter di sidebar menunjukkan jumlah notifikasi belum dibaca
- Halaman notifikasi dengan mark as read & mark all as read

---

## 🗃 Struktur Database

```
users
├── offices (lokasi kantor, GPS, radius)
├── shifts (jam kerja)
├── schedules (jadwal: user ↔ shift + WFA flag)
├── attendances (presensi: GPS, waktu, status)
├── leaves (cuti: 3-layer approval status)
├── divisions (master divisi)
├── position_histories (riwayat jabatan per user)
├── salary_settings (konfigurasi komponen gaji)
├── employee_salaries (gaji per karyawan)
├── payrolls (slip gaji bulanan)
├── notifications (notifikasi in-app)
├── import_drafts (draft import Excel)
├── roles & permissions (Spatie Permission)
└── pulse_* (Laravel Pulse monitoring)
```

---

## 🔑 Akun Demo

Halaman login menampilkan akun demo untuk testing:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@presensi.com` | `password` |
| Admin/HR | `admin@presensi.com` | `password` |
| Direksi | `direksi@presensi.com` | `password` |
| Kepala Divisi | `kadiv@presensi.com` | `password` |
| Employee | `budi.santoso@presensi.com` | `password` |

> **Filament Admin Panel**: `/admin` (hanya Super Admin)

---

## 🌱 Seeders

Jalankan semua seeder sekaligus:

```bash
php artisan migrate:fresh --seed
```

Atau jalankan seeder tertentu:

```bash
php artisan db:seed --class=RolePermissionSeeder   # 5 role + 53 permission
php artisan db:seed --class=UserSeeder              # User demo + karyawan sample
php artisan db:seed --class=OfficeSeeder            # Kantor cabang
php artisan db:seed --class=ShiftSeeder             # Shift kerja
php artisan db:seed --class=ScheduleSeeder          # Jadwal karyawan
php artisan db:seed --class=SalarySettingSeeder     # Komponen gaji
php artisan db:seed --class=EmployeeSalarySeeder    # Gaji per karyawan
php artisan db:seed --class=AttendanceSeeder        # Data presensi 30 hari
php artisan db:seed --class=LeaveSeeder             # Data cuti sample
php artisan db:seed --class=PayrollSeeder           # Data payroll sample
```

---

## 📁 Struktur Project

```
app/
├── Exports/          # Excel export (Attendance)
├── Filament/         # Filament admin panel resources
├── Http/Controllers/ # Web controllers (14 controllers)
├── Imports/          # Excel import (Attendance)
├── Livewire/         # Livewire components (Map, Presensi)
├── Models/           # Eloquent models (12 models)
├── Policies/         # Authorization policies
└── Providers/        # Service providers

database/
├── migrations/       # 27 migration files
└── seeders/          # 11 seeder files

resources/views/
├── attendance/       # Halaman presensi
├── dashboard/        # Dashboard per role
├── division/         # Manajemen divisi
├── leave/            # Pengajuan & approval cuti
├── layouts/          # Layout utama + sidebar + tabs
├── office/           # Lokasi kantor
├── payroll/          # Penggajian & slip
├── profile/          # Profil karyawan
├── role/             # Manajemen role
├── schedule/         # Jadwal kerja
├── shift/            # Jam kerja
└── user/             # Manajemen karyawan
```

---

## 📄 Lisensi

Open-source, dikembangkan untuk kebutuhan internal BPRS.
