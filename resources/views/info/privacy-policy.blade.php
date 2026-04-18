@extends('layouts.guest')
@section('title', 'Kebijakan Privasi')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg> Kembali
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg></div><h1 class="text-2xl font-bold text-gray-900">Kebijakan Privasi & Keamanan</h1></div>
            <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
                <h3 class="text-gray-900 font-semibold">Pengumpulan Data</h3>
                <p>Kami mengumpulkan data pribadi karyawan meliputi nama, email, nomor telepon, dan data lokasi (GPS) untuk keperluan presensi. Data ini hanya digunakan untuk operasional internal perusahaan.</p>
                <h3 class="text-gray-900 font-semibold">Keamanan Data</h3>
                <ul class="list-disc pl-5 space-y-1"><li>Semua data disimpan dengan enkripsi standar industri</li><li>Akses dibatasi berdasarkan role dan hak akses</li><li>Password di-hash menggunakan bcrypt</li><li>Audit trail untuk setiap perubahan data penting</li></ul>
                <h3 class="text-gray-900 font-semibold">Hak Karyawan</h3>
                <p>Setiap karyawan berhak mengakses, memperbarui, dan meminta penghapusan data pribadinya melalui menu Profil atau dengan menghubungi HR/Admin.</p>
            </div>
        </div>
    </div>
</div>
@endsection
