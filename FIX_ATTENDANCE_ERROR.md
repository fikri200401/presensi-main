# Fix: Error Submit Attendance - end_time Cannot Be NULL

## 🐛 Problem
Saat employee melakukan check-in (submit attendance pertama kali), muncul error:
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'end_time' cannot be null
```

## 🔍 Root Cause
Kolom `end_time` di tabel `attendances` tidak diset sebagai **nullable** di database, padahal saat check-in pertama kali, kolom ini harus NULL karena employee belum check-out.

## ✅ Solution

### 1. Migration Baru
Dibuat migration baru untuk mengubah kolom `end_time` menjadi nullable:

**File**: `database/migrations/2026_02_08_023051_make_end_time_nullable_in_attendances_table.php`

```php
public function up(): void
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->time('end_time')->nullable()->change();
    });
}
```

### 2. Struktur Database yang Benar
Setelah migration, struktur tabel `attendances`:

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| id | bigint | NO | Primary key |
| user_id | bigint | NO | Foreign key ke users |
| schedule_latitude | double | NO | Latitude kantor dari schedule |
| schedule_longitude | double | NO | Longitude kantor dari schedule |
| schedule_start_time | time | NO | Jam masuk dari shift |
| schedule_end_time | time | NO | Jam pulang dari shift |
| start_latitude | double | NO | Latitude saat check-in |
| start_longitude | double | NO | Longitude saat check-in |
| end_latitude | double | **YES** | Latitude saat check-out (nullable) |
| end_longitude | double | **YES** | Longitude saat check-out (nullable) |
| start_time | time | NO | Waktu check-in |
| end_time | time | **YES** | Waktu check-out (nullable) ✅ |
| created_at | timestamp | YES | |
| updated_at | timestamp | YES | |
| deleted_at | timestamp | YES | Soft delete |

## 🔄 Flow Attendance

### Check-In (Pertama Kali)
```php
Attendance::create([
    'user_id' => Auth::user()->id,
    'schedule_latitude' => $schedule->office->latitude,
    'schedule_longitude' => $schedule->office->longitude,
    'schedule_start_time' => $schedule->shift->start_time,
    'schedule_end_time' => $schedule->shift->end_time,
    'start_latitude' => $this->latitude,
    'start_longitude' => $this->longitude,
    'start_time' => Carbon::now()->toTimeString(),
    'end_time' => null,  // ✅ NULL saat check-in
]);
```

### Check-Out (Update)
```php
$attendance->update([
    'end_latitude' => $this->latitude,
    'end_longitude' => $this->longitude,
    'end_time' => Carbon::now()->toTimeString(),  // ✅ Diisi saat check-out
]);
```

## 📝 Steps Taken

1. ✅ Identified the issue: `end_time` column not nullable
2. ✅ Created new migration: `make_end_time_nullable_in_attendances_table`
3. ✅ Updated migration to make `end_time` nullable
4. ✅ Ran migration: `php artisan migrate`
5. ✅ Cleared cache: `php artisan cache:clear`, `config:clear`, `view:clear`

## 🧪 Testing

### Test Check-In
1. Login sebagai employee
2. Buka halaman presensi
3. Klik "Tag Location"
4. Allow GPS permission
5. Klik "Submit Presensi"
6. ✅ Should show: "Check-in berhasil! Selamat bekerja."

### Test Check-Out
1. Setelah check-in sukses
2. Klik "Tag Location" lagi
3. Klik "Submit Presensi"
4. ✅ Should show: "Check-out berhasil! Terima kasih atas kerja keras Anda hari ini."

### Verify Database
```sql
SELECT id, user_id, start_time, end_time 
FROM attendances 
WHERE DATE(created_at) = CURDATE()
ORDER BY id DESC;
```

**Expected Result:**
- Check-in: `end_time` = NULL ✅
- Check-out: `end_time` = '17:30:00' (example) ✅

## 🎯 Result

✅ **Problem SOLVED!**
- Employee sekarang bisa check-in tanpa error
- Kolom `end_time` nullable, bisa NULL saat check-in
- Check-out tetap berfungsi normal

## 📁 Files Modified

1. **database/migrations/2026_02_08_023051_make_end_time_nullable_in_attendances_table.php** - NEW
   - Migration untuk make end_time nullable

2. **database/migrations/2025_09_21_141515_create_attendances_table.php** - UPDATED
   - Updated untuk dokumentasi (tidak perlu re-run karena sudah ada migration alter)

## ⚠️ Important Notes

1. **Tidak perlu rollback migration lama** - Migration baru hanya mengubah kolom yang sudah ada
2. **Data existing tetap aman** - Migration hanya mengubah constraint, tidak mengubah data
3. **Compatible dengan kode existing** - Kode di `Presensi.php` sudah benar dari awal

## 🚀 Commands Run

```bash
# Create migration
php artisan make:migration make_end_time_nullable_in_attendances_table

# Run migration
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

**Fixed Date**: February 8, 2026  
**Status**: ✅ RESOLVED  
**Impact**: All employees can now check-in successfully
