<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRIS Portal - Sistem Manajemen SDM Terpadu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-white text-gray-800 antialiased">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">HRIS Portal</span>
                </div>
                <div class="hidden md:flex items-center gap-8">
                    <a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Dokumentasi</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">IT Support</a>
                    <a href="#" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Status Sistem</a>
                </div>
                @if(Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors">
                            Portal Login
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-28 pb-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-blue-100 mb-6">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                PORTAL INTERNAL PERUSAHAAN
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 leading-tight mb-6">
                Selamat Datang di <span class="text-blue-600">HRIS</span><br>
                <span class="text-blue-600">Portal</span> Terpadu
            </h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto mb-10 leading-relaxed">
                Kelola data karyawan, absensi, cuti, dan penggajian Anda dalam satu platform yang aman, efisien, dan transparan. Dirancang khusus untuk mendukung produktivitas setiap anggota tim.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                        Masuk ke Dashboard
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-blue-200 transition-all hover:-translate-y-0.5">
                        Masuk ke Dashboard
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" /></svg>
                    </a>
                @endauth
                <a href="#" class="inline-flex items-center gap-2 bg-white hover:bg-gray-50 text-gray-700 font-semibold px-6 py-3 rounded-xl border border-gray-200 transition-all hover:-translate-y-0.5">
                    Panduan Pengguna
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white border-t border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex flex-col items-start p-6 rounded-2xl border border-gray-100 hover:border-blue-100 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between w-full mb-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Terverifikasi</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Total Pegawai</p>
                    <p class="text-3xl font-bold text-gray-900">1,240</p>
                    <p class="text-xs text-gray-400 mt-1">Pegawai aktif di seluruh departemen</p>
                </div>
                <div class="flex flex-col items-start p-6 rounded-2xl border border-gray-100 hover:border-blue-100 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between w-full mb-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Terverifikasi</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Kehadiran Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900">94.2%</p>
                    <p class="text-xs text-gray-400 mt-1">Berdasarkan data scan wajah terbaru</p>
                </div>
                <div class="flex flex-col items-start p-6 rounded-2xl border border-gray-100 hover:border-blue-100 hover:shadow-sm transition-all">
                    <div class="flex items-center justify-between w-full mb-4">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                        </div>
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Terverifikasi</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-1">Proyek Aktif</p>
                    <p class="text-3xl font-bold text-gray-900">42</p>
                    <p class="text-xs text-gray-400 mt-1">Penempatan di berbagai lokasi strategis</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Navigasi Fitur Portal</h2>
                    <p class="text-gray-500 mb-10 leading-relaxed">Akses semua kebutuhan administrasi Anda dengan mudah. Portal ini dirancang untuk menyederhanakan proses birokrasi internal perusahaan.</p>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Absensi & Kehadiran</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Pantau riwayat kehadiran dan lakukan clock-in/out melalui sistem Face Recognition yang presisi.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Manajemen Cuti</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Ajukan cuti atau izin dengan persetujuan digital cepat. Pantau sisa kuota cuti tahunan Anda secara real-time.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Profil Karyawan</h3>
                                <p class="text-sm text-gray-500 leading-relaxed">Kelola data pribadi, informasi kontrak, dan riwayat promosi Anda dalam satu tempat yang aman.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Preview Mockup -->
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="flex items-center gap-1.5 px-4 py-3 border-b border-gray-100">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                            <div class="flex-1 mx-4 bg-gray-100 rounded-full h-5 px-3 flex items-center">
                                <span class="text-xs text-gray-400">hris.perusahaan.com/dashboard</span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="flex gap-3 mb-4">
                                <div class="w-24 bg-gray-50 rounded-lg h-full py-6 flex flex-col gap-2 px-2 border border-gray-100">
                                    <div class="h-2 bg-blue-600 rounded-full w-3/4"></div>
                                    <div class="h-2 bg-gray-200 rounded-full w-full"></div>
                                    <div class="h-2 bg-gray-200 rounded-full w-5/6"></div>
                                    <div class="h-2 bg-gray-200 rounded-full w-4/5"></div>
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="bg-blue-50 rounded-lg p-2"><div class="h-1.5 bg-blue-200 rounded w-full mb-1.5"></div><div class="h-3 bg-blue-600 rounded w-2/3"></div></div>
                                        <div class="bg-green-50 rounded-lg p-2"><div class="h-1.5 bg-green-200 rounded w-full mb-1.5"></div><div class="h-3 bg-green-500 rounded w-2/3"></div></div>
                                        <div class="bg-orange-50 rounded-lg p-2"><div class="h-1.5 bg-orange-200 rounded w-full mb-1.5"></div><div class="h-3 bg-orange-500 rounded w-2/3"></div></div>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                        <div class="flex gap-1 items-end h-16">
                                            <div class="flex-1 bg-blue-500 rounded-t" style="height:60%"></div>
                                            <div class="flex-1 bg-blue-500 rounded-t" style="height:80%"></div>
                                            <div class="flex-1 bg-blue-500 rounded-t" style="height:50%"></div>
                                            <div class="flex-1 bg-blue-500 rounded-t" style="height:90%"></div>
                                            <div class="flex-1 bg-blue-500 rounded-t" style="height:70%"></div>
                                            <div class="flex-1 bg-blue-300 rounded-t" style="height:40%"></div>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <div class="h-2 bg-gray-100 rounded-full w-full"></div>
                                        <div class="h-2 bg-gray-100 rounded-full w-5/6"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-4 px-4 mb-16">
        <div class="max-w-7xl mx-auto bg-blue-600 rounded-3xl py-14 px-8 sm:px-16">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div>
                    <h2 class="text-2xl font-bold text-white mb-2">Siap Memulai Hari Kerja Anda?</h2>
                    <p class="text-blue-100 text-sm max-w-md">Akses Dashboard Karyawan sekarang untuk melihat tugas hari ini dan melakukan absensi kehadiran.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center bg-white text-blue-600 font-semibold px-5 py-2.5 rounded-xl hover:bg-blue-50 transition-colors text-sm">Portal Login</a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-white text-blue-600 font-semibold px-5 py-2.5 rounded-xl hover:bg-blue-50 transition-colors text-sm">Portal Login</a>
                    @endauth
                    <a href="#" class="inline-flex items-center justify-center bg-blue-700 text-white font-semibold px-5 py-2.5 rounded-xl hover:bg-blue-800 transition-colors text-sm border border-blue-500">Kontak Admin</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-100 py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 bg-blue-600 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        </div>
                        <span class="font-bold text-gray-900">HRIS Portal</span>
                    </div>
                    <p class="text-xs text-gray-400 leading-relaxed">Integrated Human Resource Information System for modern enterprise management. Secure, efficient, and transparent.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Quick Access</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Employee Directory</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Knowledge Base</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">System Status</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Support</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Contact IT Helpdesk</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">HR Policy Manual</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Submit a Ticket</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Terms of Use</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Data Protection</a></li>
                        <li><a href="#" class="text-sm text-gray-500 hover:text-blue-600 transition-colors">Compliance</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p class="text-xs text-gray-400">© {{ date('Y') }} HRIS Internal Portal. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-xs text-gray-400 hover:text-gray-600">Privacy Policy</a>
                    <a href="#" class="text-xs text-gray-400 hover:text-gray-600">Terms of Service</a>
                    <a href="#" class="text-xs text-gray-400 hover:text-gray-600">IT Helpdesk</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
