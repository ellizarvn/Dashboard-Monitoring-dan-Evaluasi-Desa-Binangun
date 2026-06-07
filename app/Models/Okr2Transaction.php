<?php
// ============================================================
// app/Models/Okr2Transaction.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Transaksi keuangan unit BUMDes (Pemasukan & Pengeluaran).
 */
class Okr2Transaction extends Model
{
    protected $table = 'okr_2_transactions';

    protected $fillable = [
        'bumdes_unit_id', 'transaction_date', 'type', 'nominal', 'description',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'nominal'          => 'decimal:2',
        ];
    }

    public function bumdesUnit(): BelongsTo
    {
        return $this->belongsTo(Okr2BumdesUnit::class, 'bumdes_unit_id');
    }

    /** Format nominal ke format Rupiah ringkas */
    public function getNominalFormattedAttribute(): string
    {
        $value = (float) $this->nominal;
        if ($value >= 1_000_000) {
            return 'Rp ' . number_format($value / 1_000_000, 1) . 'jt';
        }
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
