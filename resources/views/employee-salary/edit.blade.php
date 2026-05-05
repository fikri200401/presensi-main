@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Edit Gaji Karyawan</h1>
            <p class="mt-2 text-sm text-gray-700">Perbarui gaji pokok, tunjangan tetap, dan potongan personal karyawan.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mt-4 rounded-md bg-red-50 p-4">
        @foreach($errors->all() as $error)
        <p class="text-sm font-medium text-red-800">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <div class="mt-8">
        <form method="POST" action="{{ route('employee-salary.update', $employeeSalary) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $employeeSalary->user->name }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ $employeeSalary->user->email }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="tipe_karyawan" class="block text-sm font-medium text-gray-900">Tipe Karyawan</label>
                            <select name="tipe_karyawan" id="tipe_karyawan" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                                <option value="tetap" @selected(old('tipe_karyawan', $employeeSalary->tipe_karyawan) === 'tetap')>Tetap</option>
                                <option value="harian" @selected(old('tipe_karyawan', $employeeSalary->tipe_karyawan) === 'harian')>Harian</option>
                                <option value="paruh_waktu" @selected(old('tipe_karyawan', $employeeSalary->tipe_karyawan) === 'paruh_waktu')>Paruh Waktu</option>
                            </select>
                        </div>

                        <div>
                            <label for="metode_perhitungan" class="block text-sm font-medium text-gray-900">Metode Perhitungan</label>
                            <select name="metode_perhitungan" id="metode_perhitungan" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                                <option value="bulanan" @selected(old('metode_perhitungan', $employeeSalary->metode_perhitungan) === 'bulanan')>Bulanan</option>
                                <option value="harian" @selected(old('metode_perhitungan', $employeeSalary->metode_perhitungan) === 'harian')>Harian</option>
                                <option value="jam" @selected(old('metode_perhitungan', $employeeSalary->metode_perhitungan) === 'jam')>Per Jam</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="gaji_pokok_bulanan" class="block text-sm font-medium text-gray-900">Gaji Pokok Bulanan</label>
                            <input type="number" name="gaji_pokok_bulanan" id="gaji_pokok_bulanan" min="0" step="1000" value="{{ old('gaji_pokok_bulanan', $employeeSalary->gaji_pokok_bulanan) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Tunjangan Tetap</h3>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div>
                            <label for="tunjangan_transport" class="block text-sm font-medium text-gray-900">Transport</label>
                            <input type="number" name="tunjangan_transport" id="tunjangan_transport" min="0" step="1000" value="{{ old('tunjangan_transport', $employeeSalary->tunjangan_transport) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                        <div>
                            <label for="tunjangan_makan" class="block text-sm font-medium text-gray-900">Makan</label>
                            <input type="number" name="tunjangan_makan" id="tunjangan_makan" min="0" step="1000" value="{{ old('tunjangan_makan', $employeeSalary->tunjangan_makan) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                        <div>
                            <label for="tunjangan_lainnya" class="block text-sm font-medium text-gray-900">Lainnya</label>
                            <input type="number" name="tunjangan_lainnya" id="tunjangan_lainnya" min="0" step="1000" value="{{ old('tunjangan_lainnya', $employeeSalary->tunjangan_lainnya) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Potongan Personal</h3>
                    <p class="text-sm text-gray-500">JHT dan JKS tetap dihitung dari Pengaturan Gaji saat payroll di-generate.</p>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="potongan_pph21" class="block text-sm font-medium text-gray-900">PPH21</label>
                            <input type="number" name="potongan_pph21" id="potongan_pph21" min="0" step="1000" value="{{ old('potongan_pph21', $employeeSalary->potongan_pph21) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                        <div>
                            <label for="potongan_lainnya" class="block text-sm font-medium text-gray-900">Potongan Lainnya</label>
                            <input type="number" name="potongan_lainnya" id="potongan_lainnya" min="0" step="1000" value="{{ old('potongan_lainnya', $employeeSalary->potongan_lainnya) }}" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-blue-600 sm:text-sm">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employeeSalary->is_active)) class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        Aktif
                    </label>
                </div>

                @include('employee-salary.partials.salary-components', ['salaryComponents' => $salaryComponents, 'employeeSalary' => $employeeSalary])

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Simpan</button>
                    <a href="{{ route('employee-salary.index') }}" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
