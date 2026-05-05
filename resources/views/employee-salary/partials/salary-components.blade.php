@php
    $selectedComponents = isset($employeeSalary)
        ? $employeeSalary->salaryComponents->keyBy('salary_component_id')
        : collect();
@endphp

<div class="border-t border-gray-200"></div>

<div class="px-4 py-5 sm:p-6 space-y-6">
    <div>
        <h3 class="text-lg font-medium leading-6 text-gray-900">Komponen Gaji Karyawan</h3>
        <p class="mt-1 text-sm text-gray-500">Pilih jenis tunjangan atau potongan yang berlaku khusus untuk karyawan ini.</p>
    </div>

    @if($salaryComponents->count() > 0)
    <div class="space-y-3">
        @foreach($salaryComponents as $component)
        @php
            $selected = $selectedComponents->get($component->id);
            $isChecked = old("components.{$component->id}.enabled", $selected ? '1' : null);
            $amountValue = old("components.{$component->id}.amount", $selected->amount ?? $component->default_amount);
            $percentageValue = old("components.{$component->id}.percentage", $selected->percentage ?? $component->default_percentage);
        @endphp
        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <label class="flex items-start gap-3">
                    <input type="checkbox" name="components[{{ $component->id }}][enabled]" value="1" @checked($isChecked) class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                    <span>
                        <span class="block text-sm font-semibold text-gray-900">{{ $component->name }}</span>
                        <span class="mt-0.5 block text-xs text-gray-500">{{ $component->type_label }} - {{ $component->calculation_type_label }}</span>
                    </span>
                </label>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:w-[640px]">
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Nominal</label>
                        <input type="number" min="0" step="1000" name="components[{{ $component->id }}][amount]" value="{{ $amountValue }}" class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500" {{ $component->calculation_type === 'percentage' ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Persen</label>
                        <input type="number" min="0" max="100" step="0.01" name="components[{{ $component->id }}][percentage]" value="{{ $percentageValue }}" class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500" {{ $component->calculation_type !== 'percentage' ? 'readonly' : '' }}>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Catatan</label>
                        <input type="text" name="components[{{ $component->id }}][notes]" value="{{ old("components.{$component->id}.notes", $selected->notes ?? '') }}" class="mt-1 block w-full rounded-lg border-gray-200 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-500">
        Belum ada jenis tunjangan atau potongan. Tambahkan dulu dari Pengaturan Gaji.
    </div>
    @endif
</div>
