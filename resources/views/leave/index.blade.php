@extends('layouts.app')

@section('title', 'Pengajuan Cuti')
@section('page-title', 'Izin & Cuti')

@section('content')

{{-- ===== Rejection Modal ===== --}}
<div id="rejectionModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 id="rejectionModalTitle" class="text-base font-semibold text-gray-900">Tolak Pengajuan Cuti</h3>
            <button type="button" onclick="closeRejectionModal()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <form id="rejectionForm" method="POST" action="">
            @csrf
            <div class="px-6 py-5">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" required minlength="10"
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none resize-none"
                    placeholder="Masukkan alasan penolakan (minimal 10 karakter)..."></textarea>
                <p class="mt-1.5 text-xs text-gray-400">Minimal 10 karakter. Alasan akan dikirim ke karyawan.</p>
            </div>
            <div class="flex gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100 justify-end">
                <button type="button" onclick="closeRejectionModal()"
                    class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    Tolak Cuti
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== Reason View Modal ===== --}}
<div id="reasonModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                Alasan Penolakan
            </h3>
            <button type="button" onclick="closeReasonModal()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="px-6 py-5">
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p id="reasonText" class="text-sm text-gray-700"></p>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeReasonModal()"
                class="rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600 transition-colors">OK</button>
        </div>
    </div>
</div>

{{-- ===== Approval Trail Modal ===== --}}
<div id="trailModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Riwayat Approval</h3>
            <button type="button" onclick="closeTrailModal()" class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div id="trailContent" class="px-6 py-5 space-y-3"></div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closeTrailModal()"
                class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Tutup</button>
        </div>
    </div>
</div>

{{-- ===== Confirmation Modal ===== --}}
<div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
        <div class="px-6 py-6">
            <div class="flex items-start gap-4">
                <div id="confirmIcon" class="flex-shrink-0"></div>
                <div class="flex-1">
                    <h3 id="confirmTitle" class="text-base font-semibold text-gray-900 mb-1"></h3>
                    <p id="confirmMessage" class="text-sm text-gray-500"></p>
                </div>
            </div>
        </div>
        <div class="flex gap-2 px-6 py-4 bg-gray-50 border-t border-gray-100 justify-end">
            <button type="button" onclick="closeConfirmModal()"
                class="rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">Batal</button>
            <button type="button" id="confirmButton" onclick="confirmAction()"
                class="rounded-xl px-4 py-2 text-sm font-semibold text-white transition-colors">OK</button>
        </div>
    </div>
</div>

