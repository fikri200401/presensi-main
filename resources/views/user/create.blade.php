@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('user.store') }}" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">Select Role (Optional)</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
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
                        <option value="{{ $key }}" {{ old('division') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('division')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="position" class="block text-sm font-medium text-gray-700">Jabatan <span class="text-xs text-gray-400 font-normal">(otomatis dari Role & Divisi)</span></label>
                <input type="text" id="position" name="position" value="{{ old('position') }}"
                    readonly
                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-600 shadow-sm sm:text-sm cursor-not-allowed">
                @error('position')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="nip" class="block text-sm font-medium text-gray-700">NIP</label>
                <input type="text" id="nip" name="nip" value="{{ old('nip') }}"
                    placeholder="Nomor Induk Pegawai"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('nip')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telepon / WA</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                    placeholder="+62 812 3456 7890"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('user.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rolePositionMap = @json(\App\Models\User::ROLE_POSITION_MAP);

    function updatePosition() {
        const role = document.getElementById('role').value;
        const division = document.getElementById('division');
        const divisionValue = division.value || '';
        const positionField = document.getElementById('position');

        if (role && rolePositionMap[role]) {
            positionField.value = rolePositionMap[role].replace(':division', divisionValue);
        } else {
            positionField.value = '';
        }
    }

    document.getElementById('role').addEventListener('change', updatePosition);
    document.getElementById('division').addEventListener('change', updatePosition);
</script>
@endpush
