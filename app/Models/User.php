<?php
// ============================================================
// app/Models/User.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model User dengan RBAC, 2FA toggle, dan relasi ke seluruh modul.
 *
 * @property int    $id
 * @property string $name
 * @property string $email
 * @property string $role  admin|kepala_desa|tim_monitoring|bpd
 * @property string|null $nip_administrator
 * @property bool   $two_factor_enabled
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'photo',
        'role', 'nip_administrator', 'two_factor_enabled', 'two_factor_secret',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'two_factor_enabled'  => 'boolean',
        ];
    }

    // ---- Relasi ----

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'author_id');
    }

    public function systemLogs(): HasMany
    {
        return $this->hasMany(SystemLog::class, 'user_id');
    }

    // ---- Helper Role ----

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKepala(): bool
    {
        return $this->role === 'kepala_desa';
    }

    public function isBpd(): bool
    {
        return $this->role === 'bpd';
    }

    public function isMonitoring(): bool
    {
        return $this->role === 'tim_monitoring';
    }

    /** Cek apakah user bisa mengubah data (bukan read-only) */
    public function canMutateData(): bool
    {
        return in_array($this->role, ['admin', 'tim_monitoring']);
    }

    /** Dapatkan inisial untuk avatar */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }

    /** Label role dalam Bahasa Indonesia */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'          => 'Administrator',
            'kepala_desa'    => 'Kepala Desa',
            'tim_monitoring' => 'Tim Monitoring',
            'bpd'            => 'BPD',
            default          => 'Pengguna',
        };
    }
}
