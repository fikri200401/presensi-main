@extends('layouts.app')

@section('title', 'Pengaturan Landing Page')
@section('page-title', 'Pengaturan')

@section('content')
@php
    $stats = \App\Models\LandingPageSetting::automaticStats();
    $features = old('features', $setting->features ?: \App\Models\LandingPageSetting::defaultFeatures());
    $footerLinks = old('footer_links', $setting->footer_links ?: \App\Models\LandingPageSetting::defaultFooterLinks());
    $footerGroups = [
        'quick_access' => ['title' => 'footer_quick_access_title', 'label' => 'Quick Access'],
        'support' => ['title' => 'footer_support_title', 'label' => 'Support'],
        'legal' => ['title' => 'footer_legal_title', 'label' => 'Legal'],
    ];
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="mb-2 flex items-center gap-1.5 text-sm text-gray-400">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-600 transition-colors">Admin Portal</a>
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                <span class="text-gray-600 font-medium">Pengaturan</span>
            </nav>
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Landing Page</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola teks, foto, tautan, statistik, fitur, CTA, dan footer halaman depan.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" /></svg>
            Lihat Landing Page
        </a>
    </div>

    @if($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
        Ada data yang belum valid. Periksa kembali form di bawah.
    </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Brand & Navigasi</h3>
                <p class="text-sm text-gray-500 mt-0.5">Informasi logo, nama portal, dan menu publik di navbar.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="space-y-4 lg:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Brand</label>
                            <input type="text" name="brand_name" value="{{ old('brand_name', $setting->brand_name) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('brand_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Subtitle Brand</label>
                            <input type="text" name="brand_subtitle" value="{{ old('brand_subtitle', $setting->brand_subtitle) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('brand_subtitle')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Menu Dokumentasi</label>
                            <input type="text" name="nav_documentation_label" value="{{ old('nav_documentation_label', $setting->nav_documentation_label) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <input type="text" name="nav_documentation_url" value="{{ old('nav_documentation_url', $setting->nav_documentation_url) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="/info/dokumentasi">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Menu Support</label>
                            <input type="text" name="nav_support_label" value="{{ old('nav_support_label', $setting->nav_support_label) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <input type="text" name="nav_support_url" value="{{ old('nav_support_url', $setting->nav_support_url) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="/info/it-support">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Menu Status</label>
                            <input type="text" name="nav_status_label" value="{{ old('nav_status_label', $setting->nav_status_label) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <input type="text" name="nav_status_url" value="{{ old('nav_status_url', $setting->nav_status_url) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="/info/status-sistem">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Logo Landing Page</label>
                    <div class="mt-1 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        @if($setting->logo_url)
                            <img src="{{ $setting->logo_url }}" alt="{{ $setting->brand_name }}" class="mb-3 h-20 w-20 rounded-xl object-cover bg-white border border-gray-200">
                        @else
                            <div class="mb-3 h-20 w-20 rounded-xl bg-blue-600 flex items-center justify-center">
                                <svg class="h-9 w-9 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                            </div>
                        @endif
                        <input type="file" name="logo_image" accept="image/jpg,image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        @if($setting->logo_image)
                            <label class="mt-3 flex items-center gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                                <input type="checkbox" name="remove_logo_image" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                Hapus logo saat disimpan
                            </label>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">JPG, PNG, atau WEBP. Maksimal 4MB.</p>
                        @error('logo_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Hero Section</h3>
                <p class="text-sm text-gray-500 mt-0.5">Headline, deskripsi, tombol utama, dan foto utama halaman depan.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Badge</label>
                            <input type="text" name="hero_badge" value="{{ old('hero_badge', $setting->hero_badge) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teks Highlight</label>
                            <input type="text" name="hero_highlight" value="{{ old('hero_highlight', $setting->hero_highlight) }}" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul Hero</label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $setting->hero_title) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('hero_title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Hero</label>
                        <textarea name="hero_subtitle" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('hero_subtitle', $setting->hero_subtitle) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tombol Utama</label>
                            <input type="text" name="hero_primary_button_label" value="{{ old('hero_primary_button_label', $setting->hero_primary_button_label) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tombol Kedua</label>
                            <input type="text" name="hero_secondary_button_label" value="{{ old('hero_secondary_button_label', $setting->hero_secondary_button_label) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL Tombol Kedua</label>
                            <input type="text" name="hero_secondary_button_url" value="{{ old('hero_secondary_button_url', $setting->hero_secondary_button_url) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Foto Hero</label>
                    <div class="mt-1 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        @if($setting->hero_image_url)
                            <img src="{{ $setting->hero_image_url }}" alt="Foto hero" class="mb-3 aspect-[4/3] w-full rounded-xl object-cover bg-white border border-gray-200">
                        @else
                            <div class="mb-3 aspect-[4/3] w-full rounded-xl bg-gradient-to-br from-blue-50 to-gray-100 border border-gray-200 flex items-center justify-center text-sm text-gray-400">Belum ada foto</div>
                        @endif
                        <input type="file" name="hero_image" accept="image/jpg,image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        @if($setting->hero_image)
                            <label class="mt-3 flex items-center gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                                <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                Hapus foto hero saat disimpan
                            </label>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">Foto ini akan muncul sebagai latar hero landing page.</p>
                        @error('hero_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Statistik Otomatis</h3>
                <p class="text-sm text-gray-500 mt-0.5">Kartu angka di landing page mengikuti data sistem dan tidak perlu diisi manual.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                @foreach($stats as $stat)
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-gray-900">{{ $stat['label'] }}</p>
                            <span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-600">{{ $stat['badge'] }}</span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                        <p class="mt-2 text-sm text-gray-500">{{ $stat['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">Fitur & Preview</h3>
                <p class="text-sm text-gray-500 mt-0.5">Judul area fitur, daftar fitur, dan foto preview di sampingnya.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul Section Fitur</label>
                        <input type="text" name="features_title" value="{{ old('features_title', $setting->features_title) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Section Fitur</label>
                        <textarea name="features_description" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('features_description', $setting->features_description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @for($i = 0; $i < max(3, count($features)); $i++)
                            @php($feature = $features[$i] ?? ['title' => '', 'description' => ''])
                            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 space-y-3">
                                <h4 class="text-sm font-semibold text-gray-900">Fitur {{ $i + 1 }}</h4>
                                <input type="text" name="features[{{ $i }}][title]" value="{{ $feature['title'] ?? '' }}" placeholder="Judul fitur" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <textarea name="features[{{ $i }}][description]" rows="4" placeholder="Deskripsi fitur" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ $feature['description'] ?? '' }}</textarea>
                            </div>
                        @endfor
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Foto Preview Fitur</label>
                    <div class="mt-1 rounded-2xl border border-gray-100 bg-gray-50 p-4">
                        @if($setting->feature_image_url)
                            <img src="{{ $setting->feature_image_url }}" alt="Foto preview fitur" class="mb-3 aspect-[4/3] w-full rounded-xl object-cover bg-white border border-gray-200">
                        @else
                            <div class="mb-3 aspect-[4/3] w-full rounded-xl bg-gradient-to-br from-blue-50 to-gray-100 border border-gray-200 flex items-center justify-center text-sm text-gray-400">Belum ada foto</div>
                        @endif
                        <input type="file" name="feature_image" accept="image/jpg,image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-xl file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        @if($setting->feature_image)
                            <label class="mt-3 flex items-center gap-2 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                                <input type="checkbox" name="remove_feature_image" value="1" class="rounded border-red-300 text-red-600 focus:ring-red-500">
                                Hapus foto preview saat disimpan
                            </label>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">Opsional. Jika kosong, preview mockup bawaan tetap dipakai.</p>
                        @error('feature_image')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="mb-5">
                <h3 class="text-base font-semibold text-gray-900">CTA & Footer</h3>
                <p class="text-sm text-gray-500 mt-0.5">Teks ajakan, tombol bawah, deskripsi footer, dan link footer.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Judul CTA</label>
                        <input type="text" name="cta_title" value="{{ old('cta_title', $setting->cta_title) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi CTA</label>
                        <textarea name="cta_description" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('cta_description', $setting->cta_description) }}</textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tombol Utama</label>
                            <input type="text" name="cta_primary_button_label" value="{{ old('cta_primary_button_label', $setting->cta_primary_button_label) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tombol Kedua</label>
                            <input type="text" name="cta_secondary_button_label" value="{{ old('cta_secondary_button_label', $setting->cta_secondary_button_label) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL Tombol Kedua</label>
                            <input type="text" name="cta_secondary_button_url" value="{{ old('cta_secondary_button_url', $setting->cta_secondary_button_url) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deskripsi Footer</label>
                        <textarea name="footer_description" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('footer_description', $setting->footer_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Copyright</label>
                        <input type="text" name="copyright_text" value="{{ old('copyright_text', $setting->copyright_text) }}" required class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
                @foreach($footerGroups as $group => $meta)
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4 space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Judul {{ $meta['label'] }}</label>
                        <input type="text" name="{{ $meta['title'] }}" value="{{ old($meta['title'], $setting->{$meta['title']}) }}" required class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">

                        @for($i = 0; $i < max(3, count($footerLinks[$group] ?? [])); $i++)
                            @php($link = $footerLinks[$group][$i] ?? ['label' => '', 'url' => ''])
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <input type="text" name="footer_links[{{ $group }}][{{ $i }}][label]" value="{{ $link['label'] ?? '' }}" placeholder="Label link" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <input type="text" name="footer_links[{{ $group }}][{{ $i }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="/info/..." class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        @endfor
                    </div>
                @endforeach
            </div>
        </section>

        <div class="sticky bottom-0 z-10 -mx-4 border-t border-gray-200 bg-white/95 px-4 py-4 backdrop-blur sm:mx-0 sm:rounded-2xl sm:border sm:border-gray-100">
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
