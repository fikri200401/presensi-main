@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

<!-- Quick Check-in Button for Employee -->
@if(!auth()->user()->hasRole(['super_admin', 'admin']))
<div class="mb-6">
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-lg rounded-lg border-4 border-indigo-300">
        <div class="p-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-2xl font-bold text-white mb-2">📍 Presensi Hari Ini</h3>
                    <p class="text-indigo-100 text-sm">
                        @if($todayAttendance)
                            @if($todayAttendance->end_time)
                                ✅ Check-in: {{ $todayAttendance->start_time }} | Check-out: {{ $todayAttendance->end_time }}
                            @else
                                ⏰ Check-in: {{ $todayAttendance->start_time }} | Belum Check-out
                            @endif
                        @else
                            ⚠️ Anda belum melakukan check-in hari ini
                        @endif
                    </p>
                    <p class="text-indigo-200 text-xs mt-1">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
                <div>
                    <a href="{{ route('presensi') }}" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 font-bold text-lg rounded-lg shadow-lg hover:bg-indigo-50 transition transform hover:scale-105">
                        <svg class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                        @if(!$todayAttendance)
                            Check In Sekarang
                        @elseif(!$todayAttendance->end_time)
                            Check Out Sekarang
                        @else
                            Lihat Presensi
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Stats -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-md bg-indigo-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-md bg-purple-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Offices</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['total_offices'] }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-md bg-green-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Today's Attendance</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['today_attendance'] }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
        <div class="p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-md bg-yellow-500 flex items-center justify-center">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Pending Leaves</dt>
                        <dd class="flex items-baseline">
                            <div class="text-2xl font-semibold text-gray-900">{{ $stats['pending_leaves'] }}</div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Attendance -->
<div class="mt-8">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Attendance</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentAttendances as $attendance)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                        {{ substr($attendance->user->name ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $attendance->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $attendance->user->schedule->shift->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $attendance->created_at->format('d M Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                            No attendance records found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