{{-- ===== Filter bar ===== --}}
<div class="mb-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
    <form method="GET" action="{{ route('leave.index') }}" class="flex flex-wrap gap-2">
        <div class="relative">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari nama karyawan..."
                   class="block w-52 rounded-xl border border-gray-200 bg-white pl-9 pr-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
        </div>
        <select name="status"
                class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Kadiv</option>
            <option value="approved_kadiv" {{ request('status') == 'approved_kadiv' ? 'selected' : '' }}>Menunggu HR</option>
            <option value="approved_hr" {{ request('status') == 'approved_hr' ? 'selected' : '' }}>Menunggu Direksi</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
            <option value="rejected_kadiv" {{ request('status') == 'rejected_kadiv' ? 'selected' : '' }}>Ditolak Kadiv</option>
            <option value="rejected_hr" {{ request('status') == 'rejected_hr' ? 'selected' : '' }}>Ditolak HR</option>
            <option value="rejected_direksi" {{ request('status') == 'rejected_direksi' ? 'selected' : '' }}>Ditolak Direksi</option>
        </select>
        <button type="submit"
                class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            Cari
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('leave.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">Reset</a>
        @endif
    </form>

    @can('create_leave')
    <a href="{{ route('leave.create') }}"
       class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Ajukan Cuti
    </a>
    @endcan
</div>

{{-- ===== Table ===== --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Karyawan</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Alasan</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Progress</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ substr($leave->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $leave->user->name ?? 'N/A' }}</p>
                                @if($leave->user->division)
                                    <p class="text-xs text-gray-400">{{ $leave->user->division }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-600">
                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} &ndash; {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-3.5 text-sm text-gray-500 max-w-xs truncate">{{ Str::limit($leave->reason, 50) }}</td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $leave->status_color }}">
                            {{ $leave->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        {{-- Approval progress indicator --}}
                        <div class="flex items-center gap-1">
                            {{-- Step 1: Kadiv --}}
                            @if($leave->approved_by_kadiv && !in_array($leave->status, ['rejected_kadiv']))
                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center" title="Kadiv: Disetujui">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                            @elseif($leave->status === 'rejected_kadiv')
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center" title="Kadiv: Ditolak">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center" title="Kadiv: Menunggu">
                                    <span class="text-[10px] font-bold text-amber-600">1</span>
                                </div>
                            @endif
                            <div class="w-3 h-px bg-gray-300"></div>
                            {{-- Step 2: HR --}}
                            @if($leave->approved_by_hr && !in_array($leave->status, ['rejected_hr']))
                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center" title="HR: Disetujui">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                            @elseif($leave->status === 'rejected_hr')
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center" title="HR: Ditolak">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            @elseif(in_array($leave->status, ['approved_kadiv', 'approved_hr', 'approved']))
                                <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center" title="HR: Menunggu">
                                    <span class="text-[10px] font-bold text-blue-600">2</span>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center" title="HR: Belum">
                                    <span class="text-[10px] font-bold text-gray-400">2</span>
                                </div>
                            @endif
                            <div class="w-3 h-px bg-gray-300"></div>
                            {{-- Step 3: Direksi --}}
                            @if($leave->approved_by_direksi && $leave->status === 'approved')
                                <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center" title="Direksi: Disetujui">
                                    <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                </div>
                            @elseif($leave->status === 'rejected_direksi')
                                <div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center" title="Direksi: Ditolak">
                                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </div>
                            @elseif($leave->status === 'approved_hr')
                                <div class="w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center" title="Direksi: Menunggu">
                                    <span class="text-[10px] font-bold text-indigo-600">3</span>
                                </div>
                            @else
                                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center" title="Direksi: Belum">
                                    <span class="text-[10px] font-bold text-gray-400">3</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Approval Trail button --}}
                            @if($leave->approved_by_kadiv || $leave->approved_by_hr || $leave->approved_by_direksi)
                            @php
                                $trailData = [
                                    'kadiv' => $leave->approverKadiv ? [
                                        'name' => $leave->approverKadiv->name,
                                        'at' => $leave->approved_at_kadiv ? $leave->approved_at_kadiv->format('d M Y H:i') : null,
                                        'note' => $leave->note_kadiv,
                                    ] : null,
                                    'hr' => $leave->approverHr ? [
                                        'name' => $leave->approverHr->name,
                                        'at' => $leave->approved_at_hr ? $leave->approved_at_hr->format('d M Y H:i') : null,
                                        'note' => $leave->note_hr,
                                    ] : null,
                                    'direksi' => $leave->approverDireksi ? [
                                        'name' => $leave->approverDireksi->name,
                                        'at' => $leave->approved_at_direksi ? $leave->approved_at_direksi->format('d M Y H:i') : null,
                                        'note' => $leave->note_direksi,
                                    ] : null,
                                ];
                            @endphp
                            <button type="button" onclick="showTrail({{ Js::from($trailData) }})"
                                class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">Riwayat</button>
                            @endif

                            {{-- KEPALA DIVISI: approve/reject if status=pending --}}
                            @if(auth()->user()->hasRole(['kepala_divisi', 'super_admin']) && $leave->status === 'pending')
                                <button type="button"
                                    onclick="showConfirm('approve-kadiv', {{ $leave->id }}, 'Setujui pengajuan cuti {{ addslashes($leave->user->name) }}? (Layer Kadiv)')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-700 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Setuju
                                </button>
                                <button type="button"
                                    onclick="openRejectionModal({{ $leave->id }}, 'kadiv')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tolak
                                </button>
                            @endif

                            {{-- HR / ADMIN: approve/reject if status=approved_kadiv --}}
                            @if(auth()->user()->hasRole(['admin', 'super_admin']) && $leave->status === 'approved_kadiv')
                                <button type="button"
                                    onclick="showConfirm('approve-hr', {{ $leave->id }}, 'Setujui pengajuan cuti {{ addslashes($leave->user->name) }}? (Layer HR)')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-700 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Setuju
                                </button>
                                <button type="button"
                                    onclick="openRejectionModal({{ $leave->id }}, 'hr')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tolak
                                </button>
                            @endif

                            {{-- DIREKSI: approve/reject if status=approved_hr --}}
                            @if(auth()->user()->hasRole(['direksi', 'super_admin']) && $leave->status === 'approved_hr')
                                <button type="button"
                                    onclick="showConfirm('approve-direksi', {{ $leave->id }}, 'Setujui FINAL pengajuan cuti {{ addslashes($leave->user->name) }}? (Layer Direksi)')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-700 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Setuju Final
                                </button>
                                <button type="button"
                                    onclick="openRejectionModal({{ $leave->id }}, 'direksi')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-red-500 hover:text-red-600 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Tolak
                                </button>
                            @endif

                            {{-- SUPER ADMIN: quick approve from any stage --}}
                            @if(auth()->user()->hasRole('super_admin') && !in_array($leave->status, ['approved', 'rejected_kadiv', 'rejected_hr', 'rejected_direksi']))
                                @if($leave->status === 'pending' || $leave->status === 'approved_kadiv' || $leave->status === 'approved_hr')
                                <button type="button"
                                    onclick="showConfirm('approve', {{ $leave->id }}, 'Fast-track approve pengajuan cuti {{ addslashes($leave->user->name) }}?')"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Fast Approve
                                </button>
                                @endif
                            @endif

                            {{-- Show rejection reason --}}
                            @if($leave->isRejected() && $leave->rejection_note)
                                <button type="button" onclick="showRejectionReason('{{ addslashes($leave->rejection_note) }}')"
                                    class="text-xs font-medium text-red-500 hover:text-red-700 hover:underline">Lihat Alasan</button>
                            @endif

                            {{-- Delete (admin/super_admin only) --}}
                            @if(auth()->user()->hasRole(['super_admin', 'admin']))
                                <button type="button"
                                    onclick="showConfirm('delete', {{ $leave->id }}, 'Hapus pengajuan cuti {{ addslashes($leave->user->name) }}?')"
                                    class="text-xs font-medium text-gray-400 hover:text-red-500 hover:underline ml-1">Hapus</button>
                            @endif

                            {{-- Employee: nothing to do when processed --}}
                            @if(auth()->user()->hasRole('employee') && $leave->status === 'pending')
                                <span class="text-xs text-gray-400">Menunggu...</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-gray-400">Tidak ada data pengajuan cuti</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100">
        {{ $leaves->links() }}
    </div>
</div>

@push('scripts')
<script>
    let confirmCallback = null;

    function openRejectionModal(leaveId, layer) {
        const modal = document.getElementById('rejectionModal');
        const form = document.getElementById('rejectionForm');
        const title = document.getElementById('rejectionModalTitle');

        const labels = {
            'kadiv': 'Tolak Pengajuan (Kepala Divisi)',
            'hr': 'Tolak Pengajuan (HR)',
            'direksi': 'Tolak Pengajuan (Direksi)',
        };
        title.textContent = labels[layer] || 'Tolak Pengajuan Cuti';
        form.action = `/leave/${leaveId}/reject-${layer}`;
        modal.classList.remove('hidden');
    }

    function closeRejectionModal() {
        document.getElementById('rejectionForm').reset();
        document.getElementById('rejectionModal').classList.add('hidden');
    }

    function showRejectionReason(reason) {
        document.getElementById('reasonText').textContent = reason;
        document.getElementById('reasonModal').classList.remove('hidden');
    }

    function closeReasonModal() {
        document.getElementById('reasonModal').classList.add('hidden');
    }

    function showTrail(data) {
        const c = document.getElementById('trailContent');
        let html = '';
        const steps = [
            { key: 'kadiv', label: 'Kepala Divisi' },
            { key: 'hr', label: 'HR / Admin' },
            { key: 'direksi', label: 'Direksi' },
        ];
        steps.forEach(s => {
            const d = data[s.key];
            if (d) {
                const isRejected = d.note;
                const color = isRejected ? 'red' : 'green';
                html += `<div class="flex items-start gap-3 p-3 rounded-xl bg-${color}-50 border border-${color}-100">
                    <div class="w-8 h-8 rounded-full bg-${color}-100 flex items-center justify-center flex-shrink-0">
                        ${isRejected
                            ? '<svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>'
                            : '<svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>'
                        }
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">${s.label}</p>
                        <p class="text-xs text-gray-500">${d.name} &mdash; ${d.at || '-'}</p>
                        ${d.note ? `<p class="text-xs text-red-600 mt-1">Alasan: ${d.note}</p>` : ''}
                    </div>
                </div>`;
            } else {
                html += `<div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-gray-400">-</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-400">${s.label}</p>
                        <p class="text-xs text-gray-400">Belum diproses</p>
                    </div>
                </div>`;
            }
        });
        c.innerHTML = html;
        document.getElementById('trailModal').classList.remove('hidden');
    }

    function closeTrailModal() {
        document.getElementById('trailModal').classList.add('hidden');
    }

    function showConfirm(action, leaveId, message) {
        const modal = document.getElementById('confirmModal');
        const btn = document.getElementById('confirmButton');

        document.getElementById('confirmTitle').textContent = action.includes('approve') ? 'Setujui Pengajuan' : 'Hapus Pengajuan';
        document.getElementById('confirmMessage').textContent = message;

        if (action.includes('approve')) {
            document.getElementById('confirmIcon').innerHTML = `<div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center"><svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>`;
            btn.className = 'rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors';
            confirmCallback = () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/leave/${leaveId}/${action}`;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}">`;
                document.body.appendChild(form);
                form.submit();
            };
        } else if (action === 'delete') {
            document.getElementById('confirmIcon').innerHTML = `<div class="h-10 w-10 rounded-xl bg-red-50 flex items-center justify-center"><svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></div>`;
            btn.className = 'rounded-xl bg-red-500 px-4 py-2 text-sm font-semibold text-white hover:bg-red-600 transition-colors';
            confirmCallback = () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/leave/${leaveId}`;
                form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                document.body.appendChild(form);
                form.submit();
            };
        }

        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        document.getElementById('confirmModal').classList.add('hidden');
        confirmCallback = null;
    }

    function confirmAction() {
        if (confirmCallback) confirmCallback();
        closeConfirmModal();
    }

    ['rejectionModal', 'reasonModal', 'confirmModal', 'trailModal'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    });
</script>
@endpush
@endsection
