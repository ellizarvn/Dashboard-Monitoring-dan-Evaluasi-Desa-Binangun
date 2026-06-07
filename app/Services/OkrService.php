<?php

namespace App\Services;

use App\Models\OkrTarget;
use App\Models\Okr1Partisipasi;
use App\Models\Okr2BumdesUnit;
use App\Models\Okr2Transaction;
use App\Models\Okr2PadesContribution;
use App\Models\Okr3SdmCapacity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * OkrService - Service Layer untuk seluruh kalkulasi OKR.
 *
 * Memindahkan logika matematis dari Controller ke Service Layer
 * sesuai prinsip Single Responsibility dan Clean Architecture.
 */
class OkrService
{
    // Konstanta aturan PerDes: minimal 25% laba bersih wajib ke PADes
    public const PADES_MIN_PERCENTAGE = 25.0;

    // ============================================================
    // OKR 1: Partisipasi Masyarakat
    // ============================================================

    /**
     * Kalkulasi persentase partisipasi dari data input.
     * Formula: (warga_hadir / total_warga_wajib_lapor) * 100
     *
     * @throws \InvalidArgumentException jika total warga 0
     */
    public function hitungPartisipasiPersen(int $totalWarga, int $wargaHadir): float
    {
        if ($totalWarga <= 0) {
            throw new \InvalidArgumentException('Total warga wajib lapor tidak boleh 0 atau negatif.');
        }
        if ($wargaHadir > $totalWarga) {
            throw new \InvalidArgumentException('Warga hadir tidak boleh melebihi total warga wajib lapor.');
        }
        return round(($wargaHadir / $totalWarga) * 100, 2);
    }

    /**
     * Simpan atau update data partisipasi bulanan.
     * Menghitung otomatis calculated_percentage sebelum simpan.
     */
    public function simpanPartisipasiBulanan(array $data): Okr1Partisipasi
    {
        $persentase = $this->hitungPartisipasiPersen(
            (int) $data['total_warga_wajib_lapor'],
            (int) $data['warga_hadir']
        );

        return Okr1Partisipasi::updateOrCreate(
            ['month' => $data['month'], 'year' => $data['year']],
            [
                'total_warga_wajib_lapor' => $data['total_warga_wajib_lapor'],
                'warga_hadir'             => $data['warga_hadir'],
                'calculated_percentage'   => $persentase,
            ]
        );
    }

    /**
     * Ambil data tren partisipasi bulanan untuk chart multi-line.
     * Mengembalikan array berisi label bulan dan nilai persentase.
     */
    public function getTrenPartisipasi(int $year): array
    {
        $data = Okr1Partisipasi::where('year', $year)
            ->orderByRaw("FIELD(month, 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember')")
            ->get();

        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = substr($item->month, 0, 3);
            $values[] = (float) $item->calculated_percentage;
        }

