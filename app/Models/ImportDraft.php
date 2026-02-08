<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportDraft extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'preview_data',
        'failed_data',
        'errors',
        'total_rows',
        'success_rows',
        'failed_rows',
        'status',
    ];

    protected $casts = [
        'preview_data' => 'array',
        'failed_data' => 'array',
        'errors' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
