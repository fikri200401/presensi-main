@extends('layouts.guest')

@section('title', 'Login')

@section('content')
@php
    $setting = $landingSetting ?? \App\Models\LandingPageSetting::current();
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex flex-col">

    <!-- Top bar -->
    <div class="flex items-center justify-between px-6 py-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            Kembali ke Beranda
        </a>
        <a href="{{ route('info.it-support') }}" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Contact IT Support</a>
    </div>

    <!-- Login Card -->
    <div class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">

                <!-- Logo & Title -->
                <div class="text-center mb-8">
                    @if($setting->logo_url)
                        <img src="{{ $setting->logo_url }}" alt="{{ $setting->brand_name }}" class="mx-auto mb-4 h-16 w-16 rounded-2xl border border-gray-100 bg-white object-contain p-2 shadow-lg shadow-blue-100">
                    @else
                        <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 rounded-2xl shadow-lg shadow-blue-200 mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                        </div>
                    @endif
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $setting->brand_name }}</h2>
                    <p class="text-sm text-gray-500">{{ $setting->brand_subtitle ?: 'Masukkan kredensial Anda untuk mengakses sistem HRIS' }}</p>
                </div>

                @if(session('error'))
                    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-100 rounded-xl p-4">
                        <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-100 rounded-xl p-4">
                        <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <ul class="text-sm text-red-700 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="space-y-5" action="{{ route('login') }}" method="POST">
                    @csrf
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email atau ID Karyawan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                   placeholder="contoh: HR-12345"
                                   class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                            <a href="{{ route('info.it-support') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lupa sandi?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   placeholder="••••••••"
                                   class="block w-full pl-10 pr-4 py-2.5 text-sm text-gray-900 border border-gray-200 rounded-xl bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:bg-white transition-all">
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="remember" class="text-sm text-gray-600">Ingat saya di perangkat ini</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl shadow-md shadow-blue-200 transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm">
                        Login Sekarang
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-400 mb-2">⚠ Kesulitan mengakses akun Anda?</p>
                    <div class="flex items-center justify-center gap-4">
                        <a href="{{ route('info.it-support') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Hubungi IT Support</a>
                        <span class="text-gray-300">•</span>
                        <a href="{{ route('info.user-guide') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Panduan Pengguna</a>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                    <p class="text-xs font-semibold text-blue-700 mb-3 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                        Akun Demo - klik untuk mengisi otomatis
                    </p>
                    <div class="space-y-2">
                        <button type="button" onclick="fillDemo('superadmin@presensi.com','password')"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-blue-200 bg-white hover:bg-blue-50 px-3 py-2 text-left transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-purple-100 text-purple-700 text-[10px] font-bold flex-shrink-0">SA</span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Super Admin</p>
                                    <p class="text-[10px] text-gray-400">superadmin@presensi.com</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-blue-500 group-hover:text-blue-700 font-medium flex-shrink-0">Isi →</span>
                        </button>
                        <button type="button" onclick="fillDemo('admin@presensi.com','password')"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-blue-200 bg-white hover:bg-blue-50 px-3 py-2 text-left transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-blue-100 text-blue-700 text-[10px] font-bold flex-shrink-0">AD</span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Admin</p>
                                    <p class="text-[10px] text-gray-400">admin@presensi.com</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-blue-500 group-hover:text-blue-700 font-medium flex-shrink-0">Isi →</span>
                        </button>
                        <button type="button" onclick="fillDemo('kadiv@presensi.com','password')"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-blue-200 bg-white hover:bg-blue-50 px-3 py-2 text-left transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-amber-100 text-amber-700 text-[10px] font-bold flex-shrink-0">KD</span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Kepala Divisi</p>
                                    <p class="text-[10px] text-gray-400">kadiv@presensi.com</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-blue-500 group-hover:text-blue-700 font-medium flex-shrink-0">Isi →</span>
                        </button>
                        <button type="button" onclick="fillDemo('direksi@presensi.com','password')"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-blue-200 bg-white hover:bg-blue-50 px-3 py-2 text-left transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-red-100 text-red-700 text-[10px] font-bold flex-shrink-0">DR</span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Direksi</p>
                                    <p class="text-[10px] text-gray-400">direksi@presensi.com</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-blue-500 group-hover:text-blue-700 font-medium flex-shrink-0">Isi →</span>
                        </button>
                        <button type="button" onclick="fillDemo('budi.santoso@presensi.com','password')"
                                class="w-full flex items-center justify-between gap-2 rounded-lg border border-blue-200 bg-white hover:bg-blue-50 px-3 py-2 text-left transition-colors group">
                            <div class="flex items-center gap-2.5">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-green-100 text-green-700 text-[10px] font-bold flex-shrink-0">EK</span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800">Karyawan</p>
                                    <p class="text-[10px] text-gray-400">budi.santoso@presensi.com</p>
                                </div>
                            </div>
                            <span class="text-[10px] text-blue-500 group-hover:text-blue-700 font-medium flex-shrink-0">Isi →</span>
                        </button>
                    </div>
                    <p class="text-[10px] text-gray-400 text-center mt-2.5">Semua akun demo menggunakan password: <span class="font-mono font-semibold text-gray-600">password</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom bar -->
    <div class="flex items-center justify-between px-6 py-4 text-xs text-gray-400">
        <div class="flex items-center gap-2">
            <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
            Sistem Operasional: Normal
        </div>
        <div class="flex gap-4">
            <a href="{{ route('info.privacy-policy') }}" class="hover:text-gray-600">Privacy Policy</a>
            <a href="{{ route('info.privacy-policy') }}" class="hover:text-gray-600">Terms of Service</a>
            <a href="{{ route('info.it-support') }}" class="hover:text-gray-600">IT Helpdesk</a>
        </div>
    </div>
    <p class="text-center text-xs text-gray-400 pb-4">© {{ date('Y') }} {{ $setting->copyright_text }}</p>
</div>

<script>
function fillDemo(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    document.getElementById('email').focus();
}
</script>
@endsection