        return compact('labels', 'values');
    }

    /**
     * Kalkulasi capaian OKR 1 terhadap target tahunan.
     * Mengembalikan rata-rata persentase seluruh bulan yang tersedia.
     */
    public function getCapaianOkr1(int $year): array
    {
        $target = OkrTarget::where('year', $year)->first();
        $partisipasi = Okr1Partisipasi::where('year', $year)->get();

        $avgPartisipasi = $partisipasi->avg('calculated_percentage') ?? 0;
        $targetPersen   = $target?->target_partisipasi_persen ?? 90.0;
        $capaianPersen  = $targetPersen > 0 ? round(($avgPartisipasi / $targetPersen) * 100, 1) : 0;

        return [
            'avg_partisipasi'  => round($avgPartisipasi, 2),
            'target'           => $targetPersen,
            'capaian_persen'   => min($capaianPersen, 100), // cap 100%
            'status'           => $capaianPersen >= 80 ? 'ON TRACK' : ($capaianPersen >= 60 ? 'AT RISK' : 'BEHIND'),
        ];
    }

    // ============================================================
    // OKR 2: BUMDes & Ekonomi Lokal
    // ============================================================

    /**
     * Validasi kepatuhan PerDes: kontribusi PADes minimal 25% dari laba bersih bulanan.
     *
     * @return array ['valid' => bool, 'min_setoran' => float, 'message' => string]
     */
    public function validasiMinimumPadesContribution(
        int    $bumdesUnitId,
        string $periodYearMonth,
        float  $nominalSetoran
    ): array {
        [$year, $month] = explode('-', $periodYearMonth);
        $unit = Okr2BumdesUnit::findOrFail($bumdesUnitId);

        $labaUnit = $unit->getLabaBersih((int)$year, (int)$month);

        if ($labaUnit <= 0) {
            return [
                'valid'       => false,
                'min_setoran' => 0,
                'laba_bersih' => 0,
                'message'     => 'Laba bersih unit usaha pada periode ini bernilai 0 atau negatif. Pastikan data transaksi sudah lengkap.',
            ];
        }

        $minSetoran = $labaUnit * (self::PADES_MIN_PERCENTAGE / 100);
        $valid      = $nominalSetoran >= $minSetoran;

        return [
            'valid'       => $valid,
            'min_setoran' => round($minSetoran, 2),
            'laba_bersih' => round($labaUnit, 2),
            'message'     => $valid
                ? 'Nominal setoran memenuhi ketentuan PerDes minimal ' . self::PADES_MIN_PERCENTAGE . '% dari laba bersih.'
                : sprintf(
                    'Nominal setoran Rp %s kurang dari minimal Rp %s (%.0f%% dari laba bersih Rp %s).',
                    number_format($nominalSetoran, 0, ',', '.'),
                    number_format($minSetoran, 0, ',', '.'),
                    self::PADES_MIN_PERCENTAGE,
                    number_format($labaUnit, 0, ',', '.')
                ),
        ];
    }

    /**
     * Simpan transaksi BUMDes baru.
     */
    public function simpanTransaksiBumdes(array $data): Okr2Transaction
    {
        return Okr2Transaction::create([
            'bumdes_unit_id'   => $data['bumdes_unit_id'],
            'transaction_date' => $data['transaction_date'],
            'type'             => $data['type'],
            'nominal'          => $data['nominal'],
            'description'      => $data['description'] ?? null,
        ]);
    }

    /**
     * Simpan setoran PADes dengan validasi PerDes.
     *
     * @throws \RuntimeException jika validasi gagal
     */
    public function simpanSetoranPades(array $data, ?string $filePath = null): Okr2PadesContribution
    {
        $validasi = $this->validasiMinimumPadesContribution(
            (int) $data['bumdes_unit_id'],
            $data['period_year_month'],
            (float) $data['nominal_setoran']
        );

        if (!$validasi['valid']) {
            throw new \RuntimeException($validasi['message']);
        }

        return Okr2PadesContribution::create([
            'bumdes_unit_id'    => $data['bumdes_unit_id'],
            'period_year_month' => $data['period_year_month'],
            'nominal_setoran'   => $data['nominal_setoran'],
            'file_proof_path'   => $filePath,
        ]);
    }

    /**
     * Hitung agregat BUMDes untuk dashboard: omzet, laba, pades.
     * Mengembalikan data ringkasan untuk top-cards statistik utama.
     */
    public function getAgregatBumdes(int $year, ?int $month = null): array
    {
        $query = Okr2Transaction::query();
        if ($month) {
            $query->whereYear('transaction_date', $year)->whereMonth('transaction_date', $month);
        } else {
            $query->whereYear('transaction_date', $year);
        }

        $totalOmzet  = (float) (clone $query)->where('type', 'Pemasukan')->sum('nominal');
        $totalKeluar = (float) (clone $query)->where('type', 'Pengeluaran')->sum('nominal');
        $labaBersih  = $totalOmzet - $totalKeluar;

        // Ambil total kontribusi PADes
        $padesQuery = Okr2PadesContribution::query();
        if ($month) {
            $padesQuery->where('period_year_month', sprintf('%04d-%02d', $year, $month));
        } else {
            $padesQuery->where('period_year_month', 'like', "$year-%");
        }
        $totalPades = (float) $padesQuery->sum('nominal_setoran');

        return [
            'total_omzet'   => $totalOmzet,
            'total_keluar'  => $totalKeluar,
            'laba_bersih'   => $labaBersih,
            'total_pades'   => $totalPades,
        ];
    }

    /**
     * Capaian OKR 2 terhadap target tahunan.
     */
    public function getCapaianOkr2(int $year): array
    {
        $target  = OkrTarget::where('year', $year)->first();
        $agregat = $this->getAgregatBumdes($year);

        $capaianOmzet = $target?->target_omzet_bumdes > 0
            ? round(($agregat['total_omzet'] / $target->target_omzet_bumdes) * 100, 1)
            : 0;

        $capaianLaba = $target?->target_laba_bersih > 0
            ? round(($agregat['laba_bersih'] / $target->target_laba_bersih) * 100, 1)
            : 0;

        $capaianPades = $target?->target_kontribusi_pades > 0
            ? round(($agregat['total_pades'] / $target->target_kontribusi_pades) * 100, 1)
            : 0;

        $avgCapaian = round(($capaianOmzet + $capaianLaba + $capaianPades) / 3, 1);

        return [
            'agregat'         => $agregat,
            'capaian_omzet'   => min($capaianOmzet, 100),
            'capaian_laba'    => min($capaianLaba, 100),
            'capaian_pades'   => min($capaianPades, 100),
            'avg_capaian'     => min($avgCapaian, 100),
            'status'          => $avgCapaian >= 80 ? 'ON TRACK' : ($avgCapaian >= 60 ? 'AT RISK' : 'BEHIND'),
        ];
    }

    // ============================================================
    // OKR 3: Kapasitas SDM
    // ============================================================

    /**
     * Simpan data kapasitas SDM baru.
     */
    public function simpanSdmCapacity(array $data): Okr3SdmCapacity
    {
        return Okr3SdmCapacity::create([
            'period_date'              => $data['period_date'],
            'total_perangkat'          => $data['total_perangkat'],
            'total_staf_terlatih'      => $data['total_staf_terlatih'],
            'avg_competency_score'     => $data['avg_competency_score'],
            'keaktifan_kinerja_persen' => $data['keaktifan_kinerja_persen'],
        ]);
    }

    /**
     * Capaian OKR 3 beserta tren terhadap bulan lalu.
     */
    public function getCapaianOkr3(int $year): array
    {
        $latest = Okr3SdmCapacity::where('period_date', 'like', "$year-%")
            ->orderBy('period_date', 'desc')
            ->first();

        $sebelumnya = Okr3SdmCapacity::where('period_date', 'like', "$year-%")
            ->orderBy('period_date', 'desc')
            ->skip(1)
            ->first();

        if (!$latest) {
            return ['capaian_persen' => 0, 'status' => 'NO DATA', 'tren_persen' => 0];
        }

        // Skor gabungan: rata-rata dari 3 indikator (masing-masing dinormalisasi ke 100)
        $skorStaf       = $latest->total_perangkat > 0
            ? ($latest->total_staf_terlatih / $latest->total_perangkat) * 100
            : 0;
        $skorKompetensi = ($latest->avg_competency_score / 10) * 100;
        $skorKeaktifan  = (float) $latest->keaktifan_kinerja_persen;

        $capaian = round(($skorStaf + $skorKompetensi + $skorKeaktifan) / 3, 1);

        // Hitung tren terhadap bulan sebelumnya
        $trenPersen = 0;
        if ($sebelumnya) {
            $skorStafLama      = $sebelumnya->total_perangkat > 0
                ? ($sebelumnya->total_staf_terlatih / $sebelumnya->total_perangkat) * 100
                : 0;
            $skorKompLama      = ($sebelumnya->avg_competency_score / 10) * 100;
            $skorKeaktifanLama = (float) $sebelumnya->keaktifan_kinerja_persen;
            $capaianLama       = ($skorStafLama + $skorKompLama + $skorKeaktifanLama) / 3;

            $trenPersen = $capaianLama > 0
                ? round((($capaian - $capaianLama) / $capaianLama) * 100, 1)
                : 0;
        }

        return [
            'capaian_persen'       => min($capaian, 100),
            'skor_staf_persen'     => round($skorStaf, 1),
            'skor_kompetensi'      => round($skorKompetensi, 1),
            'skor_keaktifan'       => round($skorKeaktifan, 1),
            'tren_persen'          => $trenPersen,
            'latest'               => $latest,
            'status'               => $capaian >= 80 ? 'ON TRACK' : ($capaian >= 60 ? 'AT RISK' : 'BEHIND'),
        ];
    }

    // ============================================================
    // AGREGAT DASHBOARD: Suplai data untuk radial chart
    // ============================================================

    /**
     * Hitung capaian total OKR gabungan untuk radial chart dashboard.
     * Rata-rata berbobot dari ketiga OKR.
     */
    public function getDashboardOkrSummary(int $year, ?int $month = null): array
    {
        $okr1 = $this->getCapaianOkr1($year);
        $okr2 = $this->getCapaianOkr2($year);
        $okr3 = $this->getCapaianOkr3($year);

        $totalCapaian = round(
            ($okr1['capaian_persen'] + $okr2['avg_capaian'] + $okr3['capaian_persen']) / 3,
            1
        );

        // Top cards data
        $agregatBumdes = $month
            ? $this->getAgregatBumdes($year, $month)
            : $this->getAgregatBumdes($year);

        $latestPartisipasi = Okr1Partisipasi::where('year', $year)
            ->orderBy('id', 'desc')
            ->first();

        // Hitung tren partisipasi terhadap bulan sebelumnya
        $prevPartisipasi = Okr1Partisipasi::where('year', $year)
            ->orderBy('id', 'desc')
            ->skip(1)
            ->first();

        $trenPartisipasi = 0;
        if ($latestPartisipasi && $prevPartisipasi && $prevPartisipasi->calculated_percentage > 0) {
            $trenPartisipasi = round(
                (($latestPartisipasi->calculated_percentage - $prevPartisipasi->calculated_percentage)
                    / $prevPartisipasi->calculated_percentage) * 100,
                1
            );
        }

        $target = OkrTarget::where('year', $year)->first();

        return [
            'total_capaian'     => $totalCapaian,
            'okr1'              => $okr1,
            'okr2'              => $okr2,
            'okr3'              => $okr3,
            'top_cards'         => [
                'partisipasi'       => [
                    'nilai'   => $latestPartisipasi?->calculated_percentage ?? 0,
                    'target'  => $target?->target_partisipasi_persen ?? 90,
                    'tren'    => $trenPartisipasi,
                ],
                'omzet_bumdes'      => [
                    'nilai'   => $agregatBumdes['total_omzet'],
                    'target'  => $target?->target_omzet_bumdes ?? 0,
                ],
                'laba_bersih'       => [
                    'nilai'   => $agregatBumdes['laba_bersih'],
                    'target'  => $target?->target_laba_bersih ?? 0,
                ],
                'kontribusi_pades'  => [
                    'nilai'   => $agregatBumdes['total_pades'],
                    'target'  => $target?->target_kontribusi_pades ?? 0,
                ],
            ],
        ];
    }

    /**
     * Data untuk bar chart: Target vs Realisasi bulanan (Omzet BUMDes).
     */
    public function getBarChartTargetRealisasi(int $year): array
    {
        $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

        $realisasi = Okr2Transaction::selectRaw(
            'MONTH(transaction_date) as bulan, SUM(nominal) as total'
        )
            ->where('type', 'Pemasukan')
            ->whereYear('transaction_date', $year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $target = OkrTarget::where('year', $year)->first();
        $targetBulanan = $target ? round($target->target_omzet_bumdes / 12, 0) : 0;

        $labels   = [];
        $actual   = [];
        $targets  = [];

        for ($m = 1; $m <= 12; $m++) {
            $labels[]  = $months[$m - 1];
            $actual[]  = (float) ($realisasi[$m] ?? 0);
            $targets[] = (float) $targetBulanan;
        }

        return compact('labels', 'actual', 'targets');
    }
}
