<?php

namespace App\Http\Controllers;

use App\Models\LandingPageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingPageSettingController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('view_any_setting')) {
            abort(403, 'Anda tidak memiliki akses untuk melihat pengaturan landing page.');
        }

        $setting = LandingPageSetting::editable();

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        if (!auth()->user()->can('update_setting')) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah pengaturan landing page.');
        }

        $setting = LandingPageSetting::editable();

        $validated = $request->validate([
            'brand_name' => 'required|string|max:100',
            'brand_subtitle' => 'nullable|string|max:100',
            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_logo_image' => 'nullable|boolean',
            'nav_documentation_label' => 'required|string|max:50',
            'nav_documentation_url' => 'required|string|max:255',
            'nav_support_label' => 'required|string|max:50',
            'nav_support_url' => 'required|string|max:255',
            'nav_status_label' => 'required|string|max:50',
            'nav_status_url' => 'required|string|max:255',
            'hero_badge' => 'required|string|max:100',
            'hero_title' => 'required|string|max:160',
            'hero_highlight' => 'nullable|string|max:100',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_primary_button_label' => 'required|string|max:60',
            'hero_secondary_button_label' => 'required|string|max:60',
            'hero_secondary_button_url' => 'required|string|max:255',
            'hero_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'remove_hero_image' => 'nullable|boolean',
            'features_title' => 'required|string|max:120',
            'features_description' => 'nullable|string|max:500',
            'features' => 'nullable|array',
            'features.*.title' => 'nullable|string|max:100',
            'features.*.description' => 'nullable|string|max:220',
            'feature_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_feature_image' => 'nullable|boolean',
            'cta_title' => 'required|string|max:120',
            'cta_description' => 'nullable|string|max:300',
            'cta_primary_button_label' => 'required|string|max:60',
            'cta_secondary_button_label' => 'required|string|max:60',
            'cta_secondary_button_url' => 'required|string|max:255',
            'footer_description' => 'nullable|string|max:350',
            'footer_quick_access_title' => 'required|string|max:80',
            'footer_support_title' => 'required|string|max:80',
            'footer_legal_title' => 'required|string|max:80',
            'footer_links' => 'nullable|array',
            'footer_links.*' => 'nullable|array',
            'footer_links.*.*.label' => 'nullable|string|max:80',
            'footer_links.*.*.url' => 'nullable|string|max:255',
            'copyright_text' => 'required|string|max:160',
        ], $this->validationMessages());

        foreach (['logo_image', 'hero_image', 'feature_image'] as $field) {
            $removeField = 'remove_' . $field;

            if ($request->hasFile($field)) {
                if ($setting->{$field} && Storage::disk('public')->exists($setting->{$field})) {
                    Storage::disk('public')->delete($setting->{$field});
                }

                $validated[$field] = $request->file($field)->store('landing', 'public');
            } elseif ($request->boolean($removeField)) {
                if ($setting->{$field} && Storage::disk('public')->exists($setting->{$field})) {
                    Storage::disk('public')->delete($setting->{$field});
                }

                $validated[$field] = null;
            } else {
                unset($validated[$field]);
            }

            unset($validated[$removeField]);
        }

        $validated['features'] = $this->cleanItems($validated['features'] ?? []);
        $validated['footer_links'] = $this->cleanFooterLinks($validated['footer_links'] ?? []);

        $setting->update($validated);

        return redirect()->route('settings.index')->with('success', 'Pengaturan landing page berhasil diperbarui.');
    }

    private function cleanItems(array $items): array
    {
        return collect($items)
            ->map(fn (array $item) => array_map(fn ($value) => is_string($value) ? trim($value) : $value, $item))
            ->filter(fn (array $item) => collect($item)->filter()->isNotEmpty())
            ->values()
            ->all();
    }

    private function validationMessages(): array
    {
        return [
            'logo_image.uploaded' => 'Logo gagal diunggah. Ukuran file kemungkinan melebihi batas upload server atau maksimal 4MB.',
            'logo_image.image' => 'Logo harus berupa file gambar.',
            'logo_image.mimes' => 'Logo harus berformat JPG, PNG, atau WEBP.',
            'logo_image.max' => 'Logo maksimal 4MB.',
            'hero_image.uploaded' => 'Foto hero gagal diunggah. Ukuran file kemungkinan melebihi batas upload server atau maksimal 4MB.',
            'hero_image.image' => 'Foto hero harus berupa file gambar.',
            'hero_image.mimes' => 'Foto hero harus berformat JPG, PNG, atau WEBP.',
            'hero_image.max' => 'Foto hero maksimal 4MB.',
            'feature_image.uploaded' => 'Foto preview fitur gagal diunggah. Ukuran file kemungkinan melebihi batas upload server atau maksimal 4MB.',
            'feature_image.image' => 'Foto preview fitur harus berupa file gambar.',
            'feature_image.mimes' => 'Foto preview fitur harus berformat JPG, PNG, atau WEBP.',
            'feature_image.max' => 'Foto preview fitur maksimal 4MB.',
        ];
    }

    private function cleanFooterLinks(array $footerLinks): array
    {
        $groups = ['quick_access', 'support', 'legal'];
        $cleaned = [];

        foreach ($groups as $group) {
            $cleaned[$group] = $this->cleanItems($footerLinks[$group] ?? []);
        }

        return $cleaned;
    }
}
