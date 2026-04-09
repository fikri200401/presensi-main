<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
        'division',
        'phone',
        'address',
        'position',
        'nip',
        'birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function employeeSalary(): HasOne
    {
        return $this->hasOne(EmployeeSalary::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function positionHistories(): HasMany
    {
        return $this->hasMany(PositionHistory::class)->orderByDesc('start_date');
    }

    public function currentPosition(): HasOne
    {
        return $this->hasOne(PositionHistory::class)->whereNull('end_date')->latestOfMany('start_date');
    }

    /**
     * Mapping role → label jabatan.
     * Jika role terkait divisi, gabungkan nama divisi secara dinamis.
     */
    public const ROLE_POSITION_MAP = [
        'super_admin'    => 'Super Admin',
        'admin'          => 'Admin :division',
        'direksi'        => 'Direktur',
        'kepala_divisi'  => 'Kepala Divisi :division',
        'employee'       => 'Staff :division',
    ];

    /**
     * Generate label jabatan dari role dan divisi.
     *
     * @param  string|null  $role
     * @param  string|null  $division
     * @return string
     */
    public static function generatePosition(?string $role, ?string $division = null): string
    {
        if (!$role || !isset(self::ROLE_POSITION_MAP[$role])) {
            return '';
        }

        $template = self::ROLE_POSITION_MAP[$role];

        return str_replace(':division', $division ?? '', $template);
    }

    /**
     * Jabatan saat ini berdasarkan role aktif + divisi.
     */
    public function getPositionLabelAttribute(): string
    {
        $role = $this->getRoleNames()->first();
        return self::generatePosition($role, $this->division);
    }
}
