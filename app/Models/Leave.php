<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'note',
        'approved_by_kadiv',
        'approved_at_kadiv',
        'note_kadiv',
        'approved_by_hr',
        'approved_at_hr',
        'note_hr',
        'approved_by_direksi',
        'approved_at_direksi',
        'note_direksi',
    ];

    protected $casts = [
        'approved_at_kadiv' => 'datetime',
        'approved_at_hr' => 'datetime',
        'approved_at_direksi' => 'datetime',
    ];

    /**
     * Status flow:
     * pending → approved_kadiv → approved_hr → approved (final by direksi)
     * At any stage it can become: rejected_kadiv / rejected_hr / rejected_direksi
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED_KADIV = 'approved_kadiv';
    const STATUS_APPROVED_HR = 'approved_hr';
    const STATUS_APPROVED = 'approved';           // final, approved by direksi
    const STATUS_REJECTED_KADIV = 'rejected_kadiv';
    const STATUS_REJECTED_HR = 'rejected_hr';
    const STATUS_REJECTED_DIREKSI = 'rejected_direksi';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approverKadiv(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_kadiv');
    }

    public function approverHr(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_hr');
    }

    public function approverDireksi(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_direksi');
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Approval Kadiv',
            self::STATUS_APPROVED_KADIV => 'Menunggu Approval HR',
            self::STATUS_APPROVED_HR => 'Menunggu Approval Direksi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED_KADIV => 'Ditolak Kadiv',
            self::STATUS_REJECTED_HR => 'Ditolak HR',
            self::STATUS_REJECTED_DIREKSI => 'Ditolak Direksi',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status color class for badge
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-50 text-amber-700',
            self::STATUS_APPROVED_KADIV => 'bg-blue-50 text-blue-700',
            self::STATUS_APPROVED_HR => 'bg-indigo-50 text-indigo-700',
            self::STATUS_APPROVED => 'bg-green-50 text-green-700',
            self::STATUS_REJECTED_KADIV, self::STATUS_REJECTED_HR, self::STATUS_REJECTED_DIREKSI => 'bg-red-50 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    /**
     * Check if the leave is in a rejected state
     */
    public function isRejected(): bool
    {
        return in_array($this->status, [
            self::STATUS_REJECTED_KADIV,
            self::STATUS_REJECTED_HR,
            self::STATUS_REJECTED_DIREKSI,
        ]);
    }

    /**
     * Get rejection note from whichever level rejected
     */
    public function getRejectionNoteAttribute(): ?string
    {
        return match ($this->status) {
            self::STATUS_REJECTED_KADIV => $this->note_kadiv,
            self::STATUS_REJECTED_HR => $this->note_hr,
            self::STATUS_REJECTED_DIREKSI => $this->note_direksi,
            default => $this->note,
        };
    }
}
