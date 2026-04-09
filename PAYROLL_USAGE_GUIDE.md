# 📋 Panduan Penggunaan Sistem Payroll & Slip Gaji

## 🎯 Tujuan Sistem
Sistem Payroll ini dibuat untuk:
- Menghitung gaji karyawan secara otomatis berdasarkan data kehadiran
- Mengelola tunjangan dan potongan gaji
- Menghasilkan slip gaji digital
- Approval workflow untuk pembayaran gaji
- Compliance dengan peraturan ketenagakerjaan Indonesia (KEP. 102/MEN/VI/2004)

---

## 📝 Langkah-langkah Penggunaan

### STEP 1: Setup Konfigurasi Gaji Karyawan (WAJIB - Langkah Pertama!)

**Mengapa harus setup ini dulu?**
Sebelum bisa generate payroll, setiap karyawan HARUS punya konfigurasi gaji. Tanpa ini, sistem tidak tahu berapa gaji yang harus dibayarkan.

**Cara Setup:**

1. **Akses Menu Setup Gaji:**
   - Klik menu "Payroll & Slip Gaji" di sidebar
   - Klik tombol "Generate Payroll"
   - Jika belum ada karyawan, klik tombol "Setup Gaji Karyawan"
   
   ATAU
   
   - Akses langsung ke: `http://127.0.0.1:8000/employee-salary/create`

2. **Isi Form Konfigurasi Gaji:**

   **A. Informasi Dasar:**
   - **Karyawan**: Pilih nama karyawan dari dropdown
   - **Tipe Karyawan**: 
     - `Tetap` - Karyawan tetap full-time
     - `Harian` - Karyawan harian
     - `Paruh Waktu` - Karyawan part-time
   - **Metode Perhitungan**:
     - `Bulanan` - Gaji tetap per bulan (tidak peduli hari hadir)
       - Cocok untuk: Karyawan tetap dengan gaji bulanan
       - Contoh: Gaji Rp 5.000.000/bulan, mau hadir 20 hari atau 22 hari tetap dapat Rp 5.000.000
     - `Harian` - Gaji dihitung per hari hadir
       - Cocok untuk: Karyawan harian atau kontrak
       - Rumus: Gaji = Hari Hadir × Gaji per Hari
       - Contoh: Gaji pokok Rp 5.000.000, hadir 20 hari dari 21 hari = Rp 4.761.905
     - `Per Jam` - Gaji dihitung per jam kerja
       - Cocok untuk: Karyawan paruh waktu atau shift
       - Rumus: Gaji = Jam Kerja × Gaji per Jam
       - Contoh: Gaji pokok Rp 5.000.000 (Rp 28.902/jam), kerja 150 jam = Rp 4.335.300

   **B. Gaji Pokok:**
   - **Gaji Pokok Bulanan**: Masukkan nominal gaji pokok (contoh: 5000000)
   - **Auto-calculated** (akan muncul otomatis):
     - Gaji per hari = Gaji Pokok / 21 (sesuai KEP. 102/MEN/VI/2004)
     - Gaji per jam = Gaji Pokok / 173 (formula pemerintah)
   - **Berlaku Dari**: Tanggal mulai berlaku konfigurasi gaji

   **C. Tunjangan** (opsional, bisa diisi 0):
   - **Tunjangan Transport**: Uang transport (default: Rp 500.000)
   - **Tunjangan Makan**: Uang makan (default: Rp 750.000)
   - **Tunjangan Jabatan**: Tunjangan sesuai posisi/jabatan
   - **Tunjangan Keluarga**: Tunjangan istri/anak
   - **Tunjangan Lainnya**: Tunjangan tambahan lainnya

   **D. Potongan**:
   - **BPJS Kesehatan (%)**: Default 1% dari gaji (ditanggung karyawan)
   - **BPJS Ketenagakerjaan (%)**: Default 2% dari gaji (ditanggung karyawan)
   - **PPH21**: Pajak penghasilan (nominal rupiah)
   - **Potongan Lainnya**: Potongan lain seperti kasbon, dll.

3. **Klik "Simpan"**

4. **Ulangi untuk setiap karyawan** yang ingin Anda buatkan payroll

