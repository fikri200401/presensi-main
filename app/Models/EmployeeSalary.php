<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gaji_pokok_bulanan',
        'gaji_per_jam',
        'gaji_per_hari',
        'tipe_karyawan',
        'metode_perhitungan',
        'tunjangan_transport',
        'tunjangan_makan',
        'tunjangan_lainnya',
        'keterangan_tunjangan',
        'potongan_bpjs_kesehatan',
        'potongan_bpjs_ketenagakerjaan',
        'potongan_pph21',
        'potongan_lainnya',
        'keterangan_potongan',
        'is_active',
        'berlaku_dari',
    ];

    protected $casts = [
        'gaji_pokok_bulanan' => 'decimal:2',
        'gaji_per_jam' => 'decimal:2',
        'gaji_per_hari' => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_makan' => 'decimal:2',
        'tunjangan_lainnya' => 'decimal:2',
        'potongan_bpjs_kesehatan' => 'decimal:2',
        'potongan_bpjs_ketenagakerjaan' => 'decimal:2',
        'potongan_pph21' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'is_active' => 'boolean',
        'berlaku_dari' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Auto calculate gaji per jam dan per hari
     */
    public function calculateRates()
    {
        $settings = SalarySetting::getSettings();
        
        // Hitung gaji per jam menggunakan formula KEP. 102/MEN/VI/2004
        $this->gaji_per_jam = (1 / $settings->total_jam_per_bulan) * $this->gaji_pokok_bulanan;
        
        // Hitung gaji per hari
        $this->gaji_per_hari = $this->gaji_pokok_bulanan / $settings->hari_kerja_per_bulan;
        
        $this->save();
        
        return $this;
    }

    /**
     * Get total tunjangan
     */
    public function getTotalTunjanganAttribute()
    {
        return $this->tunjangan_transport + 
               $this->tunjangan_makan + 
               $this->tunjangan_lainnya;
    }

    /**
     * Get total potongan tetap
     */
    public function getTotalPotonganAttribute()
    {
        return $this->potongan_bpjs_kesehatan + 
               $this->potongan_bpjs_ketenagakerjaan + 
               $this->potongan_pph21 + 
               $this->potongan_lainnya;
    }
}
