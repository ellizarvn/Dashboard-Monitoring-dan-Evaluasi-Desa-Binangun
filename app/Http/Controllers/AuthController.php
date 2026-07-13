<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * AuthController - Mengelola seluruh alur autentikasi & manajemen sesi.
 *
 * Fitur:
 * - Registrasi administrator dengan validasi NIP 13-digit
 * - Login dengan pencatatan IP dan sesi
 * - Logout dengan konfirmasi modal
 * - Ubah kata sandi
 * - Toggle 2FA
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    // ============================================================
    // LOGIN & LOGOUT
    // ============================================================

    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login dengan pencatatan IP dan sesi.
     */
    public function login(Request $request): \Illuminate\Http\JsonResponse|RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            // Catat percobaan login gagal
            $this->auditLogService->logGagal(
                'LOGIN',
                'Auth',
                "Percobaan login gagal untuk email: {$request->email}",
                null
            );

            return back()
                ->withErrors(['email' => 'Email atau kata sandi tidak valid.'])
                ->withInput($request->only('email'));
        }

        /** @var User $user */
        $user = Auth::user();

        // Validasi keaktifan akun
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->auditLogService->logGagal(
                'LOGIN',
                'Auth',
                "Percobaan login gagal: Akun ditangguhkan (Email: {$request->email})",
                $user->id
            );

            return back()
                ->withErrors(['email' => 'Akun Anda dinonaktifkan. Hubungi Super Admin.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        // Catat login berhasil ke audit log
        $this->auditLogService->logLogin(
            $user->id,
            $user->name,
            $request->userAgent() ?? ''
        );

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Proses logout dengan pencatatan ke audit log.
     */
    public function logout(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $this->auditLogService->logLogout($user->id, $user->name);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('logged_out', true);
    }

    // ============================================================
    // PENGATURAN KEAMANAN AKUN
    // ============================================================

    /**
     * Tampilkan halaman pengaturan keamanan & manajemen sesi.
     */
    public function showSettings(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Ambil sesi aktif dari tabel sessions
        $activeSessions = \DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $payload = [];
                $currentSessId = session()->getId();

                return [
                    'id'            => $session->id,
                    'ip_address'    => $session->ip_address,
                    'user_agent'    => $session->user_agent,
                    'last_activity' => \Carbon\Carbon::createFromTimestamp($session->last_activity),
                    'is_current'    => $session->id === $currentSessId,
                    'browser'       => $this->parseBrowser($session->user_agent ?? ''),
                    'device'        => $this->parseDevice($session->user_agent ?? ''),
                ];
            });

        return view('auth.settings', compact('user', 'activeSessions'));
    }

    /**
     * Proses perubahan kata sandi.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed',
                                   'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.regex' => 'Kata sandi baru harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'Pengaturan Akun',
            "Pengguna '{$user->name}' berhasil mengubah kata sandi.",
            $user->id
        );

        return back()->with('success_password', 'Kata sandi berhasil diperbarui.');
    }

    /**
     * Toggle status Otentikasi Dua Faktor (2FA).
     */
    public function toggle2fa(Request $request): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $newState = !$user->two_factor_enabled;
        $user->update(['two_factor_enabled' => $newState]);

        $stateText = $newState ? 'diaktifkan' : 'dinonaktifkan';
        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'Pengaturan Keamanan',
            "2FA {$stateText} oleh pengguna '{$user->name}'.",
            $user->id
        );

        return response()->json([
            'success' => true,
            'enabled' => $newState,
            'message' => "Otentikasi Dua Faktor berhasil {$stateText}.",
        ]);
    }

    /**
     * Revoke sesi tertentu (bukan sesi aktif saat ini).
     */
    public function revokeSession(Request $request, string $sessionId): \Illuminate\Http\JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($sessionId === session()->getId()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat mencabut sesi aktif ini.'], 422);
        }

        \DB::table('sessions')
            ->where('id', $sessionId)
            ->where('user_id', $user->id)
            ->delete();

        $this->auditLogService->logBerhasil(
            'DELETE DATA',
            'Manajemen Sesi',
            "Sesi ID {$sessionId} dicabut oleh pengguna '{$user->name}'.",
            $user->id
        );

        return response()->json(['success' => true, 'message' => 'Sesi berhasil dicabut.']);
    }

    // ---- Helper Methods ----

    private function parseBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg'))     return 'Microsoft Edge';
        if (str_contains($ua, 'Chrome'))  return 'Google Chrome';
        if (str_contains($ua, 'Firefox')) return 'Mozilla Firefox';
        if (str_contains($ua, 'Safari'))  return 'Safari';
        if (str_contains($ua, 'Opera'))   return 'Opera';
        return 'Browser Tidak Dikenal';
    }

    private function parseDevice(string $ua): string
    {
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android')) return 'Perangkat Mobile';
        if (str_contains($ua, 'Tablet') || str_contains($ua, 'iPad'))    return 'Tablet';
        return 'Desktop / Laptop';
    }
}
