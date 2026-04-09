# Laporan Perbaikan Bug - Sistem Presensi

## 📋 Ringkasan Perbaikan

Dokumen ini berisi detail perbaikan untuk 5 masalah yang ditemukan pada sistem presensi.

---

## ✅ 1. Error Saat Submit Presensi (SQLSTATE[23000]: Integrity constraint violation)

### Masalah
Employee mendapat error `Integrity constraint violation: 1048 Column 'end_time' cannot be null` saat submit presensi (check-in).

### Penyebab
Kolom `end_time` di tabel `attendances` tidak diset sebagai **nullable** di database. Saat check-in pertama kali, kolom `end_time` harus NULL karena employee belum check-out.

### Solusi
✅ **Migration Baru Dibuat**: `2026_02_08_023051_make_end_time_nullable_in_attendances_table.php`
- Mengubah kolom `end_time` menjadi nullable
- Migration berhasil dijalankan
- Cache sudah dibersihkan

### ID Pegawai
**ID Pegawai menggunakan `user_id`** yang merupakan foreign key ke tabel `users`. Setiap attendance record menyimpan:
- `user_id` - ID dari tabel users (NOT NULL)
- `start_time` - Waktu check-in (NOT NULL)
- `end_time` - Waktu check-out (NULLABLE) ✅
- Location data (latitude/longitude)

### Perubahan File
- `database/migrations/2026_02_08_023051_make_end_time_nullable_in_attendances_table.php` - NEW

### Status
✅ **FIXED!** Employee sekarang bisa check-in tanpa error.

**Detail lengkap**: Lihat file `FIX_ATTENDANCE_ERROR.md`

---

## ✅ 2. Tidak Ada Tulisan/Logo "Use My GPS"

### Masalah
Button untuk tag location tidak jelas, tidak ada indikator visual yang menjelaskan cara penggunaannya.

### Solusi
- ✅ Menambahkan **info box** dengan instruksi lengkap cara menggunakan GPS
- ✅ Button "Tag Location" sekarang memiliki **icon GPS** dan text lebih jelas
- ✅ Design diperbaiki dengan layout yang lebih user-friendly
- ✅ Button full-width dengan icon yang jelas

### Perubahan File
- `resources/views/livewire/presensi.blade.php`

### Preview Perubahan
```
📍 How to set location:
1. Click "📍 Tag Location (Use My GPS)" button below
2. Allow GPS/location permission when browser asks
3. Wait for your location to be detected on the map
4. Click "✅ Submit Presensi" if you're in office radius
```

---

## ✅ 3. Tag Location Tidak Muncul di Laptop

### Masalah
Geolocation tidak berfungsi di laptop/browser tertentu karena permission GPS tidak diminta atau error handling tidak ada.

### Solusi
- ✅ Menambahkan **error handling** yang lengkap untuk geolocation
- ✅ Menambahkan **alert message** yang jelas untuk berbagai kondisi:
  - Permission denied
  - Location unavailable
  - Timeout
  - Browser tidak support geolocation
- ✅ Menambahkan **options** untuk geolocation dengan:
  - `enableHighAccuracy: true` - GPS lebih akurat
  - `timeout: 10000` - Timeout 10 detik
  - `maximumAge: 0` - Selalu ambil posisi terbaru
- ✅ Konfirmasi alert saat lokasi berhasil terdeteksi

### Error Messages
```javascript
- "❌ GPS Permission Denied! Please allow location access..."
- "✅ Lokasi terdeteksi! Anda berada dalam radius kantor..."
- "⚠️ Lokasi terdeteksi, tetapi Anda berada di luar radius kantor."
```

### Perubahan File
- `resources/views/livewire/presensi.blade.php`

---

## ✅ 4. Employee Bisa Ganti Nama Saat Ajukan Cuti

### Masalah
Saat employee membuat leave request, mereka bisa memilih nama employee lain dari dropdown.

### Solusi
- ✅ **Employee** sekarang hanya bisa ajukan cuti untuk diri sendiri
  - Field employee menjadi **readonly** dengan nama mereka
  - `user_id` otomatis di-set ke ID user yang login
- ✅ **Admin/Super Admin** masih bisa memilih employee lain (untuk create atas nama employee)
- ✅ Saat edit, employee **tidak bisa mengubah** nama employee

### Perubahan File
- `app/Http/Controllers/LeaveController.php`
- `resources/views/leave/create.blade.php`
- `resources/views/leave/edit.blade.php`

### Logika
```php
// Di Controller
if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
    $request->merge([
        'user_id' => auth()->id(),  // Paksa user_id = ID yang login
        'status' => 'pending'       // Auto set pending
    ]);
}

// Di View
@if(auth()->user()->hasRole(['super_admin', 'admin']))
    <select>...</select>  // Admin bisa pilih
@else
    <input readonly>      // Employee readonly
    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
@endif
```

---

## ✅ 5. Employee Bisa Ubah Status Cuti

### Masalah
Employee bisa mengubah status cuti dari "Pending" ke "Approved" atau "Rejected" saat create/edit.

