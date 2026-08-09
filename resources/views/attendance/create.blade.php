@extends('layouts.app')

@section('title', 'Tambah Absensi')
@section('page-title', 'Tambah Absensi')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('attendance.store') }}" class="p-6 space-y-5">
            @csrf

            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                Pilih karyawan lalu isi waktu kehadiran aktual. Shift, kantor, dan koordinat jadwal akan diambil otomatis dari jadwal karyawan. Entri manual tidak memerlukan koordinat GPS aktual.
            </div>

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                <select id="user_id" name="user_id" required
                        class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Pilih Karyawan</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                                data-shift="{{ $user->schedule->shift->name }}"
                                data-office="{{ $user->schedule->office->name }}"
                                data-start="{{ substr($user->schedule->shift->start_time, 0, 5) }}"
                                data-end="{{ substr($user->schedule->shift->end_time, 0, 5) }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div id="schedule-summary" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3">
                <p id="schedule-empty" class="text-sm text-gray-500">Pilih karyawan untuk melihat jadwal kerjanya.</p>
                <div id="schedule-details" class="hidden grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Shift</p>
                        <p id="schedule-shift" class="mt-1 text-sm font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Lokasi</p>
                        <p id="schedule-office" class="mt-1 text-sm font-semibold text-gray-800"></p>
                    </div>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Jam Jadwal</p>
                        <p id="schedule-time" class="mt-1 text-sm font-semibold text-gray-800"></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Absensi</label>
                    <input type="date" id="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Masuk Aktual</label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('start_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">Jam Pulang Aktual <span class="font-normal text-gray-400">(opsional)</span></label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}"
                           class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    @error('end_time')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const userSelect = document.getElementById('user_id');
    const emptyState = document.getElementById('schedule-empty');
    const details = document.getElementById('schedule-details');

    const refreshSchedule = () => {
        const option = userSelect.options[userSelect.selectedIndex];
        const hasSchedule = option?.value && option.dataset.shift;

        emptyState.classList.toggle('hidden', Boolean(hasSchedule));
        details.classList.toggle('hidden', !hasSchedule);

        if (hasSchedule) {
            document.getElementById('schedule-shift').textContent = option.dataset.shift;
            document.getElementById('schedule-office').textContent = option.dataset.office;
            document.getElementById('schedule-time').textContent = `${option.dataset.start} - ${option.dataset.end}`;
        }
    };

    userSelect.addEventListener('change', refreshSchedule);
    refreshSchedule();
});
</script>
@endpush
