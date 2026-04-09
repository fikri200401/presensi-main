# 📍 GPS-Based Attendance System - Panduan Lengkap

## ✨ Fitur yang Telah Dipulihkan dan Ditingkatkan

Sistem presensi berbasis GPS telah **dipulihkan** dan **ditingkatkan** dari program asli dengan penambahan fitur-fitur berikut:

### 🎯 Fitur Utama

1. **GPS Location Tracking**
   - ✅ Real-time GPS location tagging menggunakan Leaflet.js
   - ✅ Validasi radius otomatis (karyawan harus berada dalam radius kantor)
   - ✅ Visualisasi peta interaktif dengan marker lokasi karyawan
   - ✅ Lingkaran radius kantor ditampilkan di peta

2. **WFA (Work From Anywhere) Support**
   - ✅ Karyawan dengan status WFA bisa check-in dari mana saja
   - ✅ Bypass otomatis validasi radius untuk WFA employees
   - ✅ Status WFA/WFO ditampilkan jelas di halaman presensi

3. **Intelligent Check-in/Check-out**
   - ✅ Sistem otomatis deteksi: first check = check-in, second check = check-out
   - ✅ Validasi cuti: tidak bisa presensi jika sedang cuti approved
   - ✅ Hanya 1 presensi per hari (prevent duplicate)

4. **Improved UX (Peningkatan dari Versi Asli)**
   - ✅ Flash message success/error yang jelas dan menarik
   - ✅ Button dengan emoji dan hover effects
   - ✅ Widget presensi di dashboard untuk akses cepat
   - ✅ Status hari ini ditampilkan real-time (sudah check-in/belum)
   - ✅ Menu terpisah: "Check In/Out" dan "Attendance History"

---

## 🚀 Cara Menggunakan (Employee)

### 1. Login sebagai Employee
```
Email: employee1@test.com - employee20@test.com
Password: password
```

### 2. Akses Halaman Presensi
Ada 3 cara:
- **Cara 1**: Klik menu **"Check In / Out"** di sidebar
- **Cara 2**: Klik tombol besar **"Check In Sekarang"** di Dashboard
- **Cara 3**: Akses langsung: http://127.0.0.1:8000/presensi

### 3. Tag Location
1. Klik tombol **"📍 Tag Location"**
2. Browser akan meminta izin GPS → Klik **"Allow/Izinkan"**
3. Peta akan menampilkan lokasi Anda dengan marker biru
4. Sistem otomatis cek apakah Anda di dalam radius kantor

### 4. Submit Presensi
- Jika **di dalam radius** atau **WFA**: Tombol hijau **"✅ Submit Presensi"** akan muncul
- Klik tombol hijau untuk mengirim presensi
- Jika **di luar radius** (dan bukan WFA): Tombol tidak muncul, Anda harus ke kantor

### 5. Check-out
- Ulangi langkah 3-4 di akhir hari
- Sistem otomatis tahu ini check-out (karena sudah ada check-in hari ini)

---

## 🛠️ Technical Implementation

### Files Modified/Created:

#### 1. **Livewire Component** (`app/Livewire/Presensi.php`)
```php
- Properties: latitude, longitude, insideRadius
- Validation: leave conflict check
- Logic: auto-detect check-in vs check-out
- Flash messages: success/error notifications
```

#### 2. **Blade View** (`resources/views/livewire/presensi.blade.php`)
```html
- Leaflet.js map integration (CDN)
- tagLocation() JavaScript function
- isWithinRadius() validation with WFA bypass
- Real-time marker placement
- Success/error message display
```

#### 3. **Layout** (`resources/views/layouts/app.blade.php`)
```html
+ Leaflet CSS (unpkg.com/leaflet@1.9.4)
+ Livewire styles/scripts
```

#### 4. **Sidebar Menu** (`resources/views/layouts/partials/sidebar.blade.php`)
```html
+ "Check In / Out" menu (with GPS icon)
+ "Attendance History" (renamed from "Attendance")
```

#### 5. **Dashboard Widget** (`resources/views/dashboard.blade.php`)
```html
+ Large check-in button for employees
+ Today's attendance status display
+ Dynamic button text (Check In/Check Out/Lihat)
```

#### 6. **Dashboard Controller** (`app/Http/Controllers/DashboardController.php`)
```php
+ $todayAttendance variable for employee
+ Role-based logic
```

### Routes:
```php
Route::get('presensi', Presensi::class)->name('presensi');
```

---

## 📊 Database Structure

### Attendance Table
```sql
- user_id (relasi ke users)
- schedule_latitude/longitude (koordinat kantor dari schedule)
- schedule_start_time/end_time (jam kerja dari shift)
- start_latitude/longitude (koordinat employee saat check-in)
- end_latitude/longitude (koordinat employee saat check-out)
- start_time (waktu check-in)
- end_time (waktu check-out)
```

### Key Relationships:
```
User → Schedule → Office (latitude, longitude, radius)
User → Schedule → Shift (start_time, end_time)
```

---

## 🎨 Improvements dari Versi Asli

