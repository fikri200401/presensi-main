<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Attendance;
use App\Models\Office;
use App\Models\User;

class LandingPageSetting extends Model
{
    protected $fillable = [
        'brand_name',
        'brand_subtitle',
        'logo_image',
        'nav_documentation_label',
        'nav_documentation_url',
        'nav_support_label',
        'nav_support_url',
        'nav_status_label',
        'nav_status_url',
        'hero_badge',
        'hero_title',
        'hero_highlight',
        'hero_subtitle',
        'hero_primary_button_label',
        'hero_secondary_button_label',
        'hero_secondary_button_url',
        'hero_image',
        'stats',
        'features_title',
        'features_description',
        'features',
        'feature_image',
        'cta_title',
        'cta_description',
        'cta_primary_button_label',
        'cta_secondary_button_label',
        'cta_secondary_button_url',
        'footer_description',
        'footer_quick_access_title',
        'footer_support_title',
        'footer_legal_title',
        'footer_links',
        'copyright_text',
    ];

    protected $casts = [
        'stats' => 'array',
        'features' => 'array',
        'footer_links' => 'array',
    ];

    public static function current(): self
    {
        if (!Schema::hasTable('landing_page_settings')) {
            return new self(self::defaultValues());
        }

        return self::query()->first() ?? new self(self::defaultValues());
    }

    public static function editable(): self
    {
        return self::query()->first() ?? self::query()->create(self::defaultValues());
    }

    public static function defaultValues(): array
    {
        return [
            'brand_name' => 'HRIS Portal',
            'brand_subtitle' => 'Management System',
            'nav_documentation_label' => 'Dokumentasi',
            'nav_documentation_url' => '/info/dokumentasi',
            'nav_support_label' => 'IT Support',
            'nav_support_url' => '/info/it-support',
            'nav_status_label' => 'Status Sistem',
            'nav_status_url' => '/info/status-sistem',
            'hero_badge' => 'PORTAL INTERNAL PERUSAHAAN',
            'hero_title' => 'Selamat Datang di HRIS Portal Terpadu',
            'hero_highlight' => 'HRIS Portal',
            'hero_subtitle' => 'Kelola data karyawan, absensi, cuti, dan penggajian Anda dalam satu platform yang aman, efisien, dan transparan. Dirancang khusus untuk mendukung produktivitas setiap anggota tim.',
            'hero_primary_button_label' => 'Masuk ke Dashboard',
            'hero_secondary_button_label' => 'Panduan Pengguna',
            'hero_secondary_button_url' => '/info/panduan',
            'stats' => self::defaultStats(),
            'features_title' => 'Navigasi Fitur Portal',
            'features_description' => 'Akses semua kebutuhan administrasi Anda dengan mudah. Portal ini dirancang untuk menyederhanakan proses birokrasi internal perusahaan.',
            'features' => self::defaultFeatures(),
            'cta_title' => 'Siap Memulai Hari Kerja Anda?',
            'cta_description' => 'Akses Dashboard Karyawan sekarang untuk melihat tugas hari ini dan melakukan absensi kehadiran.',
            'cta_primary_button_label' => 'Portal Login',
            'cta_secondary_button_label' => 'Kontak Admin',
            'cta_secondary_button_url' => '/info/it-support',
            'footer_description' => 'Integrated Human Resource Information System for modern enterprise management. Secure, efficient, and transparent.',
            'footer_quick_access_title' => 'Quick Access',
            'footer_support_title' => 'Support',
            'footer_legal_title' => 'Legal',
            'footer_links' => self::defaultFooterLinks(),
            'copyright_text' => 'HRIS Internal Portal. All rights reserved.',
        ];
    }

    public static function defaultStats(): array
    {
        return [
            ['label' => 'Total Pegawai', 'value' => '1,240', 'badge' => 'Terverifikasi', 'description' => 'Pegawai aktif di seluruh departemen'],
            ['label' => 'Kehadiran Hari Ini', 'value' => '94.2%', 'badge' => 'Terverifikasi', 'description' => 'Berdasarkan data scan wajah terbaru'],
            ['label' => 'Proyek Aktif', 'value' => '42', 'badge' => 'Terverifikasi', 'description' => 'Penempatan di berbagai lokasi strategis'],
        ];
    }

    public static function automaticStats(): array
    {
        $today = now()->toDateString();
        $totalUsers = Schema::hasTable('users') ? User::query()->count() : 0;
        $todayAttendance = Schema::hasTable('attendances')
            ? Attendance::query()
                ->where(function ($query) use ($today) {
                    $query->whereDate('date', $today)
                        ->orWhere(function ($fallback) use ($today) {
                            $fallback->whereNull('date')
                                ->whereDate('created_at', $today);
                        });
                })
                ->distinct('user_id')
                ->count('user_id')
            : 0;
        $attendanceRate = $totalUsers > 0 ? round(($todayAttendance / $totalUsers) * 100, 1) : 0;
        $attendanceValue = $attendanceRate == (int) $attendanceRate
            ? (int) $attendanceRate . '%'
            : $attendanceRate . '%';
        $officeCount = Schema::hasTable('offices') ? Office::query()->count() : 0;

        return [
            [
                'label' => 'Total Pegawai',
                'value' => number_format($totalUsers),
                'badge' => 'Data Sistem',
                'description' => 'Pegawai terdaftar di sistem',
            ],
            [
                'label' => 'Kehadiran Hari Ini',
                'value' => $attendanceValue,
                'badge' => 'Hari Ini',
                'description' => "{$todayAttendance} dari {$totalUsers} pegawai sudah presensi hari ini",
            ],
            [
                'label' => 'Lokasi Kantor',
                'value' => number_format($officeCount),
                'badge' => 'Aktif',
                'description' => 'Lokasi kantor terdaftar di sistem',
            ],
        ];
    }

    public static function defaultFeatures(): array
    {
        return [
            ['title' => 'Absensi & Kehadiran', 'description' => 'Pantau riwayat kehadiran dan lakukan clock-in/out melalui sistem maps yang presisi.'],
            ['title' => 'Manajemen Cuti', 'description' => 'Ajukan cuti atau izin dengan persetujuan digital cepat. Pantau sisa kuota cuti tahunan Anda secara real-time.'],
            ['title' => 'Profil Karyawan', 'description' => 'Kelola data pribadi, informasi kontrak, dan riwayat promosi Anda dalam satu tempat yang aman.'],
        ];
    }

    public static function defaultFooterLinks(): array
    {
        return [
            'quick_access' => [
                ['label' => 'Employee Directory', 'url' => '/login'],
                ['label' => 'Knowledge Base', 'url' => '/info/dokumentasi'],
                ['label' => 'System Status', 'url' => '/info/status-sistem'],
            ],
            'support' => [
                ['label' => 'Contact IT Helpdesk', 'url' => '/info/it-support'],
                ['label' => 'HR Policy Manual', 'url' => '/info/dokumentasi'],
                ['label' => 'Submit a Ticket', 'url' => '/info/it-support'],
            ],
            'legal' => [
                ['label' => 'Terms of Use', 'url' => '/info/privacy-policy'],
                ['label' => 'Data Protection', 'url' => '/info/privacy-policy'],
                ['label' => 'Compliance', 'url' => '/info/privacy-policy'],
            ],
        ];
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->storageUrl($this->logo_image);
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->storageUrl($this->hero_image);
    }

    public function getFeatureImageUrlAttribute(): ?string
    {
        return $this->storageUrl($this->feature_image);
    }

    private function storageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::url($path);
    }
}
