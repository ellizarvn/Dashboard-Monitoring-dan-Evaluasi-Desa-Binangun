<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuditLogService - Pencatatan otomatis audit trail sistem.
 *
 * Menangkap request data secara otomatis (User, Tipe, Modul, IP, Status)
 * dan menyuntikkannya ke tabel system_logs di setiap mutasi data.
 */
class AuditLogService
{
    /**
     * Catat aktivitas berhasil.
     *
     * @param string $activityType Tipe: LOGIN, UPDATE DATA, dll
     * @param string $module       Nama modul: Dashboard, OKR1, dll
     * @param string $description  Deskripsi naratif apa yang terjadi
     * @param int|null $userId     Override user ID (opsional, default: auth user)
     */
    public function logBerhasil(
        string $activityType,
        string $module,
        string $description,
        ?int   $userId = null
    ): SystemLog {
        return $this->catat($activityType, $module, $description, 'BERHASIL', $userId);
    }

    /**
     * Catat aktivitas gagal (termasuk unauthorized access).
     */
    public function logGagal(
        string $activityType,
        string $module,
        string $description,
        ?int   $userId = null
    ): SystemLog {
        return $this->catat($activityType, $module, $description, 'GAGAL', $userId);
    }

    /**
     * Catat percobaan akses ilegal secara otomatis.
     */
    public function logUnauthorizedAccess(string $module, string $attemptedAction): SystemLog
    {
        $userId = Auth::id();
        $userName = Auth::user()?->name ?? 'Guest';

        return $this->catat(
            'UNAUTHORIZED ACCESS',
            $module,
            "Pengguna '{$userName}' mencoba mengakses '{$attemptedAction}' tanpa izin yang memadai.",
            'GAGAL',
            $userId
        );
    }

    /**
     * Catat login pengguna.
     */
    public function logLogin(int $userId, string $userName, string $userAgent): SystemLog
    {
        $browser = $this->parseBrowserFromUserAgent($userAgent);
        return $this->catat(
            'LOGIN',
            'Auth',
            "Login berhasil dari browser {$browser}.",
            'BERHASIL',
            $userId
        );
    }

    /**
     * Catat logout pengguna.
     */
    public function logLogout(int $userId, string $userName): SystemLog
    {
        return $this->catat(
            'LOGOUT',
            'Auth',
            "Pengguna '{$userName}' berhasil logout dari sistem.",
            'BERHASIL',
            $userId
        );
    }

    /**
     * Ambil log sistem dengan filter dan paginasi.
     *
     * @param array $filters ['start_date', 'end_date', 'user_id', 'activity_type']
     */
    public function getFilteredLogs(array $filters = [], int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = SystemLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['activity_type'])) {
            $query->where('activity_type', $filters['activity_type']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Statistik ringkasan log untuk top metrics cards.
     */
    public function getMetricsSummary(): array
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $totalHariIni   = SystemLog::whereDate('created_at', $today)->count();
        $totalKemarin   = SystemLog::whereDate('created_at', $yesterday)->count();
        $kritisHariIni  = SystemLog::whereDate('created_at', $today)
            ->whereIn('activity_type', ['UNAUTHORIZED ACCESS', 'DELETE DATA'])
            ->where('status', 'GAGAL')
            ->count();

        // Pengguna aktif unik hari ini
        $penggunaAktif  = SystemLog::whereDate('created_at', $today)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $trendPersen = $totalKemarin > 0
            ? round((($totalHariIni - $totalKemarin) / $totalKemarin) * 100, 1)
            : 0;

        return [
            'total_hari_ini'  => $totalHariIni,
            'total_kemarin'   => $totalKemarin,
            'trend_persen'    => $trendPersen,
            'kritis'          => $kritisHariIni,
            'pengguna_aktif'  => $penggunaAktif,
        ];
    }

    /**
     * Export log ke format array untuk CSV.
     */
    public function exportToCsvArray(array $filters = []): array
    {
        $query = SystemLog::with('user')->orderBy('created_at', 'desc');

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        $logs = $query->limit(5000)->get(); // Batas 5000 baris untuk performa

        $rows = [
            ['Waktu', 'Pengguna', 'Role', 'Tipe Aktivitas', 'Modul', 'Deskripsi', 'IP Address', 'Status'],
        ];

        foreach ($logs as $log) {
            $rows[] = [
                $log->created_at->format('d/m/Y H:i:s'),
                $log->user?->name ?? 'Sistem',
                $log->user?->role_label ?? '-',
                $log->activity_type,
                $log->module,
                $log->description,
                $log->ip_address ?? '-',
                $log->status,
            ];
        }

        return $rows;
    }

    // ---- Private Methods ----

    /**
     * Method inti untuk menulis log ke database.
     */
    private function catat(
        string $activityType,
        string $module,
        string $description,
        string $status,
        ?int   $userId
    ): SystemLog {
        $effectiveUserId = $userId ?? Auth::id();
        $ipAddress = $this->getClientIp();

        return SystemLog::create([
            'user_id'       => $effectiveUserId,
            'activity_type' => $activityType,
            'module'        => $module,
            'description'   => $description,
            'ip_address'    => $ipAddress,
            'status'        => $status,
            'created_at'    => now(),
        ]);
    }

    /**
     * Ambil IP address klien dengan dukungan proxy.
     */
    private function getClientIp(): string
    {
        $request = app(Request::class);
        return $request->ip() ?? '0.0.0.0';
    }

    /**
     * Parse nama browser dari user-agent string.
     */
    private function parseBrowserFromUserAgent(string $userAgent): string
    {
        $browsers = [
            'Chrome'  => '/Chrome\/[\d.]+/',
            'Firefox' => '/Firefox\/[\d.]+/',
            'Safari'  => '/Safari\/[\d.]+/',
            'Edge'    => '/Edg\/[\d.]+/',
            'Opera'   => '/OPR\/[\d.]+/',
        ];

        foreach ($browsers as $name => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $name;
            }
        }

        return 'Browser Tidak Diketahui';
    }
}
