<?php
// ============================================================
// app/Models/VillageProgram.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Program-program kegiatan desa dengan tracking progress dan status.
 */
class VillageProgram extends Model
{
    protected $table = 'village_programs';

    protected $fillable = [
        'name', 'linked_okr', 'status', 'progress_percentage', 'description',
    ];

    protected function casts(): array
    {
        return [
            'progress_percentage' => 'integer',
        ];
    }

    /**
     * Warna badge berdasarkan status program.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'AKTIF'   => 'green',
            'PENDING' => 'yellow',
            'SELESAI' => 'blue',
            default   => 'gray',
        };
    }

    /**
     * Warna progress bar berdasarkan persentase.
     */
    public function getProgressColorAttribute(): string
    {
        if ($this->progress_percentage >= 80) return '#1A362B';
        if ($this->progress_percentage >= 50) return '#87A996';
        return '#f59e0b';
    }
}
