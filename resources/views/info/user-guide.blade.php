@extends('layouts.guest')
@section('title', 'Panduan Pengguna')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg> Kembali
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" /></svg></div><h1 class="text-2xl font-bold text-gray-900">Panduan Pengguna</h1></div>
            <div class="prose prose-sm max-w-none text-gray-600 space-y-5">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4"><p class="text-sm text-blue-700">📌 Panduan ini berlaku untuk semua role pengguna. Pastikan Anda membaca bagian yang sesuai dengan role Anda.</p></div>
                <h3 class="text-gray-900 font-semibold">1. Login</h3>
                <p>Masukkan email dan password Anda di halaman login. Jika lupa password, hubungi IT Support.</p>
                <h3 class="text-gray-900 font-semibold">2. Presensi (Karyawan)</h3>
                <ol class="list-decimal pl-5 space-y-1"><li>Buka menu <strong>Presensi Saya</strong> di sidebar</li><li>Klik tombol <strong>"📍 Tag Location"</strong></li><li>Izinkan akses GPS di browser Anda</li><li>Jika berada dalam radius kantor, klik <strong>"✅ Submit Presensi"</strong></li></ol>
                <h3 class="text-gray-900 font-semibold">3. Pengajuan Cuti</h3>
                <ol class="list-decimal pl-5 space-y-1"><li>Buka menu <strong>Pengajuan Cuti</strong></li><li>Klik <strong>"Ajukan Cuti"</strong></li><li>Isi tanggal mulai, tanggal selesai, dan alasan</li><li>Pengajuan akan melalui approval 3 level</li></ol>
                <h3 class="text-gray-900 font-semibold">4. Slip Gaji</h3>
                <p>Buka menu <strong>Slip Gaji</strong> untuk melihat riwayat penggajian Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection
