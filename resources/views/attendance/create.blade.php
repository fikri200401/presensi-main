@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('page-title', 'Tambah Absensi')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('attendance.store') }}" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                <select id="user_id" name="user_id" required
                        class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Pilih Karyawan</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="schedule_id" class="block text-sm font-medium text-gray-700 mb-1">Jadwal</label>
                <select id="schedule_id" name="schedule_id" required
                        class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Pilih Jadwal</option>
                    @foreach($schedules as $schedule)
                        <option value="{{ $schedule->id }}" {{ old('schedule_id') == $schedule->id ? 'selected' : '' }}>
                            {{ $schedule->shift->name ?? 'N/A' }} - {{ $schedule->office->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('schedule_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="schedule_latitude" class="block text-sm font-medium text-gray-700 mb-1">Latitude Jadwal</label>
                    <input type="number" step="any" id="schedule_latitude" name="schedule_latitude" value="{{ old('schedule_latitude') }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('schedule_latitude')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="schedule_longitude" class="block text-sm font-medium text-gray-700 mb-1">Longitude Jadwal</label>
                    <input type="number" step="any" id="schedule_longitude" name="schedule_longitude" value="{{ old('schedule_longitude') }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('schedule_longitude')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="schedule_start_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <input type="time" id="schedule_start_time" name="schedule_start_time" value="{{ old('schedule_start_time') }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('schedule_start_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="schedule_end_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <input type="time" id="schedule_end_time" name="schedule_end_time" value="{{ old('schedule_end_time') }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('schedule_end_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('attendance.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
                    Simpan Absensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
