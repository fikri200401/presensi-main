@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Generate Payroll</h1>
            <p class="mt-2 text-sm text-gray-700">
                Pilih karyawan dan periode untuk generate payroll secara otomatis berdasarkan data kehadiran.
            </p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="mt-4 rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                @foreach($errors->all() as $error)
                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(session('errors'))
    <div class="mt-4 rounded-md bg-yellow-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Beberapa payroll gagal di-generate:</h3>
                <div class="mt-2 text-sm text-yellow-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <div class="mt-8">
        <form method="POST" action="{{ route('payroll.generate') }}" class="space-y-6">
            @csrf
            
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-6">
                        <!-- Periode Selection -->
                        <div>
                            <label for="periode" class="block text-sm font-medium text-gray-900">
                                Periode <span class="text-red-600">*</span>
                            </label>
                            <div class="mt-2">
                                <input 
                                    type="month" 
                                    name="periode" 
                                    id="periode" 
                                    required
                                    value="{{ old('periode', date('Y-m')) }}"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                >
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Pilih bulan dan tahun untuk periode payroll</p>
                        </div>

                        <!-- Employee Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-900 mb-2">
                                Pilih Karyawan <span class="text-red-600">*</span>
                            </label>
                            
                            <div class="mb-3">
                                <button 
                                    type="button" 
                                    onclick="selectAll()"
                                    class="text-sm text-indigo-600 hover:text-indigo-900 mr-4"
                                >
                                    Pilih Semua
                                </button>
                                <button 
                                    type="button" 
                                    onclick="deselectAll()"
                                    class="text-sm text-gray-600 hover:text-gray-900"
                                >
                                    Hapus Semua
                                </button>
                            </div>

                            @if($employees->count() > 0)
                            <div class="border border-gray-300 rounded-md p-4 max-h-96 overflow-y-auto">
                                <div class="space-y-2">
                                    @foreach($employees as $employee)
                                    <div class="flex items-start">
                                        <div class="flex h-6 items-center">
                                            <input 
                                                id="employee_{{ $employee->id }}" 
                                                name="user_ids[]" 
                                                type="checkbox" 
                                                value="{{ $employee->id }}"
                                                {{ in_array($employee->id, old('user_ids', [])) ? 'checked' : '' }}
                                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 employee-checkbox"
                                            >
                                        </div>
                                        <div class="ml-3 text-sm leading-6">
                                            <label for="employee_{{ $employee->id }}" class="font-medium text-gray-900">
                                                {{ $employee->name }}
                                            </label>
                                            <p class="text-gray-500">{{ $employee->email }}</p>
                                            @if($employee->employeeSalary)
                                            <p class="text-xs text-green-600 mt-1">
                                                ✓ Gaji Pokok: Rp {{ number_format($employee->employeeSalary->gaji_pokok_bulanan, 0, ',', '.') }}
                                                ({{ ucfirst($employee->employeeSalary->metode_perhitungan) }})
                                            </p>
                                            @else
                                            <p class="text-xs text-red-600 mt-1">⚠ Belum ada konfigurasi gaji</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Total {{ $employees->count() }} karyawan dengan konfigurasi gaji aktif
                            </p>
                            @else
                            <div class="text-center py-8 bg-gray-50 rounded-md">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada karyawan</h3>
                                <p class="mt-1 text-sm text-gray-500">Belum ada karyawan dengan konfigurasi gaji aktif.</p>
                                <div class="mt-6">
                                    <a href="{{ route('employee-salary.create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Setup Gaji Karyawan
                                    </a>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Info Box -->
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800">Informasi Generate Payroll</h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <ul class="list-disc space-y-1 pl-5">
                                            <li>Sistem akan mengambil data kehadiran dari periode yang dipilih</li>
                                            <li>Gaji akan dihitung otomatis berdasarkan metode perhitungan (bulanan/harian/jam)</li>
                                            <li>Tunjangan dan potongan akan diterapkan sesuai konfigurasi</li>
                                            <li>Payroll yang sudah ada untuk periode yang sama akan di-skip</li>
                                            <li>Status awal payroll adalah "Draft" dan perlu approval</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button 
                        type="submit" 
                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto"
                        {{ $employees->count() == 0 ? 'disabled' : '' }}
                    >
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Generate Payroll
                    </button>
                    <a 
                        href="{{ route('payroll.index') }}" 
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                    >
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function selectAll() {
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.employee-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection
