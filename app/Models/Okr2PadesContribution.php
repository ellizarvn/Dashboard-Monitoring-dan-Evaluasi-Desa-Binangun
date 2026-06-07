<?php
// ============================================================
// app/Models/Okr2PadesContribution.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Setoran kontribusi PADes dari unit BUMDes.
 * Wajib minimal 25% dari laba bersih bulanan (divalidasi di OkrService).
 */
class Okr2PadesContribution extends Model
{
    protected $table = 'okr_2_pades_contributions';

    protected $fillable = [
        'bumdes_unit_id', 'period_year_month', 'nominal_setoran', 'file_proof_path',
    ];

    protected function casts(): array
    {
        return [
            'nominal_setoran' => 'decimal:2',
        ];
    }

    public function bumdesUnit(): BelongsTo
    {
        return $this->belongsTo(Okr2BumdesUnit::class, 'bumdes_unit_id');
    }
}
