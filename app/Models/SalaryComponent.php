<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryComponent extends Model
{
    use HasFactory;

    public const TYPE_ALLOWANCE = 'allowance';
    public const TYPE_DEDUCTION = 'deduction';

    public const CALCULATION_FIXED = 'fixed';
    public const CALCULATION_PERCENTAGE = 'percentage';
    public const CALCULATION_MANUAL = 'manual';

    protected $fillable = [
        'name',
        'type',
        'calculation_type',
        'default_amount',
        'default_percentage',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'default_amount' => 'decimal:2',
        'default_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function employeeSalaryComponents(): HasMany
    {
        return $this->hasMany(EmployeeSalaryComponent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_ALLOWANCE ? 'Tunjangan' : 'Potongan';
    }

    public function getCalculationTypeLabelAttribute(): string
    {
        return match ($this->calculation_type) {
            self::CALCULATION_PERCENTAGE => 'Persentase',
            self::CALCULATION_MANUAL => 'Manual',
            default => 'Nominal Tetap',
        };
    }
}
