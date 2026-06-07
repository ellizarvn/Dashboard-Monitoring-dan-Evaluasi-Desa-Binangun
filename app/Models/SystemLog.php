<?php
// ============================================================
// app/Models/SystemLog.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Audit trail seluruh aktivitas sistem.
 * Tidak memiliki updated_at karena log bersifat immutable.
 */
class SystemLog extends Model
{
    protected $table = 'system_logs';

    // Karena tabel hanya punya created_at
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'activity_type', 'module', 'description',
        'ip_address', 'status', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Warna badge berdasarkan tipe aktivitas.
     */
    public function getActivityBadgeColorAttribute(): string
    {
        return match ($this->activity_type) {
            'LOGIN'                => 'blue',
            'LOGOUT'               => 'gray',
            'UPDATE DATA'          => 'yellow',
            'CREATE DATA'          => 'green',
            'DELETE DATA'          => 'red',
            'UPLOAD FILE'          => 'purple',
            'EXPORT DATA'          => 'indigo',
            'UNAUTHORIZED ACCESS'  => 'red',
            default                => 'gray',
        };
    }

    /**
     * Warna badge status BERHASIL/GAGAL.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return $this->status === 'BERHASIL' ? 'green' : 'red';
    }
}
