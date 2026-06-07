<?php

namespace App\Http\Controllers;

use App\Models\OkrTarget;
use App\Models\Okr1Partisipasi;
use App\Models\Okr2BumdesUnit;
use App\Models\Okr2PadesContribution;
use App\Models\Okr2Transaction;
use App\Models\Okr3SdmCapacity;
use App\Models\VillageProgram;
use App\Models\SystemLog;
use App\Models\Report;
use App\Services\OkrService;
use App\Services\ProgramService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * DashboardController - Menyediakan data agregat untuk halaman dashboard utama.
 *
 * Menggabungkan data dari semua OKR untuk ditampilkan sebagai:
 * - Top-cards statistik (5 matriks kinerja)
 * - Radial/sirkular chart capaian OKR
 * - Line chart tren bulanan
 * - Bar chart Target vs Realisasi
 * - Donut chart distribusi status program
 * - Feed aktivitas terbaru
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly OkrService      $okrService,
        private readonly ProgramService  $programService,
        private readonly AuditLogService $auditLogService,
    ) {}

    /**
     * Halaman dashboard utama.
     */
    public function index(Request $request): View
    {
        $year  = (int) $request->get('year',  now()->year);
        $month = $request->filled('month') ? (int) $request->get('month') : null;

        // ---- Data Ringkasan OKR ----
        $okrSummary = $this->okrService->getDashboardOkrSummary($year, $month);

        // ---- Hitung Kegiatan Terlaksana (program SELESAI + AKTIF)
        $kegiatanTerlaksana = VillageProgram::where('status', 'SELESAI')->count();
        $target             = OkrTarget::where('year', $year)->first();

        // ---- Data Grafik Line Chart (Tren Partisipasi)
        $trenPartisipasi = $this->okrService->getTrenPartisipasi($year);

        // ---- Data Grafik Bar Chart (Target vs Realisasi Bulanan)
        $barChartData = $this->okrService->getBarChartTargetRealisasi($year);

        // ---- Data Donut Chart (Distribusi Status Program)
        $distribusiProgram = $this->programService->getDistribusiStatusProgram();

        // ---- Feed Aktivitas Terbaru (10 terakhir)
        $feedAktivitas = SystemLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ---- Opsi Dropdown Tahun (dari data yang ada, ± 2 tahun)
        $tahunOptions = range(now()->year - 2, now()->year + 1);

        // ---- Top Cards Data (dari okrSummary)
        $topCards = $okrSummary['top_cards'];

        // Format nilai numerik ke ringkasan human-readable
        $topCards['omzet_bumdes']['formatted'] = $this->formatRupiah($topCards['omzet_bumdes']['nilai']);
        $topCards['laba_bersih']['formatted']  = $this->formatRupiah($topCards['laba_bersih']['nilai']);
        $topCards['kontribusi_pades']['formatted'] = $this->formatRupiah($topCards['kontribusi_pades']['nilai']);

        // Tren kegiatan (sederhana: bandingkan dengan target)
        $trenKegiatan = $target?->target_total_kegiatan > 0
            ? round(($kegiatanTerlaksana / $target->target_total_kegiatan) * 100, 1)
            : 0;

        return view('dashboard.index', compact(
            'year', 'month', 'okrSummary', 'target',
            'kegiatanTerlaksana', 'trenKegiatan',
            'trenPartisipasi', 'barChartData',
            'distribusiProgram', 'feedAktivitas',
            'tahunOptions', 'topCards',
        ));
    }

    // ---- OKR 1: Partisipasi ----

    public function okr1Index(Request $request): View
    {
        $year  = (int) $request->get('year', now()->year);
        $data  = Okr1Partisipasi::where('year', $year)->orderBy('id')->get();
        $tren  = $this->okrService->getTrenPartisipasi($year);
        $capaian = $this->okrService->getCapaianOkr1($year);
        $target  = OkrTarget::where('year', $year)->first();

        return view('okr.okr1', compact('year', 'data', 'tren', 'capaian', 'target'));
    }

    public function okr1Store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'month'                   => ['required', 'string'],
            'year'                    => ['required', 'integer', 'min:2020', 'max:2099'],
            'total_warga_wajib_lapor' => ['required', 'integer', 'min:1'],
            'warga_hadir'             => ['required', 'integer', 'min:0'],
        ]);

        try {
            $record = $this->okrService->simpanPartisipasiBulanan($request->all());

            $this->auditLogService->logBerhasil(
                'UPDATE DATA',
                'OKR1 Partisipasi',
                "Input partisipasi {$request->month} {$request->year}: {$record->calculated_percentage}%"
            );

            return response()->json([
                'success'    => true,
                'message'    => 'Data partisipasi berhasil disimpan.',
                'percentage' => $record->calculated_percentage,
            ]);
        } catch (\Exception $e) {
            $this->auditLogService->logGagal('UPDATE DATA', 'OKR1 Partisipasi', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ---- OKR 2: BUMDes ----

    public function okr2Index(Request $request): View
    {
        $year    = (int) $request->get('year', now()->year);
        $units   = Okr2BumdesUnit::all();
        $capaian = $this->okrService->getCapaianOkr2($year);
        $target  = OkrTarget::where('year', $year)->first();

        // Transaksi terbaru 20 baris
        $transaksiTerbaru = Okr2Transaction::with('bumdesUnit')
            ->whereYear('transaction_date', $year)
            ->orderBy('transaction_date', 'desc')
            ->limit(20)
            ->get();

        return view('okr.okr2', compact('year', 'units', 'capaian', 'target', 'transaksiTerbaru'));
    }

    public function okr2StoreTransaksi(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'bumdes_unit_id'   => ['required', 'exists:okr_2_bumdes_units,id'],
            'transaction_date' => ['required', 'date'],
            'type'             => ['required', 'in:Pemasukan,Pengeluaran'],
            'nominal'          => ['required', 'numeric', 'min:0.01'],
            'description'      => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $trx = $this->okrService->simpanTransaksiBumdes($request->all());

            $this->auditLogService->logBerhasil(
                'CREATE DATA',
                'OKR2 BUMDes',
                "Transaksi {$trx->type} Rp " . number_format($trx->nominal, 0, ',', '.') . " pada unit ID {$trx->bumdes_unit_id}"
            );

            return response()->json(['success' => true, 'message' => 'Transaksi berhasil disimpan.', 'data' => $trx]);
        } catch (\Exception $e) {
            $this->auditLogService->logGagal('CREATE DATA', 'OKR2 BUMDes', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function okr2StorePades(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'bumdes_unit_id'    => ['required', 'exists:okr_2_bumdes_units,id'],
            'period_year_month' => ['required', 'regex:/^\d{4}-\d{2}$/'],
            'nominal_setoran'   => ['required', 'numeric', 'min:0.01'],
            'file_proof'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $filePath = null;
        if ($request->hasFile('file_proof')) {
            $filePath = $request->file('file_proof')->store('pades-proofs', 'public');
        }

        try {
            $contribution = $this->okrService->simpanSetoranPades($request->all(), $filePath);

            $this->auditLogService->logBerhasil(
                'UPLOAD FILE',
                'OKR2 PADes',
                "Setoran PADes Rp " . number_format($contribution->nominal_setoran, 0, ',', '.') .
                " periode {$contribution->period_year_month}"
            );

            return response()->json(['success' => true, 'message' => 'Setoran PADes berhasil disimpan.', 'data' => $contribution]);
        } catch (\RuntimeException $e) {
            $this->auditLogService->logGagal('CREATE DATA', 'OKR2 PADes', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ---- OKR 3: SDM ----

    public function okr3Index(Request $request): View
    {
        $year    = (int) $request->get('year', now()->year);
        $capaian = $this->okrService->getCapaianOkr3($year);
        $history = Okr3SdmCapacity::where('period_date', 'like', "$year-%")
            ->orderBy('period_date', 'desc')
            ->get();

        return view('okr.okr3', compact('year', 'capaian', 'history'));
    }

    public function okr3Store(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'period_date'              => ['required', 'date'],
            'total_perangkat'          => ['required', 'integer', 'min:1'],
            'total_staf_terlatih'      => ['required', 'integer', 'min:0'],
            'avg_competency_score'     => ['required', 'numeric', 'min:0', 'max:10'],
            'keaktifan_kinerja_persen' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        try {
            $record = $this->okrService->simpanSdmCapacity($request->all());

            $this->auditLogService->logBerhasil(
                'CREATE DATA',
                'OKR3 SDM',
                "Input data SDM periode {$request->period_date}: skor kompetensi {$record->avg_competency_score}"
            );

            return response()->json(['success' => true, 'message' => 'Data SDM berhasil disimpan.', 'data' => $record]);
        } catch (\Exception $e) {
            $this->auditLogService->logGagal('CREATE DATA', 'OKR3 SDM', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ---- Target Tahunan ----

    public function targetIndex(): View
    {
        $year   = now()->year;
        $target = OkrTarget::where('year', $year)->first();
        return view('okr.target', compact('target', 'year'));
    }

    public function targetStore(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'year'                        => ['required', 'integer', 'min:2020'],
            'target_partisipasi_persen'   => ['required', 'numeric', 'min:0', 'max:100'],
            'target_total_kegiatan'       => ['required', 'integer', 'min:1'],
            'target_kehadiran_musyawarah' => ['required', 'integer', 'min:1'],
            'target_omzet_bumdes'         => ['required', 'numeric', 'min:0'],
            'target_laba_bersih'          => ['required', 'numeric', 'min:0'],
            'target_kontribusi_pades'     => ['required', 'numeric', 'min:0'],
            'target_pelatihan_masyarakat' => ['required', 'integer', 'min:0'],
            'target_indeks_inovasi'       => ['required', 'in:Rendah,Sedang,Tinggi,Sangat Tinggi'],
            'target_kepuasan_masyarakat'  => ['required', 'numeric', 'min:0', 'max:5'],
            'catatan_strategis'           => ['nullable', 'string'],
            'is_verified_rkp'             => ['nullable', 'boolean'],
            'is_verified_pagu'            => ['nullable', 'boolean'],
            'is_verified_bpd'             => ['nullable', 'boolean'],
        ]);

        // Otorisasi: hanya admin yang bisa simpan target
        if (!Auth::user()->isAdmin() && !Auth::user()->isKepala()) {
            $this->auditLogService->logUnauthorizedAccess('OKR Target', 'Store Target Tahunan');
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $target = OkrTarget::updateOrCreate(
            ['year' => $request->year],
            [
                'target_partisipasi_persen'   => $request->target_partisipasi_persen,
                'target_total_kegiatan'       => $request->target_total_kegiatan,
                'target_kehadiran_musyawarah' => $request->target_kehadiran_musyawarah,
                'target_omzet_bumdes'         => $request->target_omzet_bumdes,
                'target_laba_bersih'          => $request->target_laba_bersih,
                'target_kontribusi_pades'     => $request->target_kontribusi_pades,
                'target_pelatihan_masyarakat' => $request->target_pelatihan_masyarakat,
                'target_indeks_inovasi'       => $request->target_indeks_inovasi,
                'target_kepuasan_masyarakat'  => $request->target_kepuasan_masyarakat,
                'catatan_strategis'           => $request->catatan_strategis,
                'is_verified_rkp'             => $request->boolean('is_verified_rkp'),
                'is_verified_pagu'            => $request->boolean('is_verified_pagu'),
                'is_verified_bpd'             => $request->boolean('is_verified_bpd'),
            ]
        );

        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'OKR Target',
            "Update target tahunan OKR {$request->year} oleh " . Auth::user()->name
        );

        return response()->json([
            'success'    => true,
            'message'    => 'Data target berhasil disimpan.',
            'timestamp'  => now()->format('d/m/Y H:i:s'),
            'verified'   => $target->isFullyVerified(),
        ]);
    }

    // ---- Helper ----

    private function formatRupiah(float $amount): string
    {
        if ($amount >= 1_000_000_000) return 'Rp ' . number_format($amount / 1_000_000_000, 1) . 'M';
        if ($amount >= 1_000_000)     return 'Rp ' . number_format($amount / 1_000_000, 1) . 'jt';
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
