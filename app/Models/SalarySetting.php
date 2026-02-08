<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalarySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'jam_kerja_per_hari',
        'hari_kerja_per_minggu',
        'hari_kerja_per_bulan',
        'total_jam_per_bulan',
        'metode_perhitungan_default',
        'tunjangan_transport_default',
        'tunjangan_makan_default',
        'potongan_bpjs_kesehatan_persen',
        'potongan_bpjs_ketenagakerjaan_persen',
        'catatan',
    ];

    protected $casts = [
        'tunjangan_transport_default' => 'decimal:2',
        'tunjangan_makan_default' => 'decimal:2',
        'potongan_bpjs_kesehatan_persen' => 'decimal:2',
        'potongan_bpjs_ketenagakerjaan_persen' => 'decimal:2',
    ];

    /**
     * Get global salary settings (singleton pattern)
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            $settings = self::create([
                'jam_kerja_per_hari' => 8,
                'hari_kerja_per_minggu' => 5,
                'hari_kerja_per_bulan' => 21,
                'total_jam_per_bulan' => 173,
                'metode_perhitungan_default' => 'bulanan',
                'tunjangan_transport_default' => 0,
                'tunjangan_makan_default' => 0,
                'potongan_bpjs_kesehatan_persen' => 1,
                'potongan_bpjs_ketenagakerjaan_persen' => 2,
            ]);
        }
        
        return $settings;
    }
}
