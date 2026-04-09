@extends('layouts.app')

@section('title', 'Absensi')
@section('page-title', 'Data Absensi')

@section('content')
<!-- Filter & Actions bar -->
<div class="mb-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
    <form method="GET" action="{{ route('attendance.index') }}" class="flex flex-wrap gap-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama karyawan..."
                   class="block w-56 rounded-xl border border-gray-200 bg-white pl-9 pr-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
        </div>
        <input type="date" name="date" value="{{ request('date') }}"
               class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            Cari
        </button>
        @if(request('search') || request('date'))
            <a href="{{ route('attendance.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </form>

    @if(auth()->user()->hasRole(['super_admin', 'admin']))
    <div class="flex gap-2">
        <a href="{{ route('attendance.import.form') }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
            </svg>
            Import
        </a>
        <a href="{{ route('attendance.create') }}"
           class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Tambah
        </a>
    </div>
    @endif
</div>

<!-- Table card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Shift</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Jadwal</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Jam Aktual</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ substr($attendance->user->name ?? 'U', 0, 1) }}
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ $attendance->user->name ?? 'N/A' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-600">
                        {{ $attendance->user->schedule->shift->name ?? '-' }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-500">
                        {{ $attendance->schedule_start_time ?? '-' }} &ndash; {{ $attendance->schedule_end_time ?? '-' }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-600 font-medium">
                        {{ $attendance->start_time ? \Carbon\Carbon::parse($attendance->start_time)->format('H:i') : '-' }}
                        &ndash;
                        {{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-500">
                        {{ $attendance->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        @if($attendance->end_time)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700">Selesai</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">Hadir</span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            @can('update_attendance')
                                @if(auth()->user()->hasRole(['super_admin', 'admin']) || $attendance->user_id === auth()->id())
                                    <a href="{{ route('attendance.edit', $attendance) }}"
                                       class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">Edit</a>
                                @endif
                            @endcan

                            @can('delete_attendance')
                                @if(auth()->user()->hasRole(['super_admin', 'admin']))
                                    <form method="POST" action="{{ route('attendance.destroy', $attendance) }}" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-500 hover:text-red-700 hover:underline">Hapus</button>
                                    </form>
                                @endif
                            @endcan

                            @if(!auth()->user()->can('update_attendance') && !auth()->user()->can('delete_attendance'))
                                <span class="text-gray-300 text-xs">&ndash;</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-gray-400">Tidak ada data absensi ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $attendances->links() }}
    </div>
</div>
@endsection
