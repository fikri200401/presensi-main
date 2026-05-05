@extends('layouts.app')

@section('title', 'Pengaturan Gaji')
@section('page-title', 'Pengaturan Gaji')

@section('content')
<div class="max-w-5xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pengaturan Gaji</h1>
            <p class="mt-1 text-sm text-gray-500">Atur aturan dasar payroll yang dipakai saat generate gaji bulanan.</p>
        </div>
        <a href="{{ route('payroll.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
            Kembali ke Payroll
        </a>
    </div>

    @if(session('success'))
    <div class="mb-5 rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="mb-5 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-700">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('salary-settings.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Dasar Perhitungan</h2>
            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="metode_perhitungan_default" class="block text-sm font-medium text-gray-700">Metode Default</label>
                    <select id="metode_perhitungan_default" name="metode_perhitungan_default" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="bulanan" @selected(old('metode_perhitungan_default', $settings->metode_perhitungan_default) === 'bulanan')>Bulanan</option>
                        <option value="harian" @selected(old('metode_perhitungan_default', $settings->metode_perhitungan_default) === 'harian')>Harian</option>
                        <option value="jam" @selected(old('metode_perhitungan_default', $settings->metode_perhitungan_default) === 'jam')>Per Jam</option>
                    </select>
                </div>

                <div>
                    <label for="jam_kerja_per_hari" class="block text-sm font-medium text-gray-700">Jam Kerja per Hari</label>
                    <input type="number" min="1" max="24" id="jam_kerja_per_hari" name="jam_kerja_per_hari" value="{{ old('jam_kerja_per_hari', $settings->jam_kerja_per_hari) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="hari_kerja_per_minggu" class="block text-sm font-medium text-gray-700">Hari Kerja per Minggu</label>
                    <input type="number" min="1" max="7" id="hari_kerja_per_minggu" name="hari_kerja_per_minggu" value="{{ old('hari_kerja_per_minggu', $settings->hari_kerja_per_minggu) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="hari_kerja_per_bulan" class="block text-sm font-medium text-gray-700">Hari Kerja per Bulan</label>
                    <input type="number" min="1" max="31" id="hari_kerja_per_bulan" name="hari_kerja_per_bulan" value="{{ old('hari_kerja_per_bulan', $settings->hari_kerja_per_bulan) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="total_jam_per_bulan" class="block text-sm font-medium text-gray-700">Total Jam per Bulan</label>
                    <input type="number" min="1" max="744" id="total_jam_per_bulan" name="total_jam_per_bulan" value="{{ old('total_jam_per_bulan', $settings->total_jam_per_bulan) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Tunjangan</h2>
            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-3">
                <div>
                    <label for="tunjangan_transport_default" class="block text-sm font-medium text-gray-700">Transport Default</label>
                    <input type="number" min="0" step="1000" id="tunjangan_transport_default" name="tunjangan_transport_default" value="{{ old('tunjangan_transport_default', $settings->tunjangan_transport_default) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tunjangan_makan_default" class="block text-sm font-medium text-gray-700">Makan Default</label>
                    <input type="number" min="0" step="1000" id="tunjangan_makan_default" name="tunjangan_makan_default" value="{{ old('tunjangan_makan_default', $settings->tunjangan_makan_default) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="tunjangan_jabatan_kepala_divisi" class="block text-sm font-medium text-gray-700">Tunjangan Kepala Divisi</label>
                    <input type="number" min="0" step="1000" id="tunjangan_jabatan_kepala_divisi" name="tunjangan_jabatan_kepala_divisi" value="{{ old('tunjangan_jabatan_kepala_divisi', $settings->tunjangan_jabatan_kepala_divisi) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Potongan Wajib</h2>
            <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="potongan_jht_persen" class="block text-sm font-medium text-gray-700">JHT (%)</label>
                    <input type="number" min="0" max="100" step="0.01" id="potongan_jht_persen" name="potongan_jht_persen" value="{{ old('potongan_jht_persen', $settings->potongan_jht_persen) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="potongan_jks_persen" class="block text-sm font-medium text-gray-700">JKS / BPJS Kesehatan (%)</label>
                    <input type="number" min="0" max="100" step="0.01" id="potongan_jks_persen" name="potongan_jks_persen" value="{{ old('potongan_jks_persen', $settings->potongan_jks_persen) }}" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <label for="catatan" class="block text-sm font-medium text-gray-700">Catatan Internal</label>
            <textarea id="catatan" name="catatan" rows="3" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('catatan', $settings->catatan) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
            <a href="{{ route('payroll.index') }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Simpan Pengaturan</button>
        </div>
    </form>

    <div class="mt-8 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Jenis Tunjangan & Potongan</h2>
                <p class="mt-1 text-sm text-gray-500">Komponen di sini bisa dipilih di halaman gaji karyawan dan ikut dihitung saat payroll digenerate.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('salary-components.store') }}" class="mt-5 grid grid-cols-1 gap-4 lg:grid-cols-6">
            @csrf
            <div class="lg:col-span-2">
                <label for="component_name" class="block text-sm font-medium text-gray-700">Nama Komponen</label>
                <input id="component_name" name="name" type="text" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Bonus Kinerja">
            </div>
            <div>
                <label for="component_type" class="block text-sm font-medium text-gray-700">Tipe</label>
                <select id="component_type" name="type" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="allowance">Tunjangan</option>
                    <option value="deduction">Potongan</option>
                </select>
            </div>
            <div>
                <label for="calculation_type" class="block text-sm font-medium text-gray-700">Cara Hitung</label>
                <select id="calculation_type" name="calculation_type" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="fixed">Nominal</option>
                    <option value="percentage">Persentase</option>
                    <option value="manual">Manual</option>
                </select>
            </div>
            <div>
                <label for="default_amount" class="block text-sm font-medium text-gray-700">Nominal Default</label>
                <input id="default_amount" name="default_amount" type="number" min="0" step="1000" value="0" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label for="default_percentage" class="block text-sm font-medium text-gray-700">Persen Default</label>
                <input id="default_percentage" name="default_percentage" type="number" min="0" max="100" step="0.01" value="0" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="lg:col-span-5">
                <label for="component_notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                <input id="component_notes" name="notes" type="text" class="mt-2 block w-full rounded-xl border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Tambah</button>
            </div>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Nama</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Tipe</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Cara Hitung</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Default</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($salaryComponents as $component)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $component->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $component->type_label }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $component->calculation_type_label }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            @if($component->calculation_type === 'percentage')
                                {{ $component->default_percentage }}%
                            @else
                                Rp {{ number_format($component->default_amount, 0, ',', '.') }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $component->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $component->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('salary-components.destroy', $component) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-700">Nonaktifkan</button>
                            </form>
                        </td>
                    </tr>
                    <tr class="bg-gray-50/60">
                        <td colspan="6" class="px-4 py-4">
                            <form method="POST" action="{{ route('salary-components.update', $component) }}" class="grid grid-cols-1 gap-3 lg:grid-cols-7">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" value="{{ $component->name }}" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500 lg:col-span-2">
                                <select name="type" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="allowance" @selected($component->type === 'allowance')>Tunjangan</option>
                                    <option value="deduction" @selected($component->type === 'deduction')>Potongan</option>
                                </select>
                                <select name="calculation_type" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="fixed" @selected($component->calculation_type === 'fixed')>Nominal</option>
                                    <option value="percentage" @selected($component->calculation_type === 'percentage')>Persentase</option>
                                    <option value="manual" @selected($component->calculation_type === 'manual')>Manual</option>
                                </select>
                                <input type="number" min="0" step="1000" name="default_amount" value="{{ $component->default_amount }}" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="number" min="0" max="100" step="0.01" name="default_percentage" value="{{ $component->default_percentage }}" class="rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                                <input type="hidden" name="notes" value="{{ $component->notes }}">
                                <div class="flex items-center justify-end gap-3">
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="is_active" value="1" @checked($component->is_active) class="rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                                        Aktif
                                    </label>
                                    <button type="submit" class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-blue-600 ring-1 ring-inset ring-blue-100 hover:bg-blue-50">Simpan</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada jenis tunjangan atau potongan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
