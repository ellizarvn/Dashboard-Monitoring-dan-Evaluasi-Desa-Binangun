<?php
// ============================================================
// app/Models/OkrTarget.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Master target tahunan OKR.
 */
class OkrTarget extends Model
{
    protected $table = 'okr_targets';

    protected $fillable = [
        'year',
        'target_partisipasi_persen', 'target_total_kegiatan', 'target_kehadiran_musyawarah',
        'target_omzet_bumdes', 'target_laba_bersih', 'target_kontribusi_pades',
        'target_pelatihan_masyarakat', 'target_indeks_inovasi', 'target_kepuasan_masyarakat',
        'catatan_strategis',
        'is_verified_rkp', 'is_verified_pagu', 'is_verified_bpd',
    ];

    protected function casts(): array
    {
        return [
            'is_verified_rkp'  => 'boolean',
            'is_verified_pagu' => 'boolean',
            'is_verified_bpd'  => 'boolean',
            'target_partisipasi_persen'  => 'decimal:2',
            'target_omzet_bumdes'        => 'decimal:2',
            'target_laba_bersih'         => 'decimal:2',
            'target_kontribusi_pades'    => 'decimal:2',
            'target_kepuasan_masyarakat' => 'decimal:1',
        ];
    }

    /** Apakah semua verifikasi sudah dilengkapi */
    public function isFullyVerified(): bool
    {
        return $this->is_verified_rkp && $this->is_verified_pagu && $this->is_verified_bpd;
    }
}
