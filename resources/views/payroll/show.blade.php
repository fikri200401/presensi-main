@extends('layouts.app')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Slip Gaji</h1>
            <p class="mt-2 text-sm text-gray-700">
                Detail slip gaji untuk periode {{ $payroll->periode_name }}
            </p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none flex gap-2">
            <a href="{{ route('payroll.exportPdf', $payroll->id) }}" target="_blank" class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Slip Gaji
            </a>
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Slip Gaji Card -->
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <!-- Header -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 px-6 py-8">
            <div class="flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-white">SLIP GAJI</h2>
                    <p class="text-indigo-100 mt-1">Periode: {{ $payroll->periode_name }}</p>
                </div>
                <div class="text-right">
                    <div class="inline-flex rounded-full px-3 py-1 text-sm font-semibold
                        {{ $payroll->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $payroll->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $payroll->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $payroll->status === 'paid' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $payroll->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($payroll->status) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Employee Info -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Nama Karyawan</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $payroll->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="text-lg text-gray-900">{{ $payroll->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Kehadiran</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 rounded-lg p-3">
                    <p class="text-xs text-blue-600 font-medium">Total Hari Kerja</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $payroll->total_hari_kerja }}</p>
                    <p class="text-xs text-blue-600">hari</p>
                </div>
                <div class="bg-green-50 rounded-lg p-3">
                    <p class="text-xs text-green-600 font-medium">Hari Hadir</p>
                    <p class="text-2xl font-bold text-green-900">{{ $payroll->total_hari_hadir }}</p>
                    <p class="text-xs text-green-600">hari</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-3">
                    <p class="text-xs text-purple-600 font-medium">Total Jam Hadir</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $payroll->total_jam_hadir }}</p>
                    <p class="text-xs text-purple-600">jam</p>
                </div>
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-xs text-red-600 font-medium">Keterlambatan</p>
                    <p class="text-2xl font-bold text-red-900">{{ $payroll->total_terlambat }}</p>
                    <p class="text-xs text-red-600">kali</p>
                </div>
            </div>
        </div>

        <!-- Salary Breakdown -->
        <div class="px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Gaji</h3>
            
            <!-- Gaji Pokok -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between py-2">
                    <span class="text-gray-700">Gaji Pokok</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</span>
                </div>
                <div class="text-xs text-gray-500 pl-4">
                    <div class="flex justify-between">
                        <span>• Gaji per hari: Rp {{ number_format($payroll->gaji_per_hari, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>• Gaji per jam: Rp {{ number_format($payroll->gaji_per_jam, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
                <h4 class="font-semibold text-gray-900 mb-3">Tunjangan</h4>
                <div class="space-y-2 text-sm">
                    @if($payroll->tunjangan_transport > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>Tunjangan Transport</span>
                        <span>Rp {{ number_format($payroll->tunjangan_transport, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->tunjangan_makan > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>Tunjangan Makan</span>
                        <span>Rp {{ number_format($payroll->tunjangan_makan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->tunjangan_lainnya > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>Tunjangan Lainnya</span>
                        <span>Rp {{ number_format($payroll->tunjangan_lainnya, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold text-green-700 pt-2 border-t">
                        <span>Total Tunjangan</span>
                        <span>Rp {{ number_format($payroll->total_tunjangan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4 mb-4">
                <h4 class="font-semibold text-gray-900 mb-3">Potongan</h4>
                <div class="space-y-2 text-sm">
                    @if($payroll->potongan_bpjs_kesehatan > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>BPJS Kesehatan</span>
                        <span class="text-red-600">- Rp {{ number_format($payroll->potongan_bpjs_kesehatan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->potongan_bpjs_ketenagakerjaan > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>BPJS Ketenagakerjaan</span>
                        <span class="text-red-600">- Rp {{ number_format($payroll->potongan_bpjs_ketenagakerjaan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->potongan_pph21 > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>PPH21</span>
                        <span class="text-red-600">- Rp {{ number_format($payroll->potongan_pph21, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->potongan_keterlambatan > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>Potongan Keterlambatan</span>
                        <span class="text-red-600">- Rp {{ number_format($payroll->potongan_keterlambatan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($payroll->potongan_lainnya > 0)
                    <div class="flex justify-between text-gray-700">
                        <span>Potongan Lainnya</span>
                        <span class="text-red-600">- Rp {{ number_format($payroll->potongan_lainnya, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-semibold text-red-700 pt-2 border-t">
                        <span>Total Potongan</span>
                        <span>- Rp {{ number_format($payroll->total_potongan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="border-t-2 border-gray-300 pt-4 space-y-3">
                <div class="flex justify-between text-lg">
                    <span class="text-gray-700">Gaji Kotor</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($payroll->gaji_kotor, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xl bg-indigo-50 p-4 rounded-lg">
                    <span class="font-bold text-indigo-900">Gaji Bersih (Take Home Pay)</span>
                    <span class="font-bold text-indigo-900">Rp {{ number_format($payroll->gaji_bersih, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Approval Info -->
        @if($payroll->status === 'approved' || $payroll->status === 'paid')
        <div class="px-6 py-4 bg-green-50 border-t border-green-200">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-green-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800">Payroll Approved</h3>
                    <div class="mt-2 text-sm text-green-700">
                        <p>Disetujui oleh: <strong>{{ $payroll->approver->name ?? 'System' }}</strong></p>
                        <p>Tanggal: {{ $payroll->approved_at ? $payroll->approved_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($payroll->status === 'rejected' && $payroll->catatan)
        <div class="px-6 py-4 bg-red-50 border-t border-red-200">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-red-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Payroll Rejected</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p><strong>Alasan:</strong> {{ $payroll->catatan }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Actions (Admin Only) -->
        @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('panel_user'))
        @if($payroll->status === 'draft' || $payroll->status === 'pending')
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button onclick="approvePayroll()" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Approve
            </button>
            <button onclick="openRejectModal()" class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Reject
            </button>
        </div>
        @endif

        @if($payroll->status === 'approved')
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
            <button onclick="markAsPaid()" class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tandai Sudah Dibayar
            </button>
        </div>
        @endif
        @endif

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 text-center text-xs text-gray-500">
            <p>Slip gaji ini dibuat secara otomatis oleh sistem pada {{ $payroll->created_at->format('d M Y H:i') }}</p>
            <p class="mt-1">Untuk pertanyaan lebih lanjut, hubungi bagian HR</p>
        </div>
    </div>
</div>

<!-- Reject Modal (sama seperti di index) -->
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 0.5rem; max-width: 32rem; width: 100%; max-height: 85vh; display: flex; flex-direction: column; margin: 1rem;">
        <div style="position: sticky; top: 0; background: white; border-bottom: 1px solid #e5e7eb; padding: 1.5rem; border-radius: 0.5rem 0.5rem 0 0; z-index: 10;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Reject Payroll</h3>
                <button onclick="closeRejectModal()" style="color: #6b7280; background: none; border: none; cursor: pointer; font-size: 1.5rem; line-height: 1;">&times;</button>
            </div>
        </div>
        
        <div style="flex: 1; overflow-y: auto; padding: 1.5rem;">
            <form id="rejectForm" method="POST" action="{{ route('payroll.reject', $payroll->id) }}">
                @csrf
                <div>
                    <label for="rejection_reason" style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">
                        Alasan Penolakan <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea 
                        name="rejection_reason" 
                        id="rejection_reason" 
                        rows="4" 
                        required 
                        minlength="10"
                        placeholder="Masukkan alasan penolakan payroll (minimal 10 karakter)"
                        style="width: 100%; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 0.5rem; font-size: 0.875rem; resize: vertical;"
                    ></textarea>
                    <p style="margin-top: 0.5rem; font-size: 0.75rem; color: #6b7280;">Minimal 10 karakter</p>
                </div>
            </form>
        </div>
        
        <div style="position: sticky; bottom: 0; background: #f9fafb; border-top: 1px solid #e5e7eb; padding: 1rem 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; border-radius: 0 0 0.5rem 0.5rem;">
            <button type="button" onclick="closeRejectModal()" style="background: white; border: 1px solid #d1d5db; color: #374151; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; cursor: pointer;">
                Batal
            </button>
            <button type="button" onclick="submitReject()" style="background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer;">
                Reject Payroll
            </button>
        </div>
    </div>
</div>

<script>
function approvePayroll() {
    Swal.fire({
        title: 'Approve Payroll?',
        text: 'Apakah Anda yakin ingin menyetujui payroll ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Approve',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('payroll.approve', $payroll->id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function openRejectModal() {
    document.getElementById('rejection_reason').value = '';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

function submitReject() {
    const reason = document.getElementById('rejection_reason').value;
    
    if (reason.length < 10) {
        Swal.fire({
            icon: 'error',
            title: 'Validasi Error',
            text: 'Alasan penolakan minimal 10 karakter',
            confirmButtonColor: '#ef4444'
        });
        return;
    }
    
    document.getElementById('rejectForm').submit();
}

function markAsPaid() {
    Swal.fire({
        title: 'Tandai Sudah Dibayar?',
        text: 'Tandai payroll ini sebagai sudah dibayar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Sudah Dibayar',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('payroll.markAsPaid', $payroll->id) }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
