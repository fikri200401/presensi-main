@extends('layouts.app')

@section('title', 'Penggajian')
@section('page-title', 'Manajemen Penggajian')

@section('content')

{{-- Filter bar --}}
<div class="mb-5 bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
    <form method="GET" action="{{ route('payroll.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end sm:justify-between">
        <div class="flex flex-col sm:flex-row gap-3">
            <div>
                <label for="periode" class="block text-xs font-medium text-gray-500 mb-1">Periode</label>
                <input type="month" name="periode" id="periode" value="{{ request('periode') }}"
                       class="block w-44 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
            </div>
            @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'direksi']) || auth()->user()->can('view_any_payroll'))
            <div>
                <label for="status" class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" id="status" class="block w-40 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none">
                    <option value="">Semua</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                    <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                    <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                </select>
            </div>
            @endif
            <div class="flex items-end">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
                    Filter
                </button>
            </div>
        </div>
        @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('salary-settings.edit') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Pengaturan Gaji
            </a>
            <a href="{{ route('employee-salary.index') }}"
               class="inline-flex items-center gap-1.5 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                Gaji Karyawan
            </a>
            <a href="{{ route('payroll.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 transition-colors shadow-sm shadow-blue-100">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Generate Payroll
            </a>
        </div>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead>
                <tr class="bg-gray-50">
                    @can('view_any_user')
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Karyawan</th>
                    @endcan
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Periode</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Hari Hadir</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Gaji Kotor</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Gaji Bersih</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($payrolls as $payroll)
                <tr class="hover:bg-gray-50 transition-colors">
                    @can('view_any_user')
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ substr($payroll->user->name ?? 'U', 0, 1) }}
                            </div>
                            <p class="text-sm font-semibold text-gray-900">{{ $payroll->user->name }}</p>
                        </div>
                    </td>
                    @endcan
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-500">{{ $payroll->periode_name }}</td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-500">{{ $payroll->total_hari_hadir }} / {{ $payroll->total_hari_kerja }} hari</td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $payroll->status === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $payroll->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $payroll->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $payroll->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('payroll.show', $payroll->id) }}" class="text-blue-600 hover:text-blue-700" title="Lihat Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            @if($payroll->status === 'approved')
                            <a href="{{ route('payroll.exportPdf', $payroll->id) }}" target="_blank" class="text-red-500 hover:text-red-700" title="Cetak Slip Gaji">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->can('view_any_user') ? 7 : 6 }}" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        <p class="text-sm text-gray-400">Belum ada data payroll.</p>
                        @can('view_any_user')
                        <a href="{{ route('payroll.create') }}" class="mt-1 inline-block text-sm text-blue-600 hover:underline">Generate payroll sekarang</a>
                        @endcan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payrolls->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $payrolls->links() }}
    </div>
    @endif
</div>

@endsection
