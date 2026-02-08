@extends('layouts.app')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Preview Import Absensi</h1>
            <p class="mt-1 text-sm text-gray-600">Review dan edit data sebelum menyimpan ke database</p>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
                @if(session('import_success'))
                    <p class="mt-2 text-sm">✅ Berhasil: {{ session('import_success') }} data</p>
                    <p class="text-sm">❌ Gagal: {{ session('import_failed') }} data</p>
                @endif
            </div>
        @endif

        <!-- Error Details -->
        @if(session('import_errors'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-medium text-red-800">Detail Error Import</h3>
                        <div class="mt-2 text-sm text-red-700 max-h-60 overflow-y-auto">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('import_draft_id'))
            <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex items-center justify-between">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Draft Tersimpan!</strong> Data yang gagal telah disimpan sebagai draft. 
                                Anda bisa perbaiki dan import ulang nanti.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('attendance.import.drafts') }}" class="text-sm text-blue-600 hover:text-blue-800 underline whitespace-nowrap ml-4">
                        Lihat Draft →
                    </a>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Data</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                        <input type="date" id="filterDateFrom" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                        <input type="date" id="filterDateTo" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="button" onclick="applyDateFilter()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <svg class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        <button type="button" onclick="resetFilter()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Reset</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-200 bg-blue-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">
                            <svg class="inline-block w-5 h-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Total: <span id="totalCount">{{ count($previewData) }}</span> data | Ditampilkan: <span id="visibleCount">{{ count($previewData) }}</span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Centang data yang ingin diimport, edit jika diperlukan, lalu klik "Konfirmasi Import"
                        </p>
                    </div>
                    <button type="button" onclick="addNewRow()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Data
                    </button>
                </div>
            </div>

            <form action="{{ route('attendance.import.confirm') }}" method="POST" id="importForm">
                @csrf
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('no')">
                                    <div class="flex items-center">
                                        No
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('karyawan')">
                                    <div class="flex items-center">
                                        Karyawan
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('tanggal')">
                                    <div class="flex items-center">
                                        Tanggal
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('checkin')">
                                    <div class="flex items-center">
                                        Check In
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100" onclick="sortTable('checkout')">
                                    <div class="flex items-center">
                                        Check Out
                                        <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                        </svg>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Asli</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tableBody">
                            @foreach($previewData as $index => $data)
                            <tr class="hover:bg-gray-50 data-row {{ isset($data['error']) ? 'bg-red-50' : '' }}" 
                                data-date="{{ $data['date'] ?? '' }}"
                                data-karyawan="{{ $data['user_name'] ?? '' }}"
                                data-checkin="{{ $data['check_in'] ?? '' }}"
                                data-checkout="{{ $data['check_out'] ?? '' }}"
                                data-no="{{ $index + 1 }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           name="attendance[{{ $data['row_index'] ?? $index }}][import]" 
                                           value="1" 
                                           {{ isset($data['error']) ? '' : 'checked' }}
                                           class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500">{{ $index + 1 }}</span>
                                        @if(isset($data['error']))
                                            <svg class="w-5 h-5 ml-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="hidden" name="attendance[{{ $data['row_index'] ?? $index }}][user_id]" value="{{ $data['user_id'] ?? '' }}">
                                    <select name="attendance[{{ $data['row_index'] ?? $index }}][user_id_editable]" 
                                            class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ isset($data['error']) ? 'border-red-300' : '' }}"
                                            onchange="this.previousElementSibling.value = this.value">
                                        <option value="">-- Pilih Karyawan --</option>
                                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}" {{ ($data['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(isset($data['error']))
                                        <p class="mt-1 text-xs text-red-600">{{ $data['error'] }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="date" 
                                           name="attendance[{{ $data['row_index'] ?? $index }}][date]" 
                                           value="{{ $data['date'] ?? '' }}"
                                           class="date-input text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ isset($data['error']) && str_contains($data['error'], 'Tanggal') ? 'border-red-300' : '' }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time" 
                                           name="attendance[{{ $data['row_index'] ?? $index }}][check_in]" 
                                           value="{{ $data['check_in'] ?? '' }}"
                                           class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 {{ isset($data['error']) && str_contains($data['error'], 'check in') ? 'border-red-300' : '' }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time" 
                                           name="attendance[{{ $data['row_index'] ?? $index }}][check_out]" 
                                           value="{{ $data['check_out'] ?? '' }}"
                                           class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <input type="hidden" name="attendance[{{ $data['row_index'] ?? $index }}][schedule_latitude]" value="{{ $data['schedule_latitude'] ?? '' }}">
                                    <input type="hidden" name="attendance[{{ $data['row_index'] ?? $index }}][schedule_longitude]" value="{{ $data['schedule_longitude'] ?? '' }}">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $data['raw_time'] ?? 'Manual Entry' }}</code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button type="button" onclick="deleteRow(this)" class="text-red-600 hover:text-red-800">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        <span id="selectedCount">{{ count($previewData) }}</span> dari {{ count($previewData) }} data dipilih
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('attendance.import.form') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Konfirmasi Import (<span id="confirmCount">{{ count($previewData) }}</span> data)
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Catatan Penting</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Hanya data yang dicentang yang akan diimport</li>
                            <li>Anda dapat mengedit nama karyawan, tanggal, dan waktu sebelum import</li>
                            <li>Jika data absensi untuk tanggal yang sama sudah ada, data akan di-update</li>
                            <li>Pastikan semua data sudah benar sebelum konfirmasi import</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    const confirmCount = document.getElementById('confirmCount');
    const totalCount = document.getElementById('totalCount');
    const visibleCount = document.getElementById('visibleCount');

    // Select all functionality
    selectAll.addEventListener('change', function() {
        const visibleRows = document.querySelectorAll('.data-row:not([style*="display: none"])');
        visibleRows.forEach(row => {
            const checkbox = row.querySelector('.row-checkbox');
            if (checkbox) checkbox.checked = this.checked;
        });
        updateCount();
    });

    // Update count when individual checkbox changes
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAll();
            updateCount();
        });
    });

    // Update select all checkbox state
    function updateSelectAll() {
        const visibleCheckboxes = Array.from(document.querySelectorAll('.data-row:not([style*="display: none"]) .row-checkbox'));
        const allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.every(cb => cb.checked);
        const someChecked = visibleCheckboxes.some(cb => cb.checked);
        
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
    }

    // Update selected count
    function updateCount() {
        const count = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('.data-row').style.display !== 'none').length;
        selectedCount.textContent = count;
        confirmCount.textContent = count;
    }

    // Update visible count
    window.updateVisibleCount = function() {
        const visible = document.querySelectorAll('.data-row:not([style*="display: none"])').length;
        visibleCount.textContent = visible;
        updateCount();
        updateSelectAll();
    }

    // Confirm before submit
    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const count = Array.from(rowCheckboxes).filter(cb => cb.checked && cb.closest('.data-row').style.display !== 'none').length;
        
        if (count === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Data',
                text: 'Tidak ada data yang dipilih untuk diimport!',
                confirmButtonColor: '#4F46E5'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Import',
            text: `Anda akan mengimport ${count} data absensi. Lanjutkan?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Import!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Initial count
    updateCount();
});

// Filter by date range
function applyDateFilter() {
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    
    const rows = document.querySelectorAll('.data-row');
    
    rows.forEach(row => {
        const rowDate = row.dataset.date;
        let show = true;
        
        if (dateFrom && rowDate < dateFrom) {
            show = false;
        }
        if (dateTo && rowDate > dateTo) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    window.updateVisibleCount();
}

// Reset filter
function resetFilter() {
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    
    const rows = document.querySelectorAll('.data-row');
    rows.forEach(row => {
        row.style.display = '';
    });
    
    window.updateVisibleCount();
}

// Delete row
function deleteRow(button) {
    Swal.fire({
        title: 'Hapus Data?',
        text: 'Data ini akan dihapus dari preview. Anda yakin?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const row = button.closest('tr');
            row.remove();
            window.updateVisibleCount();
            
            // Reindex rows
            const rows = document.querySelectorAll('.data-row');
            rows.forEach((row, index) => {
                row.querySelector('td:nth-child(2)').textContent = index + 1;
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Terhapus!',
                text: 'Data berhasil dihapus dari preview',
                timer: 1500,
                showConfirmButton: false
            });
        }
    });
}

// Add new row
let newRowIndex = {{ count($previewData) }};
function addNewRow() {
    const tbody = document.getElementById('tableBody');
    const today = new Date().toISOString().split('T')[0];
    
    const newRow = document.createElement('tr');
    newRow.className = 'hover:bg-gray-50 data-row bg-green-50';
    newRow.dataset.date = today;
    
    newRow.innerHTML = `
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="checkbox" 
                   name="attendance[${newRowIndex}][import]" 
                   value="1" 
                   checked
                   class="row-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            ${newRowIndex + 1}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="hidden" name="attendance[${newRowIndex}][user_id]" value="">
            <select name="attendance[${newRowIndex}][user_id_editable]" 
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    onchange="this.previousElementSibling.value = this.value" required>
                <option value="">-- Pilih Karyawan --</option>
                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="date" 
                   name="attendance[${newRowIndex}][date]" 
                   value="${today}"
                   class="date-input text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                   required>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="time" 
                   name="attendance[${newRowIndex}][check_in]" 
                   value="08:00"
                   class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                   required>
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
            <input type="time" 
                   name="attendance[${newRowIndex}][check_out]" 
                   value="17:00"
                   class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
            <input type="hidden" name="attendance[${newRowIndex}][schedule_latitude]" value="">
            <input type="hidden" name="attendance[${newRowIndex}][schedule_longitude]" value="">
            <code class="bg-green-100 px-2 py-1 rounded text-xs">Manual Entry</code>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm">
            <button type="button" onclick="deleteRow(this)" class="text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    
    // Add event listener to new checkbox
    const newCheckbox = newRow.querySelector('.row-checkbox');
    newCheckbox.addEventListener('change', function() {
        window.updateVisibleCount();
    });
    
    // Update date attribute when date input changes
    const dateInput = newRow.querySelector('.date-input');
    dateInput.addEventListener('change', function() {
        newRow.dataset.date = this.value;
    });
    
    newRowIndex++;
    window.updateVisibleCount();
    
    // Scroll to new row
    newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Sort table
let sortOrders = {
    no: 'asc',
    karyawan: 'asc',
    tanggal: 'asc',
    checkin: 'asc',
    checkout: 'asc'
};

function sortTable(column) {
    const tbody = document.getElementById('tableBody');
    const rows = Array.from(tbody.querySelectorAll('.data-row'));
    
    // Toggle sort order
    const order = sortOrders[column];
    const newOrder = order === 'asc' ? 'desc' : 'asc';
    sortOrders[column] = newOrder;
    
    // Sort rows
    rows.sort((a, b) => {
        let aVal, bVal;
        
        if (column === 'no') {
            aVal = parseInt(a.dataset.no) || 0;
            bVal = parseInt(b.dataset.no) || 0;
        } else if (column === 'karyawan') {
            aVal = a.dataset.karyawan.toLowerCase();
            bVal = b.dataset.karyawan.toLowerCase();
        } else if (column === 'tanggal') {
            aVal = a.dataset.date;
            bVal = b.dataset.date;
        } else if (column === 'checkin') {
            aVal = a.dataset.checkin || '';
            bVal = b.dataset.checkin || '';
        } else if (column === 'checkout') {
            aVal = a.dataset.checkout || '';
            bVal = b.dataset.checkout || '';
        }
        
        if (aVal < bVal) return newOrder === 'asc' ? -1 : 1;
        if (aVal > bVal) return newOrder === 'asc' ? 1 : -1;
        return 0;
    });
    
    // Re-append rows in sorted order
    rows.forEach(row => tbody.appendChild(row));
    
    // Update row numbers
    const visibleRows = tbody.querySelectorAll('.data-row');
    visibleRows.forEach((row, index) => {
        row.querySelector('td:nth-child(2)').textContent = index + 1;
        row.dataset.no = index + 1;
    });
    
    // Show sort indicator
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'info',
        title: `Diurutkan: ${column} (${newOrder === 'asc' ? 'A-Z / Terkecil' : 'Z-A / Terbesar'})`,
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true
    });
}
</script>
@endsection
