@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- =============================== --}}
{{-- ADMIN / SUPER ADMIN VIEW        --}}
{{-- =============================== --}}
@if(auth()->user()->hasRole(['super_admin', 'admin']))

<!-- Admin Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Dashboard Admin</h2>
        <p class="text-sm text-gray-500 mt-0.5">Selamat datang kembali, {{ auth()->user()->name }}. Berikut adalah ringkasan hari ini.</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        {{-- Real-time Mode toggle --}}
        <button type="button" id="btn-realtime"
                onclick="toggleRealtime()"
                class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
            <span id="realtime-dot" class="w-2 h-2 rounded-full bg-gray-300 flex-shrink-0"></span>
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="realtime-label">Real-time Mode</span>
        </button>
        <a href="{{ route('user.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
            </svg>
            Tambah Pegawai
        </a>
    </div>
</div>

<!-- Stat cards (Admin) -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4 mb-6">
    <!-- Absensi Hari Ini -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Absen Hari Ini</p>
            <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['today_attendance'] }} / {{ $stats['total_users'] }}</p>
        <div class="flex items-center gap-1 mt-1">
            <svg class="h-3 w-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
            <p class="text-xs text-green-600 font-medium">2.4%</p>
            <p class="text-xs text-gray-400 ml-1">91% kehadiran</p>
        </div>
    </div>

    <!-- Total Karyawan -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Karyawan</p>
            <div class="h-10 w-10 rounded-xl bg-orange-50 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
        <div class="flex items-center gap-1 mt-1">
            <p class="text-xs text-blue-600 font-medium">+{{ max(1, round($stats['total_users'] * 0.01)) }}</p>
            <p class="text-xs text-gray-400 ml-1">Karyawan Aktif</p>
        </div>
    </div>

    <!-- Izin & Cuti Pending -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Izin &amp; Cuti</p>
            <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_leaves'] }}</p>
        <div class="flex items-center gap-1 mt-1">
            <svg class="h-3 w-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
            <p class="text-xs text-amber-600 font-medium">+5%</p>
            <p class="text-xs text-gray-400 ml-1">Menunggu Persetujuan</p>
        </div>
    </div>

    <!-- Status Payroll -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Status Payroll</p>
            <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900">Completed</p>
        <p class="text-xs text-gray-400 mt-1">Periode {{ now()->format('F Y') }}</p>
    </div>
</div>

<!-- Main grid: Chart + Aktivitas Terbaru -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
    <!-- Chart area -->
    <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-900">Statistik Kehadiran Bulanan</h3>
                <p class="text-xs text-gray-400 mt-0.5">Ringkasan data kehadiran seluruh departemen</p>
            </div>
            <button type="button" id="btn-ekspor"
                    onclick="eksporChart()"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Ekspor Detail
            </button>
        </div>
        <canvas id="attendanceChart" height="160"></canvas>
        <!-- Legend -->
        <div class="flex items-center justify-center gap-4 mt-3 pt-3 border-t border-gray-50">
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-blue-500"></div><span class="text-xs text-gray-500">Hadir</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-yellow-400"></div><span class="text-xs text-gray-500">Izin</span></div>
            <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-sm bg-red-400"></div><span class="text-xs text-gray-500">Alpa</span></div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-900">Aktivitas Terbaru</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">3 Baru</span>
        </div>
        <div class="space-y-4">
            @forelse($recentAttendances->take(3) as $att)
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $att->user->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">
                        Check-in {{ $att->start_time ? \Carbon\Carbon::parse($att->start_time)->format('H:i') : '-' }}
                    </p>
                    <p class="text-[10px] text-gray-300 mt-0.5">{{ $att->created_at?->diffForHumans() ?? 'Baru saja' }}</p>
                </div>
            </div>
            @empty
            <!-- Static fallback items -->
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Permohonan Cuti Baru</p>
                    <p class="text-xs text-gray-400">Belum ada data</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-500 flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Laporan Bulanan Tersedia</p>
                    <p class="text-xs text-gray-400">Sistem</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-red-50 flex items-center justify-center text-red-500 flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Login dari Perangkat Baru</p>
                    <p class="text-xs text-gray-400">Sistem</p>
                </div>
            </div>
            @endforelse
        </div>
        <div class="mt-4 pt-3 border-t border-gray-50">
            <a href="{{ route('attendance.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 transition-colors">Lihat Semua Notifikasi &rarr;</a>
        </div>
    </div>