### Solusi

### 5.1 Saat Create Leave
- ✅ **Employee**: Field status menjadi **readonly** dengan value "Pending"
  - Ditampilkan text "Pending" (tidak bisa diubah)
  - Hidden input otomatis set `status = pending`
  - Ditambahkan helper text: "Your leave request will be reviewed by admin"
- ✅ **Admin/Super Admin**: Bisa memilih status (Pending/Approved/Rejected)

### 5.2 Saat Edit Leave
- ✅ **Employee**: 
  - Tidak bisa mengubah status
  - Jika status sudah **Approved/Rejected**, semua field menjadi readonly
  - Jika masih **Pending**, bisa edit reason/dates tapi status tetap pending
- ✅ **Admin/Super Admin**: Bisa mengubah semua field termasuk status

### Perubahan File
- `app/Http/Controllers/LeaveController.php`
- `resources/views/leave/create.blade.php`
- `resources/views/leave/edit.blade.php`

### Logika Edit
```php
// Di Controller - update method
if (!auth()->user()->hasRole(['super_admin', 'admin'])) {
    $request->merge(['user_id' => $leave->user_id]);  // Tidak bisa ganti user
    
    // Jika sudah approved/rejected, employee tidak bisa mengubah
    if (in_array($leave->status, ['approved', 'rejected'])) {
        $request->merge(['status' => $leave->status]);
    } else {
        $request->merge(['status' => 'pending']);  // Tetap pending
    }
}
```

---

## 🔧 Cara Test Perubahan

### 1. Test Presensi
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Login sebagai employee
# Buka halaman presensi
# Klik "Tag Location" dan allow GPS permission
# Submit presensi
```

### 2. Test Leave Request
```bash
# Login sebagai employee
# Create Leave Request
# ✅ Nama employee sudah otomatis terisi (readonly)
# ✅ Status otomatis "Pending" (readonly)

# Edit Leave yang masih pending
# ✅ Bisa edit tanggal dan reason
# ✅ Tidak bisa ubah status

# Coba edit leave yang sudah approved
# ✅ Semua field readonly
```

### 3. Test Sebagai Admin
```bash
# Login sebagai admin/super_admin
# Create Leave Request
# ✅ Bisa pilih employee
# ✅ Bisa pilih status

# Edit Leave
# ✅ Bisa ubah semua field termasuk status
```

---

## 📁 File yang Diubah

1. `app/Http/Controllers/LeaveController.php`
   - Method `create()` - Auto-fill user untuk employee
   - Method `store()` - Force user_id dan status untuk employee
   - Method `edit()` - Authorization check
   - Method `update()` - Prevent employee dari mengubah user/status

2. `resources/views/leave/create.blade.php`
   - Conditional rendering untuk employee vs admin
   - Readonly fields untuk employee
   - Hidden inputs untuk auto-fill

3. `resources/views/leave/edit.blade.php`
   - Conditional rendering berdasarkan role dan status
   - Readonly untuk approved/rejected leaves
   - Authorization untuk button submit

4. `resources/views/livewire/presensi.blade.php`
   - Tambah info box instruksi GPS
   - Perbaiki button design dengan icon
   - Tambah error handling geolocation
   - Tambah alert messages

---

## 🎯 Hasil Akhir

### Employee Experience
- ✅ Tidak bisa ganti nama employee saat create leave
- ✅ Tidak bisa ubah status leave (auto pending)
- ✅ Tidak bisa edit leave yang sudah approved/rejected
- ✅ GPS button lebih jelas dengan instruksi lengkap
- ✅ Error message yang informatif saat GPS bermasalah

### Admin Experience
- ✅ Tetap bisa create leave untuk employee lain
- ✅ Tetap bisa mengubah status leave
- ✅ Full control atas semua leave requests

### Security
- ✅ Authorization check di controller
- ✅ Force merge data di backend (tidak hanya UI)
- ✅ Employee tidak bisa manipulate request

---

## 📝 Catatan Penting

1. **GPS Permission**: User HARUS allow location permission di browser
2. **HTTPS Required**: Geolocation hanya bekerja di HTTPS atau localhost
3. **Browser Compatibility**: Test di Chrome, Firefox, Safari (modern browsers)
4. **Database**: Pastikan migration sudah dijalankan
5. **Cache**: Clear cache setelah perubahan: 
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

---

## 🐛 Troubleshooting

### GPS Tidak Muncul
1. Check console browser (F12)
2. Pastikan HTTPS atau localhost
3. Allow location permission di browser settings
4. Coba refresh page

### Error Submit Presensi
1. Clear cache Laravel
2. Check database connection
3. Verify user memiliki schedule
4. Check error log: `storage/logs/laravel.log`

### Leave Request Error
1. Clear cache
2. Verify user roles di database
3. Check validation errors
4. Review error log

---

**Tanggal Perbaikan**: 8 Februari 2026
**Developer**: GitHub Copilot
**Status**: ✅ Selesai
