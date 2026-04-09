<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>HRIS Portal - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Leaflet CSS for GPS Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin="" />

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 99px; }
    </style>

    @livewireStyles
</head>
<body class="h-full" x-data="{ sidebarOpen: false, searchOpen: false }" @keydown.slash.window="if(!['INPUT','TEXTAREA'].includes($event.target.tagName)) searchOpen = true" @keydown.escape.window="searchOpen = false">

    {{-- ── GLOBAL SEARCH OVERLAY ────────────────────────────────────────── --}}
    <div x-show="searchOpen"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="searchOpen = false"
         class="fixed inset-0 z-[999] bg-gray-900/50 backdrop-blur-sm flex items-start justify-center pt-28 px-4 lg:pl-[calc(240px+3rem)] lg:pr-10"
         style="display:none;"
         x-data="globalSearch()">

        <div @click.stop
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-xl bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Input --}}
            <div class="flex items-center gap-3 px-4 py-3.5 border-b border-gray-100">
                <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text"
                       x-model="query"
                       x-ref="searchInput"
                       x-init="$watch('searchOpen', v => { if(v) { $nextTick(() => $refs.searchInput.focus()); query = ''; } })"
                       @keydown.escape="searchOpen = false"
                       @keydown.arrow-down.prevent="moveDown()"
                       @keydown.arrow-up.prevent="moveUp()"
                       @keydown.enter.prevent="goToSelected()"
                       placeholder="Cari menu, halaman... (tekan / untuk buka)"
                       class="flex-1 text-sm text-gray-800 placeholder-gray-400 bg-transparent outline-none">
                <kbd class="hidden sm:inline-flex items-center rounded border border-gray-200 px-1.5 py-0.5 text-xs text-gray-400 font-mono">Esc</kbd>
            </div>

            {{-- Results --}}
            <div class="max-h-80 overflow-y-auto py-2">
                <template x-if="filtered().length === 0">
                    <div class="px-4 py-8 text-center text-sm text-gray-400">
                        Tidak ada hasil untuk "<span x-text="query" class="font-medium text-gray-600"></span>"
                    </div>
                </template>
                <template x-for="(item, index) in filtered()" :key="item.url">
                    <a :href="item.url"
                       @mouseenter="selected = index"
                       @click="searchOpen = false"
                       :class="selected === index ? 'bg-blue-50' : 'hover:bg-gray-50'"
                       class="flex items-center gap-3 px-4 py-2.5 transition-colors cursor-pointer">
                        <div :class="selected === index ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-500'"
                             class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors">
                            <span x-html="item.icon" class="w-4 h-4"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p :class="selected === index ? 'text-blue-700' : 'text-gray-800'"
                               class="text-sm font-medium" x-text="item.label"></p>
                            <p class="text-xs text-gray-400 truncate" x-text="item.desc"></p>
                        </div>
                        <svg x-show="selected === index" class="h-4 w-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </template>
            </div>

            {{-- Footer --}}
            <div class="border-t border-gray-100 px-4 py-2 flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1"><kbd class="border border-gray-200 rounded px-1 font-mono">↑↓</kbd> navigasi</span>
                <span class="flex items-center gap-1"><kbd class="border border-gray-200 rounded px-1 font-mono">↵</kbd> buka</span>
                <span class="flex items-center gap-1"><kbd class="border border-gray-200 rounded px-1 font-mono">Esc</kbd> tutup</span>
            </div>
        </div>
    </div>
    {{-- ── END SEARCH OVERLAY ──────────────────────────────────────────────── --}}
    <div class="min-h-full flex flex-col">
        <!-- Mobile sidebar overlay -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="relative z-50 lg:hidden"
             role="dialog"
             aria-modal="true"
             style="display: none;">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
            <div class="fixed inset-0 flex">
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-in-out duration-300 transform"
                     x-transition:enter-start="-translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transition ease-in-out duration-300 transform"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="-translate-x-full"
                     class="relative mr-16 flex w-full max-w-[240px] flex-1">
                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                        <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    @include('layouts.partials.sidebar')
                </div>
            </div>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-[240px] lg:flex-col">
            @include('layouts.partials.sidebar')
        </div>

        <div class="lg:pl-[240px] flex flex-col flex-1 min-h-screen">
            <!-- Top navbar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-600 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <!-- Left: Brand text + tabs -->
                    <div class="flex items-center gap-6">
                        <h1 class="text-base font-bold text-gray-900 hidden lg:block">
                            @if(auth()->user()->hasRole(['super_admin', 'admin']))
                                HRIS Portal Admin
                            @else
                                HRIS Portal
                            @endif
                        </h1>
                        <h1 class="text-base font-semibold text-gray-800 lg:hidden">@yield('page-title', 'Dashboard')</h1>
                        @if(auth()->user()->hasRole(['super_admin', 'admin']))
                        <nav class="hidden lg:flex items-center gap-1">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }} px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Overview</a>
                            <a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.*') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }} px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Analytics</a>
                            <a href="#" class="text-gray-500 hover:text-gray-700 px-3 py-1.5 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">Activity Logs</a>
                        </nav>
                        @endif
                    </div>

                    <div class="flex-1"></div>

                    <!-- Right side -->
                    <div class="flex items-center gap-x-3">
                        <!-- Search -->
                        <button type="button" @click="searchOpen = true" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Cari menu (/)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </button>

                        <!-- Notifications -->
                        <div class="relative" x-data="notificationBell()" x-init="loadNotifications()">
                            <button type="button"
                                    @click="open = !open; if(open) loadNotifications()"
                                    class="relative p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                                {{-- Badge unread --}}
                                <span x-show="unreadCount > 0"
                                      x-text="unreadCount > 9 ? '9+' : unreadCount"
                                      class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-0.5 leading-none"></span>
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 bg-white rounded-2xl shadow-xl ring-1 ring-gray-900/5 z-50 overflow-hidden"
                                 style="width: 420px; max-width: calc(100vw - 1rem);"
                                 style="display:none;">

                                {{-- Header --}}
                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-semibold text-gray-900">Notifikasi</h3>
                                        <span x-show="unreadCount > 0"
                                              x-text="unreadCount"
                                              class="bg-red-100 text-red-600 text-xs font-bold px-1.5 py-0.5 rounded-full"></span>
                                    </div>
                                    <button x-show="unreadCount > 0"
                                            @click="markAllRead()"
                                            class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                        Tandai semua dibaca
                                    </button>
                                </div>

                                {{-- List --}}
                                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                                    {{-- Loading state --}}
                                    <template x-if="loading">
                                        <div class="flex items-center justify-center py-8 gap-2 text-gray-400 text-sm">
                                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                                            Memuat...
                                        </div>
                                    </template>

                                    {{-- Empty state --}}
                                    <template x-if="!loading && notifications.length === 0">
                                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                            <svg class="h-10 w-10 mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                            </svg>
                                            <p class="text-sm">Tidak ada notifikasi</p>
                                        </div>
                                    </template>

                                    {{-- Notification items --}}
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <a :href="notif.url || '#'"
                                           @click="markRead(notif)"
                                           :class="notif.is_read ? 'bg-white hover:bg-gray-50' : 'bg-blue-50/60 hover:bg-blue-50'"
                                           class="flex items-start gap-3 px-4 py-3 transition-colors cursor-pointer">

                                            {{-- Icon --}}
                                            <div :class="{
                                                    'bg-blue-100 text-blue-600':   notif.color === 'blue',
                                                    'bg-green-100 text-green-600': notif.color === 'green',
                                                    'bg-red-100 text-red-600':     notif.color === 'red',
                                                    'bg-yellow-100 text-yellow-600': notif.color === 'yellow',
                                                    'bg-gray-100 text-gray-500':   notif.color === 'gray',
                                                 }"
                                                 class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                {{-- leave_request --}}
                                                <template x-if="notif.icon === 'document'">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                                </template>
                                                {{-- leave_approved --}}
                                                <template x-if="notif.icon === 'check'">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                </template>
                                                {{-- leave_rejected --}}
                                                <template x-if="notif.icon === 'x'">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </template>
                                                {{-- attendance_alert --}}
                                                <template x-if="notif.icon === 'exclamation'">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                                </template>
                                                {{-- info --}}
                                                <template x-if="notif.icon === 'info'">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                                                </template>
                                            </div>

                                            {{-- Content --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-1">
                                                    <p :class="notif.is_read ? 'text-gray-700' : 'text-gray-900 font-semibold'"
                                                       class="text-xs leading-snug" x-text="notif.title"></p>
                                                    <span x-show="!notif.is_read" class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-1"></span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-0.5 leading-snug line-clamp-3" x-text="notif.message"></p>
                                                <p class="text-[10px] text-gray-400 mt-1" x-text="notif.time"></p>
                                            </div>
                                        </a>
                                    </template>
                                </div>

                                {{-- Footer --}}
                                <div class="border-t border-gray-100 px-4 py-2.5">
                                    <a href="{{ route('attendance.index') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium transition-colors">
                                        Lihat semua aktivitas →
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200" aria-hidden="true"></div>

                        <!-- Profile dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex items-center gap-2.5 hover:bg-gray-50 rounded-xl px-2 py-1.5 transition-colors">
                                <div class="hidden lg:block text-right">
                                    <p class="text-sm font-semibold text-gray-900 leading-none">{{ auth()->user()->name ?? 'User' }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        @if(auth()->user()->hasRole('super_admin')) Super Admin
                                        @elseif(auth()->user()->hasRole('admin')) Admin
                                        @else Karyawan @endif
                                    </p>
                                </div>
                                <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </div>
                            </button>

                            <div x-show="open"
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-xl bg-white py-2 shadow-lg ring-1 ring-gray-900/5"
                                 style="display: none;">
                                <a href="#" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    Profil Saya
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="flex-1 py-6">
                <div class="px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 flex items-center gap-3 rounded-xl bg-green-50 border border-green-100 p-4">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 flex items-center gap-3 rounded-xl bg-red-50 border border-red-100 p-4">
                            <svg class="h-5 w-5 text-red-500 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200 bg-white py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-400">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                            Logout
                        </button>
                    </form>
                    <p>&copy; {{ date('Y') }} HRIS Portal - Internal Use Only</p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-gray-600 transition-colors">IT Support</a>
                        <a href="#" class="hover:text-gray-600 transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-gray-600 transition-colors">Security Guidelines</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')

    <script>
    // ── Notification Bell ──────────────────────────────────────────────────
    function notificationBell() {
        return {
            open: false,
            loading: false,
            notifications: [],
            unreadCount: 0,

            async loadNotifications() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route("notifications.index") }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.unreadCount   = data.unread_count;
                } catch(e) {
                    console.error('Notifikasi gagal dimuat', e);
                } finally {
                    this.loading = false;
                }
            },

            async markRead(notif) {
                if (notif.is_read) return;
                notif.is_read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
                await fetch(`/notifications/${notif.id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
            },

            async markAllRead() {
                this.notifications.forEach(n => n.is_read = true);
                this.unreadCount = 0;
                await fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
            },
        };
    }

    // ── Global Search ──────────────────────────────────────────────────────
    function globalSearch() {
        return {
            query: '',
            selected: 0,
            menus: [
                @if(auth()->check() && auth()->user()->hasRole(['super_admin', 'admin']))
                { label: 'Dashboard',        desc: 'Ringkasan & statistik kehadiran',    url: '{{ route("dashboard") }}',         icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zm9.75-9.75A2.25 2.25 0 0115.75 3.75H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>' },
                { label: 'Data Karyawan',    desc: 'Kelola data karyawan & pengguna',    url: '{{ route("user.index") }}',         icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>' },
                { label: 'Tambah Karyawan', desc: 'Buat akun karyawan baru',            url: '{{ route("user.create") }}',        icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21a12.318 12.318 0 01-6.374-1.766z" /></svg>' },
                { label: 'Absensi',          desc: 'Rekap & histori absensi karyawan',   url: '{{ route("attendance.index") }}',   icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>' },
                { label: 'Izin & Cuti',      desc: 'Pengajuan & persetujuan izin cuti',  url: '{{ route("leave.index") }}',        icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>' },
                { label: 'Penggajian',       desc: 'Slip gaji & laporan payroll',        url: '{{ route("payroll.index") }}',      icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" /></svg>' },
                { label: 'Master Kantor',    desc: 'Pengaturan data kantor & lokasi',    url: '{{ route("office.index") }}',       icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>' },
                { label: 'Master Shift',     desc: 'Kelola shift & jam kerja',           url: '{{ route("shift.index") }}',        icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>' },
                { label: 'Master Jadwal',    desc: 'Atur jadwal kerja karyawan',         url: '{{ route("schedule.index") }}',     icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5" /></svg>' },
                { label: 'Master Role',      desc: 'Kelola hak akses pengguna',          url: '{{ route("role.index") }}',         icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>' },
                @endif
                @if(auth()->check() && !auth()->user()->hasRole(['super_admin', 'admin']))
                { label: 'Dashboard',        desc: 'Lihat ringkasan kehadiran Anda',     url: '{{ route("dashboard") }}',         icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z" /></svg>' },
                { label: 'Presensi',         desc: 'Lakukan check-in / check-out',       url: '{{ route("presensi") }}',          icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>' },
                { label: 'Izin & Cuti',      desc: 'Ajukan atau cek status izin Anda',   url: '{{ route("leave.index") }}',       icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12" /></svg>' },
                { label: 'Penggajian',       desc: 'Lihat slip gaji Anda',               url: '{{ route("payroll.index") }}',     icon: '<svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75" /></svg>' },
                @endif
            ],
            filtered() {
                if (!this.query.trim()) return this.menus;
                const q = this.query.toLowerCase();
                return this.menus.filter(m =>
                    m.label.toLowerCase().includes(q) || m.desc.toLowerCase().includes(q)
                );
            },
            moveDown() {
                if (this.selected < this.filtered().length - 1) this.selected++;
            },
            moveUp() {
                if (this.selected > 0) this.selected--;
            },
            goToSelected() {
                const item = this.filtered()[this.selected];
                if (item) { window.location.href = item.url; this.$dispatch('close-search'); }
            },
        };
    }
    </script>

    @livewireScripts
</body>
</html>
