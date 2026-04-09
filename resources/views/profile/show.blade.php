@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- ── SUCCESS ALERTS ────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
    </div>
    @endif
    @if(session('password_success'))
    <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <p class="text-sm text-green-700 font-medium">{{ session('password_success') }}</p>
        <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
    </div>
    @endif

    {{-- ── PROFILE HEADER CARD ───────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="h-32 bg-gradient-to-r from-blue-600 via-blue-500 to-cyan-500"></div>
        <div class="px-6 pb-6 -mt-14 sm:flex sm:items-end sm:gap-6">
            <div class="relative">
                @if($user->image)
                    <img src="{{ Storage::url($user->image) }}" alt="{{ $user->name }}"
                         class="h-28 w-28 rounded-2xl border-4 border-white shadow-lg object-cover bg-white">
                @else
                    <div class="h-28 w-28 rounded-2xl border-4 border-white shadow-lg bg-blue-600 flex items-center justify-center">
                        <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                @endif
                <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full bg-green-500 ring-2 ring-white"></span>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            @if($user->nip)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">NIP: {{ $user->nip }}</span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Status: Aktif</span>
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex gap-2 mt-2 sm:mt-0">
                        <a href="#edit-profile" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                            Edit Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT COLUMN (2/3) ─────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informasi Personal & Kontak --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-5">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    Informasi Personal & Kontak
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Email</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Telepon / WA</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->phone ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Alamat Domisili</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->address ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Tanggal Lahir</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->birth_date ? $user->birth_date->translatedFormat('d F Y') : '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Divisi</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->division ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-shrink-0 h-10 w-10 bg-rose-100 rounded-lg flex items-center justify-center">
                            <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Tanggal Bergabung</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->created_at->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Riwayat Karir & Jabatan (Timeline) --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-5">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                    Riwayat Karir & Jabatan
                </h2>

                @if($user->positionHistories->count() > 0)
                <div class="relative">
                    {{-- Timeline line --}}
                    <div class="absolute left-[17px] top-2 bottom-2 w-0.5 bg-gray-200"></div>

                    <div class="space-y-0">
                        @foreach($user->positionHistories as $history)
                        <div class="relative flex gap-4 pb-6 last:pb-0">
                            {{-- Timeline dot --}}
                            <div class="relative z-10 flex-shrink-0 mt-1">
                                @if($history->is_current)
                                <div class="h-[34px] w-[34px] rounded-full bg-blue-600 flex items-center justify-center ring-4 ring-blue-100">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </div>
                                @else
                                <div class="h-[34px] w-[34px] rounded-full bg-white border-2 border-gray-300 flex items-center justify-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-gray-400"></div>
                                </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="p-4 rounded-xl {{ $history->is_current ? 'bg-gradient-to-r from-blue-50 to-blue-50/30 border border-blue-200' : 'bg-gray-50 border border-gray-100' }}">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <h3 class="text-sm font-bold {{ $history->is_current ? 'text-blue-900' : 'text-gray-900' }}">
                                                {{ $history->position }}
                                            </h3>
                                            @if($history->division)
                                            <p class="text-xs {{ $history->is_current ? 'text-blue-600' : 'text-gray-500' }} mt-0.5">
                                                Divisi {{ $history->division }}
                                            </p>
                                            @endif
                                            @if($history->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $history->description }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            @if($history->is_current)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white">Saat ini</span>
                                            @endif
                                            <p class="text-xs font-medium {{ $history->is_current ? 'text-blue-700' : 'text-gray-600' }} mt-1">{{ $history->period_label }}</p>
                                            <p class="text-[11px] text-gray-400">{{ $history->duration }}</p>
                                        </div>
                                    </div>
                                    @if($history->role)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $history->is_current ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }}">
                                            {{ ucfirst(str_replace('_', ' ', $history->role)) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-10 w-10 text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                    <p class="text-sm text-gray-400">Belum ada riwayat jabatan.</p>
                    <p class="text-xs text-gray-300 mt-1">Admin dapat menambahkan riwayat melalui halaman edit karyawan.</p>
                </div>
                @endif

                {{-- Schedule Info --}}
                @if($user->schedule)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Jadwal Kerja Saat Ini</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($user->schedule->office)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Kantor / Lokasi</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->schedule->office->name }}</p>
                        </div>
                        @endif
                        @if($user->schedule->shift)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">Shift Kerja</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $user->schedule->shift->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->schedule->shift->start_time }} - {{ $user->schedule->shift->end_time }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Edit Profile Form --}}
            <div id="edit-profile" class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center gap-2 mb-5">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                    Edit Profil
                </h2>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telepon / WA</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+62 812 3456 7890"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat Domisili</label>
                            <textarea id="address" name="address" rows="2" placeholder="Jl. Contoh No. 1, Kel. ABC, Kec. XYZ"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('address', $user->address) }}</textarea>
                            @error('address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date?->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('birth_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Foto Profil</label>
                            <input type="file" id="image" name="image" accept="image/jpg,image/jpeg,image/png"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="mt-1 text-xs text-gray-400">JPG/PNG, maks 2MB</p>
                            @error('image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-gray-100">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── RIGHT COLUMN (1/3) ────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Statistik Kehadiran Bulan Ini --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                    Statistik Kehadiran
                </h3>
                <p class="text-xs text-gray-400 mb-4">Kehadiran bulan {{ now()->translatedFormat('F Y') }}</p>

                {{-- Chart --}}
                <div class="mb-5">
                    <canvas id="attendanceChart" height="180"></canvas>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600">{{ $onTimeCount }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tepat Waktu</p>
                    </div>
                    <div class="text-center p-3 bg-red-50 rounded-xl">
                        <p class="text-2xl font-bold text-red-500">{{ $lateCount }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Terlambat</p>
                    </div>
                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                        <p class="text-2xl font-bold text-blue-600">{{ $monthlyAttendance }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">Hari Hadir</p>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-xl">
                        <p class="text-2xl font-bold text-amber-600">{{ $attendanceRate }}%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Kehadiran</p>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Cuti --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    Ringkasan Cuti
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                        <span class="text-sm text-gray-600">Total Pengajuan</span>
                        <span class="text-sm font-bold text-gray-900">{{ $totalLeaves }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-xl">
                        <span class="text-sm text-gray-600">Disetujui</span>
                        <span class="text-sm font-bold text-green-600">{{ $approvedLeaves }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-xl">
                        <span class="text-sm text-gray-600">Dalam Proses</span>
                        <span class="text-sm font-bold text-amber-600">{{ $pendingLeaves }}</span>
                    </div>
                </div>
            </div>

            {{-- Pengaturan Akun --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-4">
                    <svg class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                    Ubah Kata Sandi
                </h3>
                <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-xs font-medium text-gray-700">Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required placeholder="••••••••"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-medium text-gray-700">Sandi Baru</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-gray-700">Konfirmasi Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-600 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        Simpan Sandi Baru
                    </button>
                </form>

                <div class="mt-5 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center">Terakhir diubah: {{ $user->updated_at->translatedFormat('d M Y, H:i') }} WIB</p>
                </div>
            </div>

        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendanceChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Kehadiran',
                data: @json($chartData),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 28,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: (ctx) => ctx.parsed.y + ' hari'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5,
                        font: { size: 11 },
                        color: '#94a3b8'
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: {
                        font: { size: 10 },
                        color: '#94a3b8'
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