</div>

<!-- Login Pegawai Terbaru table -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-4 border-b border-gray-100 gap-3">
        <div>
            <h3 class="font-semibold text-gray-900">Login Pegawai Terbaru</h3>
            <p class="text-xs text-gray-400 mt-0.5">Monitoring akses sistem secara real-time</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </div>
                <input type="text" placeholder="Cari pegawai..." class="block w-40 rounded-xl border border-gray-200 bg-gray-50 pl-8 pr-3 py-1.5 text-xs text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500/20">
            </div>
            <button type="button" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">Filter</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Jabatan / Role</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu Masuk</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Autentikasi</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-50">
                @forelse($recentAttendances as $attendance)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ substr($attendance->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $attendance->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-400">{{ $attendance->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-600">
                        {{ $attendance->user->schedule->shift->name ?? ($attendance->user->roles->first()?->name ?? '-') }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-1.5 text-sm text-gray-600">
                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') . ' AM' : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        @if($attendance->end_time)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Terverifikasi
                            </span>
                        @elseif($attendance->start_time)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700">
                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                                Perlu Review
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-center">
                        <div x-data="{ open: false }" class="relative inline-block">
                            <button @click="open = !open" type="button" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 12.75a.75.75 0 110-1.5.75.75 0 010 1.5zM12 18.75a.75.75 0 110-1.5.75.75 0 010 1.5z" /></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" class="absolute right-0 z-10 mt-1 w-36 origin-top-right rounded-xl bg-white py-1 shadow-lg ring-1 ring-gray-900/5 text-left" style="display:none">
                                <a href="{{ route('attendance.index') }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Lihat Detail</a>
                                <a href="{{ route('user.edit', $attendance->user) }}" class="block px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Edit Profil</a>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                        Belum ada data presensi hari ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- =============================== --}}
{{-- ADMIN SCRIPTS                   --}}
{{-- =============================== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
// ── Chart.js ──────────────────────────────────────────────────────────────
const chartData = {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
    hadir:  [432, 510, 360, 540, 480, 570],
    izin:   [ 60,  48,  72,  36,  54,  30],
    alpa:   [ 30,  24,  48,  18,  36,  12],
};

const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Hadir',
                data: chartData.hadir,
                backgroundColor: '#3b82f6',
                borderRadius: 4,
                borderSkipped: false,
            },
            {
                label: 'Izin',
                data: chartData.izin,
                backgroundColor: '#facc15',
                borderRadius: 4,
                borderSkipped: false,
            },
            {
                label: 'Alpa',
                data: chartData.alpa,
                backgroundColor: '#f87171',
                borderRadius: 4,
                borderSkipped: false,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1f2937',
                titleColor: '#f9fafb',
                bodyColor: '#d1d5db',
                padding: 10,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                    title: (items) => 'Bulan: ' + items[0].label,
                    label: (item) => {
                        const total = chartData.hadir[item.dataIndex] + chartData.izin[item.dataIndex] + chartData.alpa[item.dataIndex];
                        const pct = ((item.raw / total) * 100).toFixed(1);
                        return ` ${item.dataset.label}: ${item.raw} presensi (${pct}%)`;
                    },
                    afterBody: (items) => {
                        const i = items[0].dataIndex;
                        const total = chartData.hadir[i] + chartData.izin[i] + chartData.alpa[i];
                        return [`─────────────`, `Total: ${total} presensi`];
                    },
                },
            },
        },
        scales: {
            x: {
                stacked: true,
                grid: { display: false },
                ticks: { color: '#9ca3af', font: { size: 11 } },
            },
            y: {
                stacked: true,
                grid: { color: '#f3f4f6' },
                ticks: { color: '#9ca3af', font: { size: 10 } },
                max: 650,
            },
        },
    },
});