**Contoh Kasus:**
```
Karyawan: John Doe
Tipe: Tetap
Metode: Bulanan
Gaji Pokok: Rp 5.000.000

Tunjangan:
- Transport: Rp 500.000
- Makan: Rp 750.000
Total Tunjangan: Rp 1.250.000

Potongan:
- BPJS Kesehatan 1%: Rp 50.000
- BPJS Ketenagakerjaan 2%: Rp 100.000
Total Potongan: Rp 150.000

Gaji Kotor: Rp 5.000.000 + Rp 1.250.000 = Rp 6.250.000
Gaji Bersih (Take Home Pay): Rp 6.250.000 - Rp 150.000 = Rp 6.100.000
```

---

### STEP 2: Pastikan Ada Data Kehadiran (Attendance)

**Mengapa penting?**
Sistem payroll mengambil data dari tabel `attendances` untuk menghitung:
- Total hari hadir
- Total jam kerja
- Total keterlambatan

**Yang perlu dipastikan:**
1. Karyawan sudah melakukan check-in/check-out di sistem presensi
2. Ada data attendance untuk periode yang akan di-generate payroll
3. Status attendance adalah "hadir"

**Cara cek:**
- Klik menu "Attendance" di sidebar
- Filter by bulan yang akan di-generate payroll
- Pastikan ada data kehadiran karyawan

---

### STEP 3: Generate Payroll

Setelah semua karyawan punya konfigurasi gaji dan ada data attendance, baru bisa generate payroll.

**Cara Generate:**

1. **Akses Menu Payroll:**
   - Klik menu "Payroll & Slip Gaji" di sidebar
   - Klik tombol "Generate Payroll"

2. **Pilih Periode:**
   - Pilih bulan dan tahun (contoh: Februari 2026)
   - Sistem akan mengambil semua data attendance di bulan tersebut

3. **Pilih Karyawan:**
   - Centang karyawan yang mau di-generate payroll-nya
   - Bisa gunakan tombol "Pilih Semua" untuk select semua karyawan
   - Yang tampil hanya karyawan dengan konfigurasi gaji aktif
   - Akan terlihat info:
     - ✓ Gaji Pokok: Rp xxx (Metode)
     - ⚠ Belum ada konfigurasi gaji (jika belum setup)

4. **Baca Informasi Generate:**
   - Sistem akan mengambil data kehadiran dari periode yang dipilih
   - Gaji dihitung otomatis berdasarkan metode perhitungan
   - Tunjangan dan potongan diterapkan sesuai konfigurasi
   - Payroll yang sudah ada akan di-skip (tidak duplikat)
   - Status awal adalah "Draft"

5. **Klik "Generate Payroll"**

**Proses yang Terjadi:**
```
1. Sistem ambil data attendance periode Februari 2026
2. Hitung total hari hadir, jam kerja, keterlambatan
3. Hitung gaji berdasarkan metode:
   - Bulanan: Gaji tetap
   - Harian: Gaji per hari × Hari hadir
   - Jam: Gaji per jam × Jam kerja
4. Tambahkan tunjangan
5. Kurangi potongan (BPJS, PPH21, dll)
6. Simpan payroll dengan status "Draft"
```

---

### STEP 4: Review & Approve Payroll

**Workflow Status:**
```
Draft → Approved → Paid
  ↓
Rejected
```

**Cara Approve/Reject:**

1. **Lihat Daftar Payroll:**
   - Klik menu "Payroll & Slip Gaji"
   - Akan muncul tabel dengan kolom:
     - Karyawan (untuk admin)
     - Periode (contoh: Februari 2026)
     - Hari Hadir (contoh: 20/21 hari)
     - Gaji Kotor
     - Gaji Bersih
     - Status (Draft/Approved/Paid/Rejected)
     - Actions

2. **Review Payroll:**
   - Klik icon mata 👁️ untuk lihat detail slip gaji
   - Periksa perhitungan gaji, tunjangan, potongan
   - Pastikan semua benar

3. **Approve Payroll:**
   - Klik icon checklist hijau ✓
   - Konfirmasi approval
   - Status berubah menjadi "Approved"
   - Data approver dan waktu approval tersimpan

