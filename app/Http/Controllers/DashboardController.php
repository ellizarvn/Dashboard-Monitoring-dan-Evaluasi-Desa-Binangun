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

        // ---- Hitung Alokasi Dana Desa secara Dinamis dari Target ----
        if ($target) {
            $kehadiran = (int) $target->target_kehadiran_musyawarah;
            $kegiatan  = (int) $target->target_total_kegiatan;
            $pelatihan = (int) $target->target_pelatihan_masyarakat;
            $pades     = (float) $target->target_kontribusi_pades;
            $kepuasan  = (float) $target->target_kepuasan_masyarakat;
        } else {
            // Modifikasi dinamis berbasis tahun agar data terlihat bervariasi jika belum ada target di DB
            $factor    = 1.0 + (($year - 2026) * 0.08); 
            $kehadiran = round(150 * $factor);
            $kegiatan  = round(24 * $factor);
            $pelatihan = round(200 * $factor);
            $pades     = round(12000000 * $factor);
            $kepuasan  = round(4.2 * $factor, 1);
        }

        $pemerintahan = $kehadiran * 2500000;
        $pembangunan  = $kegiatan * 20833333;
        $pembinaan    = $pelatihan * 625000;
        $pemberdayaan = $pades * 15.625;
        $darurat      = $kepuasan > 0 ? ($kepuasan / 4.2) * 62500000 : 62500000;

        $totalDanaDesa = $pemerintahan + $pembangunan + $pembinaan + $pemberdayaan + $darurat;
        $totalDanaDesaFormatted = $totalDanaDesa >= 1000000000
            ? 'Rp ' . number_format($totalDanaDesa / 1000000000, 2, ',', '.') . ' Miliar'
            : 'Rp ' . number_format($totalDanaDesa, 0, ',', '.');
            
        $alokasiDanaDesa = [
            'pemerintahan' => $pemerintahan,
            'pembangunan'  => $pembangunan,
            'pembinaan'    => $pembinaan,
            'pemberdayaan' => $pemberdayaan,
            'darurat'      => $darurat,
            'total'        => $totalDanaDesa,
            'formatted'    => $totalDanaDesaFormatted,
        ];

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
            'alokasiDanaDesa', 'barChartData',
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

    /**
     * Memetakan kolom header CSV berdasarkan aliases
     */
    private function findHeaderIndex(array $headerMap, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            if (isset($headerMap[$alias])) {
                return $headerMap[$alias];
            }
        }
        return null;
    }

    /**
     * Import Data OKR 1 - Partisipasi Bulanan via CSV
     */
    public function okr1Import(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->canMutateData()) {
            $this->auditLogService->logUnauthorizedAccess('OKR 1', 'Import Partisipasi CSV');
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'file_import' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $file = $request->file('file_import');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['xlsx', 'xls'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format file Excel (.xlsx/.xls) belum didukung langsung. Harap simpan sebagai berkas CSV (Comma/Semicolon delimited) terlebih dahulu lalu coba unggah kembali.'
            ], 422);
        }

        $delimiter = ',';
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            if (str_contains($firstLine, ';')) {
                $delimiter = ';';
            }
            fclose($handle);
        }

        $importedCount = 0;
        $errors = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'Berkas CSV kosong atau tidak valid.'], 422);
            }

            $header[0] = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{FEFF}]/u', '', $header[0]);

            $headerMap = [];
            foreach ($header as $index => $colName) {
                $colClean = strtolower(trim($colName));
                $headerMap[$colClean] = $index;
            }

            $idxMonth     = $this->findHeaderIndex($headerMap, ['bulan', 'month', 'periode']);
            $idxYear      = $this->findHeaderIndex($headerMap, ['tahun', 'year']);
            $idxWajib     = $this->findHeaderIndex($headerMap, ['warga wajib lapor', 'warga_wajib_lapor', 'total warga wajib lapor', 'wajib lapor', 'total warga']);
            $idxHadir     = $this->findHeaderIndex($headerMap, ['warga hadir', 'warga_hadir', 'hadir']);

            if ($idxMonth === null || $idxYear === null || $idxWajib === null || $idxHadir === null) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Struktur kolom berkas tidak sesuai template. Kolom wajib: "Bulan", "Tahun", "Warga Wajib Lapor", dan "Warga Hadir".'
                ], 422);
            }

            $rowNum = 1;
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rowNum++;

                    if (count($row) === 1 && empty($row[0])) {
                        continue;
                    }

                    $month  = isset($row[$idxMonth]) ? trim($row[$idxMonth]) : '';
                    $year   = isset($row[$idxYear]) ? (int)trim($row[$idxYear]) : 0;
                    $wajib  = isset($row[$idxWajib]) ? (int)trim($row[$idxWajib]) : 0;
                    $hadir  = isset($row[$idxHadir]) ? (int)trim($row[$idxHadir]) : 0;

                    $month = ucfirst(strtolower($month));
                    $validMonths = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                    if (!in_array($month, $validMonths)) {
                        $errors[] = "Baris {$rowNum}: Bulan tidak valid (harus Januari-Desember).";
                        continue;
                    }
                    if ($year < 2020 || $year > 2035) {
                        $errors[] = "Baris {$rowNum}: Tahun tidak valid (harus antara 2020-2035).";
                        continue;
                    }
                    if ($wajib <= 0) {
                        $errors[] = "Baris {$rowNum}: Warga Wajib Lapor harus bernilai positif.";
                        continue;
                    }
                    if ($hadir < 0 || $hadir > $wajib) {
                        $errors[] = "Baris {$rowNum}: Warga Hadir tidak boleh negatif atau melebihi Wajib Lapor.";
                        continue;
                    }

                    $percentage = round(($hadir / $wajib) * 100, 2);

                    Okr1Partisipasi::updateOrCreate(
                        ['month' => $month, 'year' => $year],
                        [
                            'total_warga_wajib_lapor' => $wajib,
                            'warga_hadir'             => $hadir,
                            'calculated_percentage'   => $percentage,
                        ]
                    );
                    $importedCount++;
                }

                if (count($errors) > 0 && $importedCount === 0) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengimpor data. Semua baris tidak valid:',
                        'errors'  => $errors
                    ], 422);
                }

                \Illuminate\Support\Facades\DB::commit();
                fclose($handle);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan: ' . $e->getMessage()], 500);
            }
        }

        $this->auditLogService->logBerhasil(
            'CREATE DATA',
            'OKR 1',
            "Impor data partisipasi bulanan berhasil: {$importedCount} data berhasil dimasukkan."
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} data partisipasi.",
            'errors'  => $errors
        ]);
    }

    /**
     * Import Data OKR 2 - Transaksi BUMDes via CSV
     */
    public function okr2Import(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->canMutateData()) {
            $this->auditLogService->logUnauthorizedAccess('OKR 2', 'Import Transaksi BUMDes CSV');
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'file_import' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $file = $request->file('file_import');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['xlsx', 'xls'])) {
            return response()->json([
                'success' => false,
                'message' => 'Format file Excel (.xlsx/.xls) belum didukung langsung. Harap simpan sebagai berkas CSV (Comma/Semicolon delimited) terlebih dahulu lalu coba unggah kembali.'
            ], 422);
        }

        $delimiter = ',';
        if (($handle = fopen($filePath, 'r')) !== false) {
            $firstLine = fgets($handle);
            if (str_contains($firstLine, ';')) {
                $delimiter = ';';
            }
            fclose($handle);
        }

        $importedCount = 0;
        $errors = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'Berkas CSV kosong atau tidak valid.'], 422);
            }

            $header[0] = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{FEFF}]/u', '', $header[0]);

            $headerMap = [];
            foreach ($header as $index => $colName) {
                $colClean = strtolower(trim($colName));
                $headerMap[$colClean] = $index;
            }

            $idxNameUnit = $this->findHeaderIndex($headerMap, ['nama unit', 'nama_unit', 'unit', 'unit bumdes', 'unit_bumdes']);
            $idxDate     = $this->findHeaderIndex($headerMap, ['tanggal transaksi', 'tanggal_transaksi', 'tanggal', 'date']);
            $idxType     = $this->findHeaderIndex($headerMap, ['jenis', 'type', 'jenis transaksi', 'jenis_transaksi']);
            $idxNominal  = $this->findHeaderIndex($headerMap, ['nominal', 'amount', 'jumlah']);
            $idxDesc     = $this->findHeaderIndex($headerMap, ['keterangan', 'description', 'detail']);

            if ($idxNameUnit === null || $idxDate === null || $idxType === null || $idxNominal === null) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Struktur kolom berkas tidak sesuai template. Kolom wajib: "Nama Unit", "Tanggal Transaksi", "Jenis", dan "Nominal".'
                ], 422);
            }

            $rowNum = 1;
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rowNum++;

                    if (count($row) === 1 && empty($row[0])) {
                        continue;
                    }

                    $unitName = isset($row[$idxNameUnit]) ? trim($row[$idxNameUnit]) : '';
                    $date     = isset($row[$idxDate]) ? trim($row[$idxDate]) : '';
                    $type     = isset($row[$idxType]) ? ucfirst(strtolower(trim($row[$idxType]))) : '';
                    $nominal  = isset($row[$idxNominal]) ? trim($row[$idxNominal]) : '';
                    $desc     = ($idxDesc !== null && isset($row[$idxDesc])) ? trim($row[$idxDesc]) : '';

                    $unit = Okr2BumdesUnit::where('name_unit', 'LIKE', '%' . $unitName . '%')->first();
                    if (!$unit) {
                        $errors[] = "Baris {$rowNum}: Unit BUMDes dengan nama '{$unitName}' tidak ditemukan.";
                        continue;
                    }

                    $parsedDate = strtotime($date);
                    if (!$parsedDate) {
                        $errors[] = "Baris {$rowNum}: Format Tanggal '{$date}' tidak valid (gunakan YYYY-MM-DD).";
                        continue;
                    }
                    $formattedDate = date('Y-m-d', $parsedDate);

                    if (!in_array($type, ['Pemasukan', 'Pengeluaran'])) {
                        $errors[] = "Baris {$rowNum}: Jenis transaksi tidak valid (harus Pemasukan/Pengeluaran).";
                        continue;
                    }

                    $nominalVal = (float) $nominal;
                    if ($nominalVal <= 0) {
                        $errors[] = "Baris {$rowNum}: Nominal transaksi harus bernilai positif.";
                        continue;
                    }

                    Okr2Transaction::create([
                        'bumdes_unit_id'   => $unit->id,
                        'transaction_date' => $formattedDate,
                        'type'             => $type,
                        'nominal'          => $nominalVal,
                        'description'      => $desc ?: null,
                    ]);
                    $importedCount++;
                }

                if (count($errors) > 0 && $importedCount === 0) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    fclose($handle);
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal mengimpor data. Semua baris tidak valid:',
                        'errors'  => $errors
                    ], 422);
                }

                \Illuminate\Support\Facades\DB::commit();
                fclose($handle);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem saat menyimpan: ' . $e->getMessage()], 500);
            }
        }

        $this->auditLogService->logBerhasil(
            'CREATE DATA',
            'OKR 2',
            "Impor data transaksi BUMDes berhasil: {$importedCount} transaksi berhasil dimasukkan."
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} transaksi BUMDes.",
            'errors'  => $errors
        ]);
    }
}
