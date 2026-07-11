<?php
// ============================================================
// app/Http/Controllers/ProgramController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\VillageProgram;
use App\Services\ProgramService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ProgramController - Manajemen program desa.
 *
 * RBAC:
 * - View: semua role
 * - Create/Update: admin, tim_monitoring
 * - Delete: admin only
 */
class ProgramController extends Controller
{
    public function __construct(
        private readonly ProgramService  $programService,
        private readonly AuditLogService $auditLogService,
    ) {}

    /**
     * Daftar semua program desa dengan filter.
     */
    public function index(Request $request): View
    {
        $status    = $request->get('status');
        $linkedOkr = $request->get('linked_okr');
        $programs  = $this->programService->getDaftarProgram($status, $linkedOkr, 10);
        $distribusi = $this->programService->getDistribusiStatusProgram();
        $avgProgress = $this->programService->getAvgProgressAktif();

        return view('programs.index', compact('programs', 'distribusi', 'avgProgress', 'status', 'linkedOkr'));
    }

    /**
     * Simpan program baru.
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::user()->canMutateData()) {
            $this->auditLogService->logUnauthorizedAccess('Program Desa', 'Buat Program Baru');
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'name'                => ['required', 'string', 'min:5', 'max:200'],
            'linked_okr'          => ['required', 'in:OKR1,OKR2,OKR3'],
            'status'              => ['required', 'in:AKTIF,PENDING,SELESAI'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'description'         => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $program = $this->programService->buatProgram($request->all());

            $this->auditLogService->logBerhasil(
                'CREATE DATA',
                'Program Desa',
                "Program baru '{$program->name}' (OKR: {$program->linked_okr}) dibuat oleh " . Auth::user()->name
            );

            return response()->json([
                'success' => true,
                'message' => "Program '{$program->name}' berhasil ditambahkan.",
                'data'    => $program,
            ]);
        } catch (\Exception $e) {
            $this->auditLogService->logGagal('CREATE DATA', 'Program Desa', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Update program yang sudah ada (termasuk progress slider).
     */
    public function update(Request $request, VillageProgram $program): JsonResponse
    {
        if (!Auth::user()->canMutateData()) {
            $this->auditLogService->logUnauthorizedAccess('Program Desa', "Update Program #{$program->id}");
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $request->validate([
            'name'                => ['sometimes', 'string', 'min:5', 'max:200'],
            'linked_okr'          => ['sometimes', 'in:OKR1,OKR2,OKR3'],
            'status'              => ['sometimes', 'in:AKTIF,PENDING,SELESAI'],
            'progress_percentage' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'description'         => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $updated = $this->programService->updateProgram($program, $request->all());

            $this->auditLogService->logBerhasil(
                'UPDATE DATA',
                'Program Desa',
                "Program '{$updated->name}' diupdate: status={$updated->status}, progress={$updated->progress_percentage}%"
            );

            return response()->json([
                'success'  => true,
                'message'  => 'Program berhasil diperbarui.',
                'data'     => $updated,
            ]);
        } catch (\RuntimeException $e) {
            $this->auditLogService->logGagal('UPDATE DATA', 'Program Desa', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Hapus program (admin only).
     */
    public function destroy(VillageProgram $program): JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            $this->auditLogService->logUnauthorizedAccess('Program Desa', "Hapus Program #{$program->id}");
            return response()->json(['success' => false, 'message' => 'Hanya administrator yang dapat menghapus program.'], 403);
        }

        try {
            $programName = $program->name;
            $this->programService->hapusProgram($program);

            $this->auditLogService->logBerhasil(
                'DELETE DATA',
                'Program Desa',
                "Program '{$programName}' dihapus oleh " . Auth::user()->name
            );

            return response()->json(['success' => true, 'message' => "Program '{$programName}' berhasil dihapus."]);
        } catch (\RuntimeException $e) {
            $this->auditLogService->logGagal('DELETE DATA', 'Program Desa', $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * Ambil detail program untuk modal (AJAX).
     */
    public function show(VillageProgram $program): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $program]);
    }

    /**
     * Mengimpor program desa dari berkas Excel (CSV).
     */
    public function import(Request $request): JsonResponse
    {
        if (!Auth::user()->canMutateData()) {
            $this->auditLogService->logUnauthorizedAccess('Program Desa', 'Import Program CSV');
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

        // Deteksi pembatas (delimiter) koma atau titik koma
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
            // Membaca header kolom
            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                fclose($handle);
                return response()->json(['success' => false, 'message' => 'Berkas CSV kosong atau tidak valid.'], 422);
            }

            // Hapus karakter BOM UTF-8 jika ada pada nama kolom pertama
            $header[0] = preg_replace('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{FEFF}]/u', '', $header[0]);

            // Petakan kolom ke index array
            $headerMap = [];
            foreach ($header as $index => $colName) {
                $colClean = strtolower(trim($colName));
                $headerMap[$colClean] = $index;
            }

            // Temukan index berdasarkan aliases
            $idxName     = $this->findHeaderIndex($headerMap, ['nama program', 'nama_program', 'nama', 'program']);
            $idxOkr      = $this->findHeaderIndex($headerMap, ['terhubung okr', 'terhubung_okr', 'okr']);
            $idxStatus   = $this->findHeaderIndex($headerMap, ['status', 'status program', 'status_program']);
            $idxProgress = $this->findHeaderIndex($headerMap, ['progress realisasi', 'progress_realisasi', 'progress', 'realisasi', 'persentase']);
            $idxDesc     = $this->findHeaderIndex($headerMap, ['deskripsi', 'description', 'keterangan', 'detail']);

            if ($idxName === null || $idxOkr === null || $idxStatus === null || $idxProgress === null) {
                fclose($handle);
                return response()->json([
                    'success' => false,
                    'message' => 'Struktur kolom berkas tidak sesuai template. Kolom wajib: "Nama Program", "Terhubung OKR", "Status", dan "Progress Realisasi".'
                ], 422);
            }

            $rowNum = 1;
            \Illuminate\Support\Facades\DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                    $rowNum++;

                    // Lewati baris kosong
                    if (count($row) === 1 && empty($row[0])) {
                        continue;
                    }

                    $name     = isset($row[$idxName]) ? trim($row[$idxName]) : '';
                    $okr      = isset($row[$idxOkr]) ? strtoupper(trim($row[$idxOkr])) : '';
                    $status   = isset($row[$idxStatus]) ? strtoupper(trim($row[$idxStatus])) : '';
                    $progress = isset($row[$idxProgress]) ? trim($row[$idxProgress]) : '0';
                    $desc     = ($idxDesc !== null && isset($row[$idxDesc])) ? trim($row[$idxDesc]) : '';

                    // Normalisasi spasi OKR (contoh: OKR 1 -> OKR1)
                    $okr = str_replace(' ', '', $okr);

                    // Validasi baris data
                    if (empty($name) || strlen($name) < 5) {
                        $errors[] = "Baris {$rowNum}: Nama program terlalu pendek (min. 5 karakter).";
                        continue;
                    }
                    if (!in_array($okr, ['OKR1', 'OKR2', 'OKR3'])) {
                        $errors[] = "Baris {$rowNum}: 'Terhubung OKR' tidak valid (harus OKR1/OKR2/OKR3).";
                        continue;
                    }
                    if (!in_array($status, ['AKTIF', 'PENDING', 'SELESAI'])) {
                        $errors[] = "Baris {$rowNum}: Status tidak valid (harus AKTIF/PENDING/SELESAI).";
                        continue;
                    }

                    $progressVal = (int) $progress;
                    if ($status === 'SELESAI') {
                        $progressVal = 100;
                    }
                    if ($progressVal < 0 || $progressVal > 100) {
                        $errors[] = "Baris {$rowNum}: Progress harus bernilai antara 0-100.";
                        continue;
                    }

                    // Insert or Update program desa
                    VillageProgram::updateOrCreate(
                        ['name' => $name],
                        [
                            'linked_okr'          => $okr,
                            'status'              => $status,
                            'progress_percentage' => $progressVal,
                            'description'         => $desc ?: null,
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

        // Catat Audit Log
        $this->auditLogService->logBerhasil(
            'CREATE DATA',
            'Program Desa',
            "Impor data program desa berhasil: {$importedCount} program berhasil dimasukkan."
        );

        return response()->json([
            'success' => true,
            'message' => "Berhasil mengimpor {$importedCount} data program.",
            'errors'  => $errors // Kembalikan baris error sebagai peringatan non-blocking jika ada data sukses
        ]);
    }

    /**
     * Cari index kolom dengan beberapa varian nama.
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
}
