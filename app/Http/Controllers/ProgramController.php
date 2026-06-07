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
}
