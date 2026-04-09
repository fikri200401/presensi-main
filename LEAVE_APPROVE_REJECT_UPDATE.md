# Update Leave Management - Approve/Reject dengan Icon

## 🎯 Perubahan

### Sebelum
- ❌ Harus klik "Edit" untuk approve/reject leave
- ❌ Membuka halaman edit terpisah
- ❌ Tidak ada field untuk alasan penolakan
- ❌ Proses tidak efisien

### Sesudah
- ✅ Icon approve (✓) dan reject (✗) langsung di kolom Actions
- ✅ Tidak perlu halaman edit terpisah
- ✅ Modal popup untuk input alasan penolakan
- ✅ Proses lebih cepat dan efisien

## 🎨 Fitur Baru

### 1. Icon Actions untuk Admin

| Status | Icon Approve | Icon Reject | Icon Delete | Icon Info |
|--------|--------------|-------------|-------------|-----------|
| **Pending** | ✅ Hijau (Check) | ❌ Merah (X) | 🗑️ Abu-abu | - |
| **Approved** | - | - | 🗑️ Abu-abu | - |
| **Rejected** | - | - | 🗑️ Abu-abu | ℹ️ Biru (Lihat Alasan) |

### 2. Modal Rejection
- Muncul saat klik icon reject (❌)
- Wajib input alasan minimal 10 karakter
- Alasan disimpan di kolom `note` di database
- Dapat dilihat oleh admin dan employee

### 3. Employee View
- Employee yang di-reject bisa klik tombol "Lihat Alasan"
- Menampilkan alasan penolakan dari admin

## 🔧 Technical Details

### Routes Baru
```php
POST /leave/{leave}/approve  → approve leave
POST /leave/{leave}/reject   → reject leave dengan alasan
```

### Controller Methods

#### Approve
```php
public function approve(Leave $leave)
{
    $leave->update([
        'status' => 'approved',
        'note' => null
    ]);
    return redirect()->route('leave.index')
        ->with('success', 'Leave request approved successfully');
}
```

#### Reject
```php
public function reject(Request $request, Leave $leave)
{
    $validated = $request->validate([
        'rejection_reason' => 'required|string|min:10'
    ]);

    $leave->update([
        'status' => 'rejected',
        'note' => $validated['rejection_reason']
    ]);
    
    return redirect()->route('leave.index')
        ->with('success', 'Leave request rejected');
}
```

### Database Schema
```php
// Table: leaves
- note (text, nullable) → untuk menyimpan alasan penolakan
```

## 📋 Workflow

### Admin Approve Leave
1. Admin melihat list leave requests
2. Untuk leave dengan status "Pending", muncul icon:
   - ✅ Hijau (Approve)
   - ❌ Merah (Reject)
3. Klik ✅ → Langsung approve (dengan konfirmasi)
4. Status berubah jadi "Approved"

### Admin Reject Leave
1. Admin klik icon ❌ Merah
2. **Modal muncul** dengan form alasan penolakan
3. Admin input alasan minimal 10 karakter
4. Klik "Reject Leave"
5. Status berubah jadi "Rejected"
6. Alasan tersimpan di database

### Employee Lihat Alasan Reject
1. Employee lihat leave request yang di-reject
2. Ada tombol "Lihat Alasan" dengan icon ℹ️
3. Klik tombol → Muncul alert dengan alasan penolakan
4. Employee bisa memahami kenapa di-reject

## 🎨 UI Components

### Modal Rejection
```html
- Header: "Reject Leave Request" dengan tombol close (X)
- Form:
  - Textarea untuk alasan (required, min 10 chars)
  - Helper text: "Alasan penolakan akan dikirim ke karyawan"
- Buttons:
  - Cancel (Abu-abu)
  - Reject Leave (Merah)
```

### Icons SVG
- **Approve**: Circle dengan checkmark
- **Reject**: Circle dengan X
- **Delete**: Trash icon
- **Info**: Circle dengan i

## 📁 File yang Diubah

1. **app/Http/Controllers/LeaveController.php**
   - Method `approve()` - NEW
   - Method `reject()` - NEW

2. **routes/web.php**
   - Route `leave.approve` - NEW
   - Route `leave.reject` - NEW

3. **resources/views/leave/index.blade.php**
   - Modal rejection - NEW
   - Icon buttons untuk approve/reject
   - JavaScript untuk handle modal
   - Conditional rendering berdasarkan status

4. **app/Models/Leave.php**
   - Sudah ada `note` di fillable ✅

## 🔒 Authorization

### Admin/Super Admin
- ✅ Bisa approve leave
- ✅ Bisa reject leave dengan alasan
- ✅ Bisa delete leave
- ✅ Bisa lihat semua leave requests

### Employee
- ❌ Tidak bisa approve/reject
- ❌ Tidak bisa delete
- ✅ Bisa lihat leave mereka sendiri
- ✅ Bisa lihat alasan reject

## 🧪 Testing

### Test Approve
1. Login sebagai admin
2. Buka Leave Management
3. Cari leave dengan status "Pending"
4. Klik icon ✅ hijau
5. Konfirmasi "Approve leave request ini?"
6. ✅ Status berubah jadi "Approved"
7. ✅ Success message muncul

### Test Reject
1. Login sebagai admin
2. Buka Leave Management
3. Cari leave dengan status "Pending"
4. Klik icon ❌ merah
5. ✅ Modal muncul
6. Input alasan: "Kuota cuti bulan ini sudah penuh"
7. Klik "Reject Leave"
8. ✅ Status berubah jadi "Rejected"
9. ✅ Success message muncul
10. ✅ Icon info (ℹ️) muncul untuk lihat alasan

### Test View Rejection Reason
1. Login sebagai employee yang leave-nya di-reject
2. Buka Leave Management
3. ✅ Ada tombol "Lihat Alasan" di kolom Actions
4. Klik tombol
5. ✅ Alert muncul dengan alasan penolakan

### Test Admin View Rejection Reason
1. Login sebagai admin
2. Buka Leave Management
3. Leave yang rejected ada icon ℹ️
4. Klik icon
5. ✅ Alert muncul dengan alasan penolakan

## 🎯 Benefits

1. ✅ **Lebih Cepat** - Approve/reject langsung tanpa buka halaman baru
2. ✅ **Lebih Jelas** - Icon visual lebih mudah dipahami
3. ✅ **Transparansi** - Employee tau kenapa di-reject
4. ✅ **User Friendly** - Modal lebih modern dari halaman edit
5. ✅ **Audit Trail** - Alasan reject tersimpan di database

## ⚠️ Validation

### Rejection Reason
- **Required**: Wajib diisi
- **Minimal**: 10 karakter
- **Type**: Text/String

### Error Messages
```php
'rejection_reason.required' => 'Alasan penolakan wajib diisi'
'rejection_reason.min' => 'Alasan minimal 10 karakter'
```

## 📝 Contoh Alasan Penolakan

✅ Good Examples:
- "Kuota cuti bulan ini sudah penuh, mohon ajukan bulan depan"
- "Tanggal yang diminta bertabrakan dengan project deadline"
- "Sudah ada 3 karyawan cuti di tanggal yang sama"

❌ Bad Examples (terlalu pendek):
- "Tidak bisa"
- "Ditolak"
- "Nanti"

---

**Update Date**: February 8, 2026  
**Status**: ✅ Implemented  
**Type**: Feature Enhancement
