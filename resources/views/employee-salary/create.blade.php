@extends('layouts.app')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-semibold text-gray-900">Setup Gaji Karyawan</h1>
            <p class="mt-2 text-sm text-gray-700">
                Konfigurasi gaji pokok, tunjangan, dan potongan untuk karyawan.
            </p>
        </div>
    </div>

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

    <div class="mt-8">
        <form method="POST" action="{{ route('employee-salary.store') }}" class="space-y-6">
            @csrf
            
            <div class="bg-white shadow sm:rounded-lg">
                <!-- Basic Info -->
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Informasi Dasar</h3>
                        <p class="mt-1 text-sm text-gray-500">Data karyawan dan tipe employment</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-900">
                                Karyawan <span class="text-red-600">*</span>
                            </label>
                            <select name="user_id" id="user_id" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tipe_karyawan" class="block text-sm font-medium text-gray-900">
                                Tipe Karyawan <span class="text-red-600">*</span>
                            </label>
                            <select name="tipe_karyawan" id="tipe_karyawan" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <option value="tetap" {{ old('tipe_karyawan') == 'tetap' ? 'selected' : '' }}>Tetap</option>
                                <option value="harian" {{ old('tipe_karyawan') == 'harian' ? 'selected' : '' }}>Harian</option>
                                <option value="paruh_waktu" {{ old('tipe_karyawan') == 'paruh_waktu' ? 'selected' : '' }}>Paruh Waktu</option>
                            </select>
                        </div>

                        <div>
                            <label for="metode_perhitungan" class="block text-sm font-medium text-gray-900">
                                Metode Perhitungan <span class="text-red-600">*</span>
                            </label>
                            <select name="metode_perhitungan" id="metode_perhitungan" required class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <option value="bulanan" {{ old('metode_perhitungan', 'bulanan') == 'bulanan' ? 'selected' : '' }}>Bulanan (Gaji tetap)</option>
                                <option value="harian" {{ old('metode_perhitungan') == 'harian' ? 'selected' : '' }}>Harian (Berdasarkan hari hadir)</option>
                                <option value="jam" {{ old('metode_perhitungan') == 'jam' ? 'selected' : '' }}>Per Jam (Berdasarkan jam kerja)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                <span class="font-semibold">Bulanan:</span> Gaji tetap per bulan<br>
                                <span class="font-semibold">Harian:</span> Gaji = hari hadir × gaji per hari<br>
                                <span class="font-semibold">Per Jam:</span> Gaji = jam kerja × gaji per jam
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Gaji Pokok -->
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Gaji Pokok</h3>
                        <p class="mt-1 text-sm text-gray-500">Gaji pokok akan dihitung otomatis menjadi gaji per hari dan per jam</p>
                    </div>

                    <div>
                        <label for="gaji_pokok_bulanan" class="block text-sm font-medium text-gray-900">
                            Gaji Pokok Bulanan <span class="text-red-600">*</span>
                        </label>
                        <div class="mt-2 relative rounded-md shadow-sm">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input 
                                type="number" 
                                name="gaji_pokok_bulanan" 
                                id="gaji_pokok_bulanan" 
                                required
                                min="0"
                                step="1000"
                                value="{{ old('gaji_pokok_bulanan') }}"
                                onkeyup="calculateRates()"
                                class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                                placeholder="5000000"
                            >
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <div class="bg-blue-50 p-3 rounded-md">
                                <p class="font-semibold text-blue-900 mb-2">Auto-calculated (Formula KEP. 102/MEN/VI/2004):</p>
                                <p id="gaji_per_hari_display" class="text-blue-800">Gaji per hari: Rp 0</p>
                                <p id="gaji_per_jam_display" class="text-blue-800">Gaji per jam: Rp 0</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="berlaku_dari" class="block text-sm font-medium text-gray-900">
                            Berlaku Dari
                        </label>
                        <input 
                            type="date" 
                            name="berlaku_dari" 
                            id="berlaku_dari"
                            value="{{ old('berlaku_dari', date('Y-m-d')) }}"
                            class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"
                        >
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Tunjangan -->
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Tunjangan</h3>
                        <p class="mt-1 text-sm text-gray-500">Default: Rp {{ number_format($settings->tunjangan_transport_default, 0, ',', '.') }} (Transport), Rp {{ number_format($settings->tunjangan_makan_default, 0, ',', '.') }} (Makan)</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="tunjangan_transport" class="block text-sm font-medium text-gray-900">Tunjangan Transport</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="tunjangan_transport" id="tunjangan_transport" min="0" step="1000" value="{{ old('tunjangan_transport', $settings->tunjangan_transport_default) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="tunjangan_makan" class="block text-sm font-medium text-gray-900">Tunjangan Makan</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="tunjangan_makan" id="tunjangan_makan" min="0" step="1000" value="{{ old('tunjangan_makan', $settings->tunjangan_makan_default) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="tunjangan_jabatan" class="block text-sm font-medium text-gray-900">Tunjangan Jabatan</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="tunjangan_jabatan" id="tunjangan_jabatan" min="0" step="1000" value="{{ old('tunjangan_jabatan', 0) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="tunjangan_keluarga" class="block text-sm font-medium text-gray-900">Tunjangan Keluarga</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="tunjangan_keluarga" id="tunjangan_keluarga" min="0" step="1000" value="{{ old('tunjangan_keluarga', 0) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="tunjangan_lainnya" class="block text-sm font-medium text-gray-900">Tunjangan Lainnya</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="tunjangan_lainnya" id="tunjangan_lainnya" min="0" step="1000" value="{{ old('tunjangan_lainnya', 0) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Potongan -->
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Potongan</h3>
                        <p class="mt-1 text-sm text-gray-500">Default: BPJS Kesehatan {{ $settings->potongan_bpjs_kesehatan_persen }}%, BPJS Ketenagakerjaan {{ $settings->potongan_bpjs_ketenagakerjaan_persen }}%</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="potongan_bpjs_kesehatan_persen" class="block text-sm font-medium text-gray-900">BPJS Kesehatan (%)</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" name="potongan_bpjs_kesehatan_persen" id="potongan_bpjs_kesehatan_persen" min="0" max="100" step="0.01" value="{{ old('potongan_bpjs_kesehatan_persen', $settings->potongan_bpjs_kesehatan_persen) }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="potongan_bpjs_ketenagakerjaan_persen" class="block text-sm font-medium text-gray-900">BPJS Ketenagakerjaan (%)</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <input type="number" name="potongan_bpjs_ketenagakerjaan_persen" id="potongan_bpjs_ketenagakerjaan_persen" min="0" max="100" step="0.01" value="{{ old('potongan_bpjs_ketenagakerjaan_persen', $settings->potongan_bpjs_ketenagakerjaan_persen) }}" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="potongan_pph21" class="block text-sm font-medium text-gray-900">PPH21</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="potongan_pph21" id="potongan_pph21" min="0" step="1000" value="{{ old('potongan_pph21', 0) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="potongan_lainnya" class="block text-sm font-medium text-gray-900">Potongan Lainnya</label>
                            <div class="mt-2 relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">Rp</span>
                                </div>
                                <input type="number" name="potongan_lainnya" id="potongan_lainnya" min="0" step="1000" value="{{ old('potongan_lainnya', 0) }}" class="block w-full rounded-md border-0 py-1.5 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">
                        Simpan
                    </button>
                    <a href="{{ route('payroll.create') }}" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function calculateRates() {
    const gajiPokok = parseFloat(document.getElementById('gaji_pokok_bulanan').value) || 0;
    
    // Formula KEP. 102/MEN/VI/2004
    const gajiPerHari = gajiPokok / 21; // 21 hari kerja per bulan
    const gajiPerJam = gajiPokok / 173; // 173 jam per bulan
    
    document.getElementById('gaji_per_hari_display').textContent = 
        'Gaji per hari: Rp ' + Math.round(gajiPerHari).toLocaleString('id-ID');
    
    document.getElementById('gaji_per_jam_display').textContent = 
        'Gaji per jam: Rp ' + Math.round(gajiPerJam).toLocaleString('id-ID');
}

// Calculate on page load if value exists
window.addEventListener('DOMContentLoaded', function() {
    calculateRates();
});
</script>
@endsection
