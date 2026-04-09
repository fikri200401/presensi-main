<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'url',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Icon & warna berdasarkan type */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'leave_request'  => 'document',
            'leave_approved' => 'check',
            'leave_rejected' => 'x',
            'attendance_alert' => 'exclamation',
            default          => 'info',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'leave_request'    => 'blue',
            'leave_approved'   => 'green',
            'leave_rejected'   => 'red',
            'attendance_alert' => 'yellow',
            default            => 'gray',
        };
    }
}
