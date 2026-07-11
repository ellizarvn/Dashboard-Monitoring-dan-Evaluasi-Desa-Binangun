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
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * ReportController - Manajemen laporan desa (Draft & Published).
 */
class ReportController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function index(Request $request): View
    {
        $totalLaporan    = Report::count();
        $terpublikasi    = Report::where('status', 'Published')->count();
        $draf            = Report::where('status', 'Draft')->count();
        $menungguReview  = Report::where('status', 'Draft')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $query = Report::with('author')->orderBy('report_date', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(10)->withQueryString();

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

    /**
     * Ekspor Laporan Desa ke format CSV (dengan BOM untuk Excel).
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $this->auditLogService->logBerhasil(
            'EXPORT DATA',
            'Laporan Desa',
            'Export daftar laporan desa ke format CSV oleh ' . Auth::user()->name
        );

        $reports = Report::with('author')
            ->orderBy('report_date', 'desc')
            ->get();

        $filename = 'laporan-desa-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($reports) {
            $output = fopen('php://output', 'w');

            // BOM agar terbaca UTF-8 di Excel
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header kolom
            fputcsv($output, ['Judul Laporan', 'Jenis Laporan', 'Tanggal Laporan', 'Penulis', 'Status'], ';');

            foreach ($reports as $report) {
                fputcsv($output, [
                    $report->title,
                    $report->type,
                    $report->report_date ? $report->report_date->format('d/m/Y') : '-',
                    $report->author?->name ?? '—',
                    $report->status
                ], ';');
            }
            fclose($output);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