4. **Reject Payroll** (jika ada yang salah):
   - Klik icon X merah ✗
   - Muncul modal "Reject Payroll"
   - Masukkan alasan penolakan (minimal 10 karakter)
   - Klik "Reject Payroll"
   - Status berubah menjadi "Rejected"
   - Alasan tersimpan di field "catatan"

5. **Mark as Paid:**
   - Setelah status "Approved" dan gaji sudah ditransfer
   - Klik icon uang 💰 "Tandai Sudah Dibayar"
   - Status berubah menjadi "Paid"

6. **Hapus Draft:**
   - Payroll dengan status "Draft" bisa dihapus
   - Klik icon trash 🗑️
   - Konfirmasi penghapusan

---

### STEP 5: Lihat Slip Gaji (Untuk Karyawan)

**Sebagai Karyawan Biasa:**

1. **Login ke sistem**
2. **Klik menu "Slip Gaji"** di sidebar
3. **Lihat daftar slip gaji Anda:**
   - Hanya muncul slip gaji untuk user yang login
   - Filter by periode atau status
4. **Klik icon mata** untuk lihat detail slip gaji:
   - Periode gaji
   - Total hari kerja vs hari hadir
   - Breakdown gaji pokok
   - Detail tunjangan
   - Detail potongan
   - Gaji kotor dan bersih

---

## 🔍 Filter & Pencarian

**Filter yang tersedia:**

1. **Filter by Periode:**
   - Pilih bulan-tahun di dropdown "Periode"
   - Contoh: 2026-02 untuk Februari 2026

2. **Filter by Status:**
   - Semua Status
   - Draft - Belum approved
   - Pending - Menunggu approval
   - Approved - Sudah approved, siap bayar
   - Paid - Sudah dibayar
   - Rejected - Ditolak

3. **Klik tombol "Filter"** untuk apply filter

---

## 👥 Role & Permission

**Admin/Super Admin:**
- Bisa setup konfigurasi gaji karyawan
- Bisa generate payroll untuk semua karyawan
- Bisa approve/reject payroll
- Bisa mark as paid
- Bisa lihat semua payroll

**Karyawan Biasa:**
- Hanya bisa lihat slip gaji sendiri
- Tidak bisa generate, approve, atau reject
- Menu: "Slip Gaji"

---

## 📊 Contoh Perhitungan Gaji

### Contoh 1: Karyawan Tetap - Metode Bulanan
```
Karyawan: Ahmad
Gaji Pokok: Rp 6.000.000
Metode: Bulanan
Hari Hadir: 19/21 (sakit 2 hari)

Perhitungan:
Gaji Pokok: Rp 6.000.000 (tetap, tidak peduli hari hadir)
Tunjangan Transport: Rp 500.000
Tunjangan Makan: Rp 750.000
-----------------------------------------
Gaji Kotor: Rp 7.250.000

Potongan:
BPJS Kesehatan (1%): Rp 60.000
BPJS Ketenagakerjaan (2%): Rp 120.000
-----------------------------------------
Total Potongan: Rp 180.000

GAJI BERSIH: Rp 7.070.000
```

### Contoh 2: Karyawan Harian - Metode Harian
```
Karyawan: Budi
Gaji Pokok Bulanan: Rp 5.000.000
Gaji per Hari: Rp 238.095 (5.000.000 / 21)
Metode: Harian
Hari Kerja: 21 hari
Hari Hadir: 18 hari (izin 3 hari)

Perhitungan:
Gaji Pokok: Rp 238.095 × 18 = Rp 4.285.714
Tunjangan Transport: Rp 300.000
Tunjangan Makan: Rp 400.000
-----------------------------------------
Gaji Kotor: Rp 4.985.714

Potongan:
BPJS Kesehatan (1%): Rp 42.857
BPJS Ketenagakerjaan (2%): Rp 85.714
-----------------------------------------
Total Potongan: Rp 128.571

GAJI BERSIH: Rp 4.857.143
```

