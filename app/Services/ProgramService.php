<?php

namespace App\Services;

use App\Models\VillageProgram;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * ProgramService - Manajemen mutasi program desa.
 *
 * Mengelola CRUD program, transisi status, dan pemetaan progress slider.
 */
class ProgramService
{
    // Aturan transisi status yang diizinkan
    private const ALLOWED_TRANSITIONS = [
        'PENDING'  => ['AKTIF', 'PENDING'],
        'AKTIF'    => ['AKTIF', 'SELESAI', 'PENDING'],
        'SELESAI'  => ['SELESAI'], // Status selesai bersifat final
    ];

    /**
     * Buat program desa baru dengan validasi data.
     */
    public function buatProgram(array $data): VillageProgram
    {
        return VillageProgram::create([
            'name'                => $data['name'],
            'linked_okr'          => $data['linked_okr'],
            'status'              => $data['status'] ?? 'PENDING',
            'progress_percentage' => $this->sanitizeProgress($data['progress_percentage'] ?? 0),
            'description'         => $data['description'] ?? null,
        ]);
    }

    /**
     * Update program yang sudah ada.
     *
     * @throws \RuntimeException jika transisi status tidak valid
     */
    public function updateProgram(VillageProgram $program, array $data): VillageProgram
    {
        $newStatus = $data['status'] ?? $program->status;

        // Validasi transisi status
        if (!$this->isValidTransition($program->status, $newStatus)) {
            throw new \RuntimeException(
                "Transisi status dari '{$program->status}' ke '{$newStatus}' tidak diizinkan."
            );
        }

        // Jika status menjadi SELESAI, progress otomatis 100%
        $progress = $newStatus === 'SELESAI'
            ? 100
            : $this->sanitizeProgress($data['progress_percentage'] ?? $program->progress_percentage);

        $program->update([
            'name'                => $data['name'] ?? $program->name,
            'linked_okr'          => $data['linked_okr'] ?? $program->linked_okr,
            'status'              => $newStatus,
            'progress_percentage' => $progress,
            'description'         => $data['description'] ?? $program->description,
        ]);

        return $program->fresh();
    }

    /**
     * Hapus program desa.
     *
     * @throws \RuntimeException jika program masih AKTIF
     */
    public function hapusProgram(VillageProgram $program): void
    {
        if ($program->status === 'AKTIF') {
            throw new \RuntimeException(
                "Program aktif tidak dapat dihapus. Ubah status menjadi PENDING atau SELESAI terlebih dahulu."
            );
        }
        $program->delete();
    }

    /**
     * Ambil daftar program dengan filter dan paginasi.
     */
    public function getDaftarProgram(
        ?string $status = null,
        ?string $linkedOkr = null,
        int     $perPage = 10
    ): LengthAwarePaginator {
        $query = VillageProgram::query()->latest();

        if ($status) {
            $query->where('status', $status);
        }
        if ($linkedOkr) {
            $query->where('linked_okr', $linkedOkr);
        }

        return $query->paginate($perPage);
    }

    /**
     * Statistik distribusi program berdasarkan status untuk donut chart.
     */
    public function getDistribusiStatusProgram(): array
    {
        $counts = VillageProgram::selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        return [
            'AKTIF'   => $counts['AKTIF']   ?? 0,
            'PENDING' => $counts['PENDING'] ?? 0,
            'SELESAI' => $counts['SELESAI'] ?? 0,
            'total'   => array_sum($counts),
        ];
    }

    /**
     * Rata-rata progress semua program aktif.
     */
    public function getAvgProgressAktif(): float
    {
        return round(
            VillageProgram::where('status', 'AKTIF')->avg('progress_percentage') ?? 0,
            1
        );
    }

    /**
     * Sanitasi nilai progress agar selalu 0-100.
     */
    private function sanitizeProgress(mixed $value): int
    {
        return max(0, min(100, (int) $value));
    }

    /**
     * Validasi apakah transisi status diizinkan.
     */
    private function isValidTransition(string $currentStatus, string $newStatus): bool
    {
        $allowed = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
        return in_array($newStatus, $allowed);
    }
}
