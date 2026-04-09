<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Karyawan yang tergabung di divisi ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'division', 'name');
    }

    /**
     * Ambil daftar divisi untuk dropdown (key=name, value=name).
     */
    public static function dropdown(): array
    {
        return static::orderBy('name')->pluck('name', 'name')->toArray();
    }
}