### Contoh 3: Karyawan Part-Time - Metode Per Jam
```
Karyawan: Citra
Gaji Pokok Bulanan: Rp 3.000.000
Gaji per Jam: Rp 17.341 (3.000.000 / 173)
Metode: Per Jam
Total Jam Kerja: 173 jam (standar)
Jam Hadir: 120 jam (part-time)

Perhitungan:
Gaji Pokok: Rp 17.341 × 120 = Rp 2.080.920
Tunjangan Transport: Rp 200.000
-----------------------------------------
Gaji Kotor: Rp 2.280.920

Potongan:
BPJS Kesehatan (1%): Rp 20.809
BPJS Ketenagakerjaan (2%): Rp 41.618
-----------------------------------------
Total Potongan: Rp 62.427

GAJI BERSIH: Rp 2.218.493
```

---

## ⚠️ Troubleshooting

### Problem 1: "Tidak ada karyawan" saat generate payroll
**Penyebab:** Belum ada karyawan dengan konfigurasi gaji aktif
**Solusi:** 
1. Klik tombol "Setup Gaji Karyawan"
2. Buat konfigurasi gaji untuk karyawan yang diinginkan
3. Kembali ke halaman Generate Payroll

### Problem 2: Payroll sudah ada untuk periode yang sama
**Penyebab:** Sistem mencegah duplikasi payroll
**Solusi:**
1. Cek di daftar payroll apakah sudah ada
2. Jika status "Draft", bisa dihapus dulu lalu generate ulang
3. Jika sudah "Approved" atau "Paid", tidak bisa generate ulang

### Problem 3: Gaji tidak sesuai dengan yang diharapkan
**Penyebab:** 
- Data attendance tidak lengkap
- Metode perhitungan salah
- Konfigurasi gaji belum update
**Solusi:**
1. Cek data attendance di periode tersebut
2. Review konfigurasi gaji karyawan
3. Pastikan metode perhitungan sudah benar
4. Jika sudah generate, reject payroll dan generate ulang

### Problem 4: Tidak bisa approve/reject
**Penyebab:** Bukan admin atau payroll sudah diproses
**Solusi:**
1. Pastikan login sebagai admin/super_admin
2. Cek status payroll (hanya "Draft" dan "Pending" yang bisa di-approve/reject)

---

## 📌 Tips & Best Practices

1. **Setup Gaji Dulu:**
   - Selalu setup konfigurasi gaji sebelum generate payroll
   - Review konfigurasi secara berkala

2. **Data Attendance Lengkap:**
   - Pastikan semua karyawan sudah check-in/out
   - Koreksi data attendance sebelum generate payroll

3. **Review Sebelum Approve:**
   - Selalu review detail slip gaji sebelum approve
   - Cek total hari hadir, jam kerja, dan perhitungan

4. **Generate di Awal Bulan:**
   - Generate payroll untuk bulan lalu di awal bulan
   - Contoh: Generate payroll Februari 2026 di tanggal 1-5 Maret 2026

5. **Backup Data:**
   - Sebelum mark as paid, pastikan data sudah di-backup
   - Export payroll report jika diperlukan

6. **Komunikasi:**
   - Informasikan ke karyawan saat payroll sudah approved
   - Beri akses ke karyawan untuk lihat slip gaji mereka

---

## 🎓 Formula & Regulasi

**Dasar Hukum:**
- KEP. 102/MEN/VI/2004 tentang Waktu Kerja Lembur dan Upah Kerja Lembur

**Formula Gaji per Jam:**
```
Gaji per Jam = (1/173) × Gaji Sebulan
```

**Formula Gaji per Hari:**
```
Gaji per Hari = (1/21) × Gaji Sebulan
```

**Keterangan:**
- 173 jam = 21 hari × 8 jam + 5 jam (Sabtu)
- 21 hari = Hari kerja efektif per bulan (Senin-Jumat)
- 8 jam = Jam kerja per hari

**Komponen Gaji:**
```
Gaji Kotor = Gaji Pokok + Total Tunjangan
Gaji Bersih = Gaji Kotor - Total Potongan
```

---

## 📞 Support

Jika ada pertanyaan atau kendala:
1. Hubungi admin sistem
2. Cek dokumentasi di `PAYROLL_SYSTEM_DOCUMENTATION.md`
3. Review source code di `app/Http/Controllers/PayrollController.php`

---

**Last Updated:** February 8, 2026
**Version:** 1.0.0
