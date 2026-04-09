<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PositionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position',
        'division',
        'role',
        'start_date',
        'end_date',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    // ── Relationships ──────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessors ──────────────────────────────────

    /**
     * Check if this is the current/active position (no end_date).
     */
    public function getIsCurrentAttribute(): bool
    {
        return is_null($this->end_date);
    }

    /**
     * Format the period as human readable string.
     * e.g. "Jan 2025 - Mar 2025" or "Jun 2025 - Sekarang"
     */
    public function getPeriodLabelAttribute(): string
    {
        $start = $this->start_date->translatedFormat('M Y');
        $end = $this->is_current ? 'Sekarang' : $this->end_date->translatedFormat('M Y');
        return "{$start} - {$end}";
    }

    /**
     * Duration in months.
     */
    public function getDurationAttribute(): string
    {
        $end = $this->end_date ?? now();
        $diff = $this->start_date->diff($end);

        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' Tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' Bulan';
        if (empty($parts)) $parts[] = '< 1 Bulan';

        return implode(' ', $parts);
    }

    // ── Scopes ─────────────────────────────────────

    public function scopeCurrent($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('start_date');
    }
}
