@extends('layouts.app')

@section('title', 'Edit Pengajuan Cuti')
@section('page-title', 'Edit Pengajuan Cuti')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('leave.update', $leave) }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Karyawan</label>
                @if(auth()->user()->hasRole(['super_admin', 'admin']))
                    <select id="user_id" name="user_id" required
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        <option value="">Pilih Karyawan</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $leave->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="text" value="{{ $leave->user->name }}" readonly
                           class="block w-full rounded-xl border border-gray-200 bg-gray-100 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="user_id" value="{{ $leave->user_id }}">
                @endif
                @error('user_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $leave->start_date) }}"
                        @if(!auth()->user()->hasRole(['super_admin', 'admin']) && $leave->status !== 'pending')
                            readonly class="block w-full rounded-xl border border-gray-200 bg-gray-100 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed"
                        @else
                            required class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                        @endif>
                    @error('start_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $leave->end_date) }}"
                        @if(!auth()->user()->hasRole(['super_admin', 'admin']) && $leave->status !== 'pending')
                            readonly class="block w-full rounded-xl border border-gray-200 bg-gray-100 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed"
                        @else
                            required class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none"
                        @endif>
                    @error('end_date')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan</label>
                <textarea id="reason" name="reason" rows="4"
                    @if(!auth()->user()->hasRole(['super_admin', 'admin']) && $leave->status !== 'pending')
                        readonly class="block w-full rounded-xl border border-gray-200 bg-gray-100 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed resize-none"
                    @else
                        required class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none resize-none"
                    @endif>{{ old('reason', $leave->reason) }}</textarea>
                @error('reason')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                @if(auth()->user()->hasRole(['super_admin', 'admin']))
                    <select id="status" name="status" required
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                        <option value="pending" {{ old('status', $leave->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved_kadiv" {{ old('status', $leave->status) == 'approved_kadiv' ? 'selected' : '' }}>Disetujui Kadiv</option>
                        <option value="approved_hr" {{ old('status', $leave->status) == 'approved_hr' ? 'selected' : '' }}>Disetujui HR</option>
                        <option value="approved" {{ old('status', $leave->status) == 'approved' ? 'selected' : '' }}>Disetujui (Final)</option>
                        <option value="rejected_kadiv" {{ old('status', $leave->status) == 'rejected_kadiv' ? 'selected' : '' }}>Ditolak Kadiv</option>
                        <option value="rejected_hr" {{ old('status', $leave->status) == 'rejected_hr' ? 'selected' : '' }}>Ditolak HR</option>
                        <option value="rejected_direksi" {{ old('status', $leave->status) == 'rejected_direksi' ? 'selected' : '' }}>Ditolak Direksi</option>
                    </select>
                @else
                    <input type="text" value="{{ $leave->status_label }}" readonly
                           class="block w-full rounded-xl border border-gray-200 bg-gray-100 px-3.5 py-2.5 text-sm text-gray-500 cursor-not-allowed">
                    <input type="hidden" name="status" value="{{ $leave->status }}">
                    @if($leave->status !== 'pending')
                        <p class="mt-1 text-xs text-gray-500">Pengajuan ini sedang/telah diproses. Tidak dapat diubah.</p>
                    @else
                        <p class="mt-1 text-xs text-gray-500">Status akan tetap pending hingga ditinjau Kepala Divisi.</p>
                    @endif
                @endif
                @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('leave.index') }}"
                   class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                @if(auth()->user()->hasRole(['super_admin', 'admin']) || !in_array($leave->status, ['approved', 'rejected']))
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
                        Simpan Perubahan
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
