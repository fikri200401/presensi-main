<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'periode',
        'bulan',
        'tahun',
        'total_hari_kerja',
        'total_hari_hadir',
        'total_jam_kerja',
        'total_jam_hadir',
        'total_terlambat',
        'gaji_pokok',
        'gaji_per_hari',
        'gaji_per_jam',
        'tunjangan_transport',
        'tunjangan_makan',
        'tunjangan_lainnya',
        'total_tunjangan',
        'potongan_bpjs_kesehatan',
        'potongan_bpjs_ketenagakerjaan',
        'potongan_pph21',
        'potongan_keterlambatan',
        'potongan_lainnya',
        'total_potongan',
        'gaji_kotor',
        'gaji_bersih',
        'status',
        'approved_by',
        'approved_at',
        'catatan',
    ];

    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'gaji_per_hari' => 'decimal:2',
        'gaji_per_jam' => 'decimal:2',
        'tunjangan_transport' => 'decimal:2',
        'tunjangan_makan' => 'decimal:2',
        'tunjangan_lainnya' => 'decimal:2',
        'total_tunjangan' => 'decimal:2',
        'potongan_bpjs_kesehatan' => 'decimal:2',
        'potongan_bpjs_ketenagakerjaan' => 'decimal:2',
        'potongan_pph21' => 'decimal:2',
        'potongan_keterlambatan' => 'decimal:2',
        'potongan_lainnya' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'gaji_kotor' => 'decimal:2',
        'gaji_bersih' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get periode name (e.g., "Januari 2026")
     */
    public function getPeriodeNameAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulan[$this->bulan] . ' ' . $this->tahun;
    }
}
