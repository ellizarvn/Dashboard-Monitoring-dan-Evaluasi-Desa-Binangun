<?php
// ============================================================
// app/Models/Okr1Partisipasi.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Data partisipasi masyarakat per bulan.
 */
class Okr1Partisipasi extends Model
{
    protected $table = 'okr_1_partisipasis';

    protected $fillable = [
        'month', 'year', 'total_warga_wajib_lapor', 'warga_hadir', 'calculated_percentage',
    ];

    protected function casts(): array
    {
        return [
            'calculated_percentage' => 'decimal:2',
        ];
    }
}
