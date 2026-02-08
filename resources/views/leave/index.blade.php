@extends('layouts.app')

@section('title', 'Leave Requests')
@section('page-title', 'Leave Management')

@section('content')
<!-- Rejection Modal -->
<div id="rejectionModal" class="hidden fixed inset-0 z-50" style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md" style="max-height: 85vh; display: flex; flex-direction: column;">
            <!-- Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900">Reject Leave Request</h3>
                <button type="button" onclick="closeRejectionModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Form Content -->
            <form id="rejectionForm" method="POST" action="" style="display: flex; flex-direction: column; flex: 1; overflow-y: auto;">
                @csrf
                <div class="p-6" style="flex: 1;">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="rejection_reason" 
                        name="rejection_reason" 
                        rows="4" 
                        required
                        minlength="10"
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;"
                        placeholder="Contoh: Kuota cuti bulan ini sudah penuh, mohon ajukan di bulan berikutnya..."></textarea>
                    <p class="mt-2 text-xs text-gray-500">
                        ℹ️ Minimal 10 karakter. Alasan akan dikirim ke karyawan.
                    </p>
                </div>
                
                <!-- Footer Buttons - ALWAYS VISIBLE -->
                <div class="p-6 pt-4 border-t border-gray-200" style="background-color: #f9fafb;">
                    <div class="flex gap-3 justify-end">
                        <button 
                            type="button" 
                            onclick="closeRejectionModal()"
                            style="padding: 0.625rem 1.25rem; background-color: white; border: 1px solid #d1d5db; color: #374151; border-radius: 0.375rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;"
                            onmouseover="this.style.backgroundColor='#f3f4f6'"
                            onmouseout="this.style.backgroundColor='white'">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            style="padding: 0.625rem 1.25rem; background-color: #dc2626; color: white; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;"
                            onmouseover="this.style.backgroundColor='#b91c1c'"
                            onmouseout="this.style.backgroundColor='#dc2626'">
                            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Reject Leave
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Reason Modal (View Only) -->
<div id="reasonModal" class="hidden fixed inset-0 z-50" style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Alasan Penolakan
                    </h3>
                    <button type="button" onclick="closeReasonModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
                    <p id="reasonText" class="text-sm text-gray-700"></p>
                </div>
                
                <div class="flex justify-end">
                    <button 
                        type="button" 
                        onclick="closeReasonModal()"
                        style="padding: 0.625rem 1.25rem; background-color: #dc2626; color: white; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#b91c1c'"
                        onmouseout="this.style.backgroundColor='#dc2626'">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="hidden fixed inset-0 z-50" style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div id="confirmIcon" class="flex-shrink-0"></div>
                    <div class="flex-1">
                        <h3 id="confirmTitle" class="text-lg font-semibold text-gray-900 mb-2"></h3>
                        <p id="confirmMessage" class="text-sm text-gray-600"></p>
                    </div>
                </div>
                
                <div class="flex gap-3 justify-end pt-4 border-t border-gray-200">
                    <button 
                        type="button" 
                        onclick="closeConfirmModal()"
                        style="padding: 0.625rem 1.25rem; background-color: white; border: 1px solid #d1d5db; color: #374151; border-radius: 0.375rem; font-weight: 500; font-size: 0.875rem; cursor: pointer;"
                        onmouseover="this.style.backgroundColor='#f3f4f6'"
                        onmouseout="this.style.backgroundColor='white'">
                        Batal
                    </button>
                    <button 
                        type="button" 
                        id="confirmButton"
                        onclick="confirmAction()"
                        style="padding: 0.625rem 1.25rem; border: none; border-radius: 0.375rem; font-weight: 500; font-size: 0.875rem; cursor: pointer; color: white;">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-4 flex items-center justify-between">
    <div class="flex-1 max-w-lg flex gap-2">
        <form method="GET" action="{{ route('leave.index') }}" class="flex gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search by employee..." 
                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                Search
            </button>
        </form>
    </div>
    @can('create_leave')
        <a href="{{ route('leave.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add New
        </a>
    @endcan
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leaves as $leave)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold">
                                {{ substr($leave->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $leave->user->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($leave->reason, 50) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($leave->status === 'pending')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @elseif($leave->status === 'approved')
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Approved</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Rejected</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-3 items-center">
                            @if(auth()->user()->hasRole(['super_admin', 'admin']))
                                @if($leave->status === 'pending')
                                    <!-- Approve Button -->
                                    <button 
                                        type="button" 
                                        onclick="showConfirm('approve', {{ $leave->id }}, 'Approve leave request untuk {{ addslashes($leave->user->name) }}?')"
                                        class="text-green-600 hover:text-green-900" 
                                        title="Approve">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                    
                                    <!-- Reject Button -->
                                    <button 
                                        type="button" 
                                        onclick="openRejectionModal({{ $leave->id }})" 
                                        class="text-red-600 hover:text-red-900"
                                        title="Reject">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">{{ ucfirst($leave->status) }}</span>
                                    @if($leave->status === 'rejected' && $leave->note)
                                        <button 
                                            type="button"
                                            onclick="showRejectionReason('{{ addslashes($leave->note) }}')"
                                            class="text-blue-600 hover:text-blue-900 text-xs"
                                            title="Lihat Alasan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                @endif
                                
                                <!-- Delete Button -->
                                <button 
                                    type="button"
                                    onclick="showConfirm('delete', {{ $leave->id }}, 'Hapus leave request untuk {{ addslashes($leave->user->name) }}?')"
                                    class="text-gray-400 hover:text-red-600" 
                                    title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            @else
                                <!-- Employee View -->
                                @if($leave->status === 'rejected' && $leave->note)
                                    <button 
                                        type="button"
                                        onclick="showRejectionReason('{{ addslashes($leave->note) }}')"
                                        class="text-red-600 hover:text-red-900 text-xs flex items-center gap-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Lihat Alasan
                                    </button>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        No leave requests found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $leaves->links() }}
    </div>
</div>

<script>
    let confirmCallback = null;

    // Rejection Modal Functions
    function openRejectionModal(leaveId) {
        const modal = document.getElementById('rejectionModal');
        const form = document.getElementById('rejectionForm');
        form.action = `/leave/${leaveId}/reject`;
        modal.classList.remove('hidden');
    }

    function closeRejectionModal() {
        const modal = document.getElementById('rejectionModal');
        const form = document.getElementById('rejectionForm');
        form.reset();
        modal.classList.add('hidden');
    }

    // Rejection Reason Modal Functions
    function showRejectionReason(reason) {
        const modal = document.getElementById('reasonModal');
        const reasonText = document.getElementById('reasonText');
        reasonText.textContent = reason;
        modal.classList.remove('hidden');
    }

    function closeReasonModal() {
        const modal = document.getElementById('reasonModal');
        modal.classList.add('hidden');
    }

    // Confirmation Modal Functions
    function showConfirm(action, leaveId, message) {
        const modal = document.getElementById('confirmModal');
        const title = document.getElementById('confirmTitle');
        const msg = document.getElementById('confirmMessage');
        const icon = document.getElementById('confirmIcon');
        const btn = document.getElementById('confirmButton');
        
        if (action === 'approve') {
            title.textContent = 'Approve Leave Request';
            msg.textContent = message;
            icon.innerHTML = `
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            `;
            btn.style.backgroundColor = '#16a34a';
            confirmCallback = () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/leave/${leaveId}/approve`;
                form.innerHTML = '@csrf';
                document.body.appendChild(form);
                form.submit();
            };
        } else if (action === 'delete') {
            title.textContent = 'Hapus Leave Request';
            msg.textContent = message;
            icon.innerHTML = `
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            `;
            btn.style.backgroundColor = '#dc2626';
            confirmCallback = () => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/leave/${leaveId}`;
                form.innerHTML = '@csrf @method("DELETE")';
                document.body.appendChild(form);
                form.submit();
            };
        }
        
        modal.classList.remove('hidden');
    }

    function closeConfirmModal() {
        const modal = document.getElementById('confirmModal');
        modal.classList.add('hidden');
        confirmCallback = null;
    }

    function confirmAction() {
        if (confirmCallback) {
            confirmCallback();
        }
        closeConfirmModal();
    }

    // Close modals when clicking outside
    document.getElementById('rejectionModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeRejectionModal();
        }
    });

    document.getElementById('reasonModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeReasonModal();
        }
    });

    document.getElementById('confirmModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeConfirmModal();
        }
    });
</script>
@endsection
