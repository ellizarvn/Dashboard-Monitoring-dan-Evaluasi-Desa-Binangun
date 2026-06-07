<?php
// ============================================================
// app/Models/Okr3SdmCapacity.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Data kapasitas SDM perangkat desa per periode.
 */
class Okr3SdmCapacity extends Model
{
    protected $table = 'okr_3_sdm_capacities';

    protected $fillable = [
        'period_date', 'total_perangkat', 'total_staf_terlatih',
        'avg_competency_score', 'keaktifan_kinerja_persen',
    ];

    protected function casts(): array
    {
        return [
            'period_date'            => 'date',
            'avg_competency_score'   => 'decimal:1',
            'keaktifan_kinerja_persen' => 'decimal:2',
        ];
    }

    /** Persentase staf terlatih dari total perangkat */
    public function getPersentaseStafTerlatihAttribute(): float
    {
        if ($this->total_perangkat === 0) return 0.0;
        return round(($this->total_staf_terlatih / $this->total_perangkat) * 100, 2);
    }
}
