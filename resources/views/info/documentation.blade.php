@extends('layouts.guest')
@section('title', 'Dokumentasi')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg> Kembali
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg></div><h1 class="text-2xl font-bold text-gray-900">Dokumentasi Sistem</h1></div>
            <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
                <h3 class="text-gray-900 font-semibold">Tentang HRIS Portal</h3>
                <p>HRIS Portal adalah sistem manajemen sumber daya manusia terpadu yang dirancang untuk mengelola data karyawan, absensi, pengajuan cuti, dan penggajian secara efisien.</p>
                <h3 class="text-gray-900 font-semibold">Fitur Utama</h3>
                <ul class="list-disc pl-5 space-y-1"><li>Manajemen data karyawan dan jabatan</li><li>Presensi GPS-based dengan radius kantor</li><li>Pengajuan cuti dengan approval 3 level (Kadiv → HR → Direksi)</li><li>Slip gaji digital dan penggajian</li><li>Dashboard real-time dengan statistik</li><li>Notifikasi otomatis</li></ul>
                <h3 class="text-gray-900 font-semibold">Role & Hak Akses</h3>
                <ul class="list-disc pl-5 space-y-1"><li><strong>Super Admin</strong> — Full access ke semua fitur</li><li><strong>Admin / HR</strong> — Kelola master data, approval cuti level 2</li><li><strong>Kepala Divisi</strong> — Approval cuti level 1, lihat laporan</li><li><strong>Direksi</strong> — Final approval cuti, validasi gaji</li><li><strong>Karyawan</strong> — Presensi, ajukan cuti, lihat slip gaji</li></ul>
            </div>
        </div>
    </div>
</div>
@endsection
