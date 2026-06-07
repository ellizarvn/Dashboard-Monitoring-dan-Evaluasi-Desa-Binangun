<?php
// ============================================================
// app/Models/Okr2BumdesUnit.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Unit usaha BUMDes dengan relasi ke transaksi dan kontribusi PADes.
 */
class Okr2BumdesUnit extends Model
{
    protected $table = 'okr_2_bumdes_units';

    protected $fillable = [
        'name_unit', 'sector', 'pic_name', 'initial_capital',
    ];

    protected function casts(): array
    {
        return [
            'initial_capital' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Okr2Transaction::class, 'bumdes_unit_id');
    }

    public function padesContributions(): HasMany
    {
        return $this->hasMany(Okr2PadesContribution::class, 'bumdes_unit_id');
    }

    /**
     * Hitung total omzet (hanya pemasukan) untuk periode tertentu.
     */
    public function getTotalOmzet(?int $year = null, ?int $month = null): float
    {
        $query = $this->transactions()->where('type', 'Pemasukan');
        if ($year)  $query->whereYear('transaction_date', $year);
        if ($month) $query->whereMonth('transaction_date', $month);
        return (float) $query->sum('nominal');
    }

    /**
     * Hitung total pengeluaran untuk periode tertentu.
     */
    public function getTotalPengeluaran(?int $year = null, ?int $month = null): float
    {
        $query = $this->transactions()->where('type', 'Pengeluaran');
        if ($year)  $query->whereYear('transaction_date', $year);
        if ($month) $query->whereMonth('transaction_date', $month);
        return (float) $query->sum('nominal');
    }

    /**
     * Hitung laba bersih: omzet - pengeluaran.
     */
    public function getLabaBersih(?int $year = null, ?int $month = null): float
    {
        return $this->getTotalOmzet($year, $month) - $this->getTotalPengeluaran($year, $month);
    }
}
