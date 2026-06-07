<?php
// ============================================================
// app/Http/Controllers/ReportController.php
// ============================================================
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * ReportController - Manajemen laporan desa (Draft & Published).
 */
class ReportController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function index(): View
    {
        $totalLaporan    = Report::count();
        $terpublikasi    = Report::where('status', 'Published')->count();
        $draf            = Report::where('status', 'Draft')->count();
        $menungguReview  = Report::where('status', 'Draft')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $reports = Report::with('author')
            ->orderBy('report_date', 'desc')
            ->paginate(10);

        return view('reports.index', compact(
            'totalLaporan', 'terpublikasi', 'draf', 'menungguReview', 'reports'
        ));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'min:5', 'max:250'],
            'type'        => ['required', 'string', 'max:100'],
            'report_date' => ['required', 'date'],
            'status'      => ['required', 'in:Draft,Published'],
        ]);

        $report = Report::create([
            'title'       => $request->title,
            'type'        => $request->type,
            'report_date' => $request->report_date,
            'author_id'   => Auth::id(),
            'status'      => $request->status,
        ]);

        $this->auditLogService->logBerhasil(
            'CREATE DATA',
            'Laporan Desa',
            "Laporan '{$report->title}' dibuat dengan status {$report->status}"
        );

        return response()->json(['success' => true, 'message' => 'Laporan berhasil disimpan.', 'data' => $report]);
    }

    public function publish(Report $report): JsonResponse
    {
        if (!Auth::user()->isAdmin() && !Auth::user()->isKepala()) {
            $this->auditLogService->logUnauthorizedAccess('Laporan Desa', "Publish Laporan #{$report->id}");
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $report->update(['status' => 'Published']);

        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'Laporan Desa',
            "Laporan '{$report->title}' dipublikasikan oleh " . Auth::user()->name
        );

        return response()->json(['success' => true, 'message' => 'Laporan berhasil dipublikasikan.']);
    }

    public function destroy(Report $report): JsonResponse
    {
        if (!Auth::user()->isAdmin()) {
            $this->auditLogService->logUnauthorizedAccess('Laporan Desa', "Hapus Laporan #{$report->id}");
            return response()->json(['success' => false, 'message' => 'Hanya admin yang dapat menghapus laporan.'], 403);
        }

        $title = $report->title;
        $report->delete();

        $this->auditLogService->logBerhasil(
            'DELETE DATA',
            'Laporan Desa',
            "Laporan '{$title}' dihapus oleh " . Auth::user()->name
        );

        return response()->json(['success' => true, 'message' => "Laporan '{$title}' berhasil dihapus."]);
    }
}
