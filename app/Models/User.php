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
        'role', 'jabatan', 'is_active', 'nip_administrator', 
        'two_factor_enabled', 'two_factor_secret',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret'];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'two_factor_enabled'  => 'boolean',
            'is_active'           => 'boolean',
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

    // ---- Helper Role & Accessors ----

    public function isSuperAdmin(): bool
    {
        return $this->getRawOriginal('role') === 'super_admin';
    }

    public function getDbRoleAttribute(): string
    {
        return $this->getRawOriginal('role') ?? 'admin';
    }

    /**
     * Override getter role untuk kompatibilitas ke belakang (RBAC lama).
     * Memetakan admin+jabatan ke role lama (admin, kepala_desa, tim_monitoring, bpd).
     */
    public function getRoleAttribute($value): string
    {
        if ($value === 'super_admin') {
            return 'super_admin';
        }
        return match ($this->jabatan) {
            'Administrator'  => 'admin',
            'Kepala Desa'    => 'kepala_desa',
            'Tim Monitoring' => 'tim_monitoring',
            'BPD'            => 'bpd',
            default          => 'tim_monitoring',
        };
    }

    public function isAdmin(): bool
    {
        return $this->isSuperAdmin() || ($this->getRawOriginal('role') === 'admin' && $this->jabatan === 'Administrator');
    }

    public function isKepala(): bool
    {
        return $this->isSuperAdmin() || ($this->getRawOriginal('role') === 'admin' && $this->jabatan === 'Kepala Desa');
    }

    public function isBpd(): bool
    {
        return $this->isSuperAdmin() || ($this->getRawOriginal('role') === 'admin' && $this->jabatan === 'BPD');
    }

    public function isMonitoring(): bool
    {
        return $this->isSuperAdmin() || ($this->getRawOriginal('role') === 'admin' && $this->jabatan === 'Tim Monitoring');
    }

    /** Cek apakah user bisa mengubah data (bukan read-only) */
    public function canMutateData(): bool
    {
        return $this->isSuperAdmin() || ($this->getRawOriginal('role') === 'admin' && in_array($this->jabatan, ['Administrator', 'Tim Monitoring']));
    }

    /** Dapatkan inisial untuk avatar */
    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->name);
        return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }

    /** Label role/jabatan dalam Bahasa Indonesia */
    public function getRoleLabelAttribute(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Super Admin';
        }
        return $this->jabatan ?? 'Pengguna';
    }
}
