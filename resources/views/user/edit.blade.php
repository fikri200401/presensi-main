@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('user.update', $user) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password <span class="text-gray-500 font-normal">(leave blank to keep current)</span></label>
                <input type="password" id="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select Role (Optional)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="division" class="block text-sm font-medium text-gray-700">Divisi</label>
                <select id="division" name="division"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">— Pilih Divisi —</option>
                    @foreach(\App\Models\Division::dropdown() as $key => $label)
                        <option value="{{ $key }}" {{ old('division', $user->division) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('division')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Jabatan</label>
                <input type="text" id="position" name="position" value="{{ old('position', $user->position) }}"
                    placeholder="Contoh: Staff, Manager, Kepala Unit"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('position')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip', $user->nip) }}"
                    placeholder="Nomor Induk Pegawai"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telepon / WA</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                    placeholder="+62 812 3456 7890"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('user.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update User
                </button>
            </div>
        </form>
    </div>

    {{-- ── RIWAYAT JABATAN ─────────────────────────────────── --}}
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6" x-data="{ showForm: false, editId: null, editData: {} }">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" /></svg>
                    Riwayat Jabatan
                </h3>
                <button @click="showForm = !showForm; editId = null; editData = {}" type="button"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Tambah Riwayat
                </button>
            </div>

            {{-- Add / Edit Form --}}
            <div x-show="showForm" x-transition class="mb-5 p-4 bg-blue-50/50 border border-blue-100 rounded-xl">
                <form :action="editId ? '/position-history/' + editId : '{{ route('position-history.store', $user) }}'" method="POST" class="space-y-4">
                    @csrf
                    <template x-if="editId"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                            <input type="text" name="position" x-model="editData.position" required placeholder="Karyawan, Kepala Divisi, dll"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Divisi</label>
                            <select name="division" x-model="editData.division"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">— Pilih Divisi —</option>
                                @foreach(\App\Models\Division::dropdown() as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Role Sistem</label>
                            <select name="role" x-model="editData.role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">— Pilih —</option>
                                <option value="employee">Employee</option>
                                <option value="kepala_divisi">Kepala Divisi</option>
                                <option value="admin">Admin</option>
                                <option value="direksi">Direksi</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" x-model="editData.start_date" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Tanggal Selesai <span class="text-gray-400 font-normal">(kosongkan jika masih aktif)</span></label>
                            <input type="date" name="end_date" x-model="editData.end_date"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Keterangan</label>
                            <input type="text" name="description" x-model="editData.description" placeholder="Opsional"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <span x-text="editId ? 'Update' : 'Simpan'"></span>
                        </button>
                        <button type="button" @click="showForm = false" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            {{-- History List --}}
            @if($user->positionHistories->count() > 0)
            <div class="space-y-3">
                @foreach($user->positionHistories as $history)
                <div class="flex items-start gap-3 p-4 rounded-xl {{ $history->is_current ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 border border-gray-100' }}">
                    <div class="flex-shrink-0 mt-0.5">
                        @if($history->is_current)
                        <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                        </div>
                        @else
                        <div class="h-8 w-8 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center">
                            <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                        </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $history->position }}</p>
                                @if($history->division)
                                <p class="text-xs text-gray-500">Divisi {{ $history->division }}</p>
                                @endif
                                @if($history->description)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $history->description }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs font-medium text-gray-600">{{ $history->period_label }}</p>
                                @if($history->is_current)
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-600 text-white mt-0.5">Aktif</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 flex gap-1">
                        <button type="button" @click="showForm = true; editId = {{ $history->id }}; editData = { position: '{{ addslashes($history->position) }}', division: '{{ addslashes($history->division ?? '') }}', role: '{{ $history->role ?? '' }}', start_date: '{{ $history->start_date->format('Y-m-d') }}', end_date: '{{ $history->end_date ? $history->end_date->format('Y-m-d') : '' }}', description: '{{ addslashes($history->description ?? '') }}' }"
                            class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                        </button>
                        <form method="POST" action="{{ route('position-history.destroy', $history) }}" class="inline" onsubmit="return confirm('Hapus riwayat jabatan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6">
                <p class="text-sm text-gray-400">Belum ada riwayat jabatan.</p>
                <p class="text-xs text-gray-300 mt-1">Klik "Tambah Riwayat" untuk menambahkan.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
