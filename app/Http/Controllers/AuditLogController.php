<?php
// ============================================================
// app/Http/Controllers/AuditLogController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * AuditLogController - Tampilan dan ekspor sistem audit log.
 *
 * RBAC:
 * - View: admin, kepala_desa
 * - Export CSV: admin only
 */
class AuditLogController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    /**
     * Halaman daftar audit log dengan filter.
     */
    public function index(Request $request): View
    {
        // Otorisasi: hanya admin dan kepala desa
        if (!in_array(Auth::user()->role, ['admin', 'kepala_desa'])) {
            $this->auditLogService->logUnauthorizedAccess('Audit Log', 'Akses Halaman Log');
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $filters = $request->only(['start_date', 'end_date', 'user_id', 'activity_type']);
        $logs    = $this->auditLogService->getFilteredLogs($filters, 20);
        $metrics = $this->auditLogService->getMetricsSummary();
        $users   = User::orderBy('name')->get(['id', 'name', 'role']);

        $activityTypes = [
            'LOGIN', 'LOGOUT', 'UPDATE DATA', 'CREATE DATA',
            'DELETE DATA', 'UPLOAD FILE', 'EXPORT DATA', 'UNAUTHORIZED ACCESS',
        ];

        return view('audit.index', compact('logs', 'metrics', 'users', 'activityTypes', 'filters'));
    }

    /**
     * Ekspor log ke CSV (streaming response untuk file besar).
     * Admin only.
     */
    public function exportCsv(Request $request): StreamedResponse|JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            $this->auditLogService->logUnauthorizedAccess('Audit Log', 'Export CSV');
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $filters = $request->only(['start_date', 'end_date']);

        $this->auditLogService->logBerhasil(
            'EXPORT DATA',
            'Audit Log',
            'Export log sistem ke format CSV oleh ' . Auth::user()->name
        );

        $filename = 'audit-log-' . now()->format('Y-m-d-His') . '.csv';
        $rows     = $this->auditLogService->exportToCsvArray($filters);

        return response()->streamDownload(function () use ($rows) {
            $output = fopen('php://output', 'w');

            // BOM untuk Excel agar bisa baca UTF-8
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($rows as $row) {
                fputcsv($output, $row, ';');
            }
            fclose($output);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