| Fitur | Versi Asli | Versi Baru (Improved) |
|-------|-----------|---------------------|
| Flash Messages | ❌ Inline HTML error | ✅ Beautiful Tailwind alerts |
| Dashboard Widget | ❌ Tidak ada | ✅ Quick check-in button |
| Menu Structure | ⚠️ Satu menu "Attendance" | ✅ Terpisah: Check In + History |
| Button Design | ⚠️ Plain | ✅ Icons, hover, transitions |
| Status Display | ⚠️ Basic | ✅ Real-time dengan emoji |
| Success Message | ❌ Tidak ada | ✅ Konfirmasi jelas check-in/out |
| End Time | ⚠️ Set saat create | ✅ NULL saat check-in, update saat check-out |

---

## 🧪 Testing Scenarios

### Scenario 1: Normal WFO Check-in (Inside Radius)
```
1. Login sebagai employee1@test.com
2. Akses /presensi
3. Tag Location (dalam radius kantor)
4. Tombol Submit muncul → Klik
5. Expected: ✅ "Check-in berhasil! Selamat bekerja."
6. Redirect ke /presensi dengan marker tetap di peta
```

### Scenario 2: WFO Check-in (Outside Radius)
```
1. Login sebagai employee
2. Tag Location (di luar radius kantor)
3. Expected: Tombol Submit TIDAK muncul
4. Must go to office to check-in
```

### Scenario 3: WFA Check-in (Anywhere)
```
1. Login sebagai employee dengan is_wfa = true
2. Tag Location (lokasi mana saja)
3. Expected: Tombol Submit SELALU muncul (bypass radius)
4. Check-in berhasil dari rumah/cafe/etc
```

### Scenario 4: Check-out
```
1. Sudah check-in pagi
2. Tag Location sore hari
3. Klik Submit
4. Expected: ✅ "Check-out berhasil! Terima kasih atas kerja keras Anda."
5. Database: end_time terisi
```

### Scenario 5: Leave Conflict
```
1. Employee sedang cuti approved (start_date ≤ today ≤ end_date)
2. Coba check-in
3. Expected: ❌ "Anda tidak dapat melakukan presensi karena sedang cuti."
```

---

## 🐛 Troubleshooting

### Issue: Tombol "Submit Presensi" tidak muncul
**Solution:**
- Cek apakah sudah klik "Tag Location"
- Cek apakah lokasi GPS terdeteksi (marker muncul?)
- Jika WFO: pastikan berada dalam radius kantor
- Jika WFA: cek `is_wfa` di database (seharusnya auto-muncul)

### Issue: Browser tidak minta izin GPS
**Solution:**
- Gunakan HTTPS atau localhost (HTTP tidak boleh GPS)
- Cek browser settings → Location permission
- Coba browser lain (Chrome recommended)

### Issue: Peta tidak muncul
**Solution:**
- Cek console browser (F12) untuk error
- Pastikan internet aktif (Leaflet.js dari CDN)
- Cek apakah Livewire scripts loaded
- Clear cache browser: Ctrl+Shift+R

### Issue: "Call to undefined relationship [schedule]"
**Solution:**
- Sudah fixed! Pastikan User model memiliki:
```php
public function schedule() {
    return $this->hasOne(Schedule::class);
}
```

---

## 🔐 Security & Best Practices

✅ **Implemented:**
- CSRF protection (Livewire otomatis)
- GPS coordinate validation
- Leave conflict check
- Duplicate attendance prevention
- Role-based access control

⚠️ **Recommendations for Production:**
- Add rate limiting (prevent spam check-in)
- Add photo capture for attendance
- Add IP address logging
- Add geofencing backup (server-side)
- Encrypt GPS coordinates in database
- Add attendance approval workflow

---

## 📱 Browser Compatibility

| Browser | GPS Support | Tested |
|---------|------------|--------|
| Chrome | ✅ Yes | ✅ Passed |
| Firefox | ✅ Yes | ⚠️ Not tested |
| Edge | ✅ Yes | ⚠️ Not tested |
| Safari | ✅ Yes | ⚠️ Not tested |
| Mobile Chrome | ✅ Yes | ⚠️ Not tested |

**Note:** GPS requires HTTPS in production (except localhost)

---

## 🎯 Next Steps & Enhancements

### Suggested Improvements:
1. **Photo Capture**: Selfie saat check-in/out
2. **Notification**: Push notification untuk reminder check-out
3. **History Map**: Show all attendance locations on map
4. **Distance Display**: Show real-time distance from office
5. **Offline Support**: Queue check-in if no internet
6. **QR Code**: Alternative check-in method
7. **Face Recognition**: Anti-spoofing
8. **Reports**: Monthly attendance report dengan peta

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek section Troubleshooting di atas
2. Cek Laravel logs: `storage/logs/laravel.log`
3. Cek browser console (F12)
4. Pastikan seeder sudah dijalankan dengan benar

---

**Dibuat oleh:** GitHub Copilot  
**Tanggal:** {{ now() }}  
**Versi:** 2.0 (Improved from Original)  
**Status:** ✅ Fully Restored & Enhanced
