<?php
// ============================================================
// app/Http/Middleware/CheckRole.php
// ============================================================
namespace App\Http\Middleware;

use App\Services\AuditLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole Middleware - Pembatasan akses berbasis RBAC.
 *
 * Penggunaan di route:
 *   ->middleware('role:admin')
 *   ->middleware('role:admin,kepala_desa')
 */
class CheckRole
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            // Catat percobaan akses tidak sah
            $this->auditLogService->logGagal(
                'UNAUTHORIZED ACCESS',
                'Middleware',
                "Role '{$user->role}' mencoba mengakses route yang dibatasi untuk: " . implode(', ', $roles),
                $user->id
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Halaman ini memerlukan hak akses: ' . implode(', ', $roles));
        }

        return $next($request);
    }
}