// ── Ekspor Detail ──────────────────────────────────────────────────────────
function eksporChart() {
    const btn = document.getElementById('btn-ekspor');
    btn.disabled = true;
    btn.innerHTML = `<svg class="h-3.5 w-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Mengekspor...`;

    setTimeout(() => {
        // Build CSV
        const rows = [['Bulan','Hadir (presensi)','Izin (presensi)','Alpa (presensi)','Total']];
        chartData.labels.forEach((m, i) => {
            const total = chartData.hadir[i] + chartData.izin[i] + chartData.alpa[i];
            rows.push([m, chartData.hadir[i], chartData.izin[i], chartData.alpa[i], total]);
        });
        const csv = rows.map(r => r.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const url  = URL.createObjectURL(blob);
        const a    = document.createElement('a');
        a.href     = url;
        a.download = 'statistik-kehadiran-' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        URL.revokeObjectURL(url);

        btn.disabled = false;
        btn.innerHTML = `<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg> Ekspor Detail`;
    }, 600);
}

// ── Real-time Mode ─────────────────────────────────────────────────────────
let realtimeActive = false;
let realtimeTimer  = null;

function toggleRealtime() {
    const btn   = document.getElementById('btn-realtime');
    const dot   = document.getElementById('realtime-dot');
    const label = document.getElementById('realtime-label');

    realtimeActive = !realtimeActive;

    if (realtimeActive) {
        dot.classList.replace('bg-gray-300', 'bg-green-400');
        dot.classList.add('animate-pulse');
        label.textContent = 'Live — Aktif';
        btn.classList.add('border-green-200', 'text-green-700');
        btn.classList.remove('border-gray-200', 'text-gray-700');

        // Refresh halaman setiap 30 detik
        realtimeTimer = setInterval(() => { window.location.reload(); }, 30000);
    } else {
        dot.classList.replace('bg-green-400', 'bg-gray-300');
        dot.classList.remove('animate-pulse');
        label.textContent = 'Real-time Mode';
        btn.classList.remove('border-green-200', 'text-green-700');
        btn.classList.add('border-gray-200', 'text-gray-700');

        clearInterval(realtimeTimer);
        realtimeTimer = null;
    }
}
</script>

{{-- =============================== --}}
{{-- EMPLOYEE / KADIV / DIREKSI VIEW --}}
{{-- =============================== --}}
@else

@php
    $hasSchedule = \App\Models\Schedule::where('user_id', auth()->id())->exists();
@endphp

<!-- Employee greeting -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h2>
        <p class="text-sm text-gray-500 mt-0.5">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="flex items-center gap-2 text-xs font-medium text-gray-500 bg-white border border-gray-200 rounded-xl px-3 py-2">
        <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ now()->format('H:i') }} WIB
    </div>
</div>

<!-- Presensi Harian + Stats -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
    <!-- Presensi Harian card -->
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Presensi Harian</p>
                    <h3 class="text-lg font-bold text-gray-900 mt-0.5">
                        @if(!$hasSchedule)
                            Jadwal kerja belum diatur
                        @elseif(!$todayAttendance)
                            Belum check-in hari ini
                        @elseif(!$todayAttendance->end_time)
                            Sudah Check-In &mdash; Belum Check-Out
                        @else
                            Presensi Selesai ✅
                        @endif
                    </h3>
                </div>
                @if(!$hasSchedule)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Belum Ada Jadwal</span>
                @elseif($todayAttendance && $todayAttendance->end_time)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">Selesai</span>
                @elseif($todayAttendance)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700">Aktif</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">Belum Absen</span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
                <div class="bg-gray-50 rounded-xl p-3.5">
                    <p class="text-xs text-gray-400 mb-1">Jam Masuk</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $todayAttendance?->start_time ? \Carbon\Carbon::parse($todayAttendance->start_time)->format('H:i') : '--:--' }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-xl p-3.5">
                    <p class="text-xs text-gray-400 mb-1">Jam Keluar</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ $todayAttendance?->end_time ? \Carbon\Carbon::parse($todayAttendance->end_time)->format('H:i') : '--:--' }}
                    </p>
                </div>
            </div>

            @if($hasSchedule)
            <a href="{{ route('presensi') }}"
               class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-xl transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                @if(!$todayAttendance)
                    Check In Sekarang
                @elseif(!$todayAttendance->end_time)
                    Check Out Sekarang
                @else
                    Lihat Detail Presensi
                @endif
            </a>
            @else
            <div class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-400 font-semibold py-3 px-4 rounded-xl">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                Hubungi admin untuk mengatur jadwal kerja
            </div>
            @endif
        </div>
    </div>

    <!-- Employee stat cards -->
    <div class="flex flex-col gap-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Sisa Cuti Tahunan</p>
                <p class="text-xl font-bold text-gray-900">12 <span class="text-sm font-normal text-gray-400">hari</span></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Kehadiran Bulan Ini</p>
                <p class="text-xl font-bold text-gray-900">{{ $stats['today_attendance'] }} <span class="text-sm font-normal text-gray-400">hari</span></p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
            </div>
            <div>
                <p class="text-xs text-gray-400">Terlambat</p>
                <p class="text-xl font-bold text-gray-900">0 <span class="text-sm font-normal text-gray-400">kali</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Layanan Cepat -->
