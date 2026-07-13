<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function __construct(
        private readonly AuditLogService $auditLogService
    ) {}

    /**
     * Tampilkan daftar user.
     */
    public function index(): View
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Tampilkan form tambah user.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Simpan user baru ke database.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'jabatan'   => $request->role === 'super_admin' ? null : $request->jabatan,
            'phone'     => $request->phone,
            'is_active' => true,
        ]);

        $this->auditLogService->logBerhasil(
            'CREATE DATA',
            'User Management',
            "Membuat user baru: {$user->name} ({$user->email}, Role: {$user->role})"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    /**
     * Tampilkan form edit user.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Perbarui data user di database.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->jabatan = $request->role === 'super_admin' ? null : $request->jabatan;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'User Management',
            "Memperbarui data user: {$user->name} ({$user->email})"
        );

        return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Hapus user secara permanen.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Cegah menghapus diri sendiri
        if (auth()->id() === $user->id) {
            $this->auditLogService->logGagal(
                'DELETE DATA',
                'User Management',
                "Gagal menghapus user: Mencoba menghapus diri sendiri ({$user->email})"
            );
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $userEmail = $user->email;
        $user->delete();

        $this->auditLogService->logBerhasil(
            'DELETE DATA',
            'User Management',
            "Menghapus user secara permanen: {$userName} ({$userEmail})"
        );

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Aktifkan atau nonaktifkan akun user.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        // Cegah menonaktifkan diri sendiri
        if (auth()->id() === $user->id) {
            $this->auditLogService->logGagal(
                'UPDATE DATA',
                'User Management',
                "Gagal menonaktifkan user: Mencoba menonaktifkan diri sendiri ({$user->email})"
            );
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        $this->auditLogService->logBerhasil(
            'UPDATE DATA',
            'User Management',
            "Mengubah status keaktifan user {$user->name}: {$status}"
        );

        return redirect()->route('users.index')->with('success', "Status user berhasil {$status}.");
    }

    /**
     * Reset password user.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        $this->auditLogService->logBerhasil(
            'PASSWORD RESET',
            'User Management',
            "Mereset password user: {$user->name} ({$user->email})"
        );

        return redirect()->route('users.index')->with('success', 'Password user berhasil direset.');
    }
}
