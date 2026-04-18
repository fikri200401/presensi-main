@extends('layouts.guest')
@section('title', 'IT Support')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-blue-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-6">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg> Kembali
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-3 mb-6"><div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg></div><h1 class="text-2xl font-bold text-gray-900">IT Support & Helpdesk</h1></div>
            <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4"><p class="text-sm text-blue-700"><strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 17:00 WIB</p></div>
                <h3 class="text-gray-900 font-semibold">Kontak</h3>
                <ul class="list-none space-y-2"><li>📧 Email: <strong>it-support@company.com</strong></li><li>📞 Telepon: <strong>(021) 1234-5678 ext. 100</strong></li><li>💬 WhatsApp: <strong>+62 812-3456-7890</strong></li></ul>
                <h3 class="text-gray-900 font-semibold">Panduan Umum</h3>
                <ul class="list-disc pl-5 space-y-1"><li>Lupa password? Hubungi IT Support untuk reset akun</li><li>Pastikan browser Chrome/Firefox versi terbaru</li><li>Aktifkan GPS/Location pada browser untuk fitur presensi</li><li>Clear cache browser jika mengalami masalah tampilan</li></ul>
            </div>
        </div>
    </div>
</div>
@endsection