<div class="mb-6">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Layanan Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @if($hasSchedule)
        <a href="{{ route('presensi') }}" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 hover:border-blue-200 hover:shadow-sm transition-all group">
            <div class="h-11 w-11 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-700">Presensi</p>
        </a>
        @else
        <div class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 opacity-50 cursor-not-allowed">
            <div class="h-11 w-11 rounded-xl bg-gray-100 flex items-center justify-center">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-400">Presensi</p>
        </div>
        @endif
        <a href="{{ route('leave.create') }}" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 hover:border-amber-200 hover:shadow-sm transition-all group">
            <div class="h-11 w-11 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-700">Ajukan Izin</p>
        </a>
        <a href="{{ route('attendance.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 hover:border-green-200 hover:shadow-sm transition-all group">
            <div class="h-11 w-11 rounded-xl bg-green-50 group-hover:bg-green-100 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-700">Riwayat</p>
        </a>
        <a href="{{ route('payroll.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 hover:border-blue-200 hover:shadow-sm transition-all group">
            <div class="h-11 w-11 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-700">Slip Gaji</p>
        </a>
        <a href="{{ route('leave.index') }}" class="bg-white rounded-2xl border border-gray-100 p-4 flex flex-col items-center text-center gap-2.5 hover:border-blue-200 hover:shadow-sm transition-all group">
            <div class="h-11 w-11 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            </div>
            <p class="text-xs font-semibold text-gray-700">Riwayat Cuti</p>
        </a>
    </div>
</div>

<!-- Statistik Kehadiran chart -->
<div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="font-semibold text-gray-900">Statistik Kehadiran</h3>
        <span class="text-xs text-gray-400">6 bulan terakhir</span>
    </div>
    <div class="flex items-end gap-4 h-40">
        @php $months6 = ['Jul','Agu','Sep','Okt','Nov','Des']; @endphp
        @foreach($months6 as $m)
        @php $h = rand(40, 95); @endphp
        <div class="flex-1 flex flex-col items-center gap-1.5">
            <div class="w-full rounded-t-lg bg-blue-500" style="height: {{ $h }}%"></div>
            <span class="text-[10px] text-gray-400 font-medium">{{ $m }}</span>
        </div>
        @endforeach
    </div>
</div>

<!-- Aktivitas Terakhir -->
<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900">Aktivitas Terakhir</h3>
        <a href="{{ route('attendance.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700">Lihat semua &rarr;</a>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($recentAttendances->take(5) as $attendance)
        <div class="flex items-center gap-4 px-6 py-3 hover:bg-gray-50/50 transition-colors">
            <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                {{ substr($attendance->user->name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $attendance->user->name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-400">
                    Masuk: {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}
                    @if($attendance->end_time)
                        &bull; Keluar: {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                    @endif
                </p>
            </div>
            <div>
                @if($attendance->end_time)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Selesai</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Hadir</span>
                @endif
            </div>
        </div>
        @empty
        <div class="px-6 py-8 text-center text-sm text-gray-400">
            Belum ada aktivitas tercatat.
        </div>
        @endforelse
    </div>
</div>

@endif
@endsection
