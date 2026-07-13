@extends('layouts.app')
@section('title', 'Manajemen User')
@section('breadcrumb', 'Manajemen User')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Manajemen Pengguna</h1>
        <p class="text-sm text-sage-600 mt-0.5">Kelola akun, hak akses, jabatan, status keaktifan, dan kata sandi pengguna portal</p>
    </div>
    <a href="{{ route('users.create') }}"
       class="inline-flex items-center justify-center gap-2 bg-forest hover:bg-[#075956] text-white text-sm font-bold px-5 py-2.5 rounded-xl
              active:scale-[0.98] transition-all shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah User Baru
    </a>
</div>

{{-- Alert Messages --}}
@if(session('success'))
<div class="mb-5 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>{{ session('success') }}</span>
</div>
@endif

@if(session('error'))
<div class="mb-5 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
    <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Main Table Container --}}
<div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
    <div class="px-6 py-4 border-b border-sage-100/60 flex items-center justify-between">
        <h2 class="font-bold text-forest text-sm">Daftar Pengguna Portal</h2>
        <p class="text-xs text-sage-500">{{ $users->count() }} Pengguna</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-forest-50/30 border-b border-sage-100/60">
                    <th class="text-left px-6 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Nama & Email</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Role</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Jabatan</th>
                    <th class="text-left px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">No. Telepon</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Status</th>
                    <th class="text-center px-6 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sage-50">
                @forelse($users as $user)
                <tr class="hover:bg-forest-50/20 transition-colors {{ !$user->is_active ? 'bg-gray-50/40' : '' }}">
                    {{-- Nama & Email --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-forest flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">{{ $user->initials }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 leading-snug">{{ $user->name }}</p>
                                <p class="text-[10px] text-sage-500 font-medium">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td class="px-4 py-4 text-center">
                        @if($user->isSuperAdmin())
                        <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-100">
                            Super Admin
                        </span>
                        @else
                        <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-forest-50 text-forest border border-forest-100">
                            Admin
                        </span>
                        @endif
                    </td>

                    {{-- Jabatan --}}
                    <td class="px-4 py-4 text-center">
                        @if($user->jabatan)
                        <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-sage-100 text-sage-800">
                            {{ $user->jabatan }}
                        </span>
                        @else
                        <span class="text-gray-400 font-medium">—</span>
                        @endif
                    </td>

                    {{-- No Telepon --}}
                    <td class="px-4 py-4">
                        <span class="font-medium text-gray-600">{{ $user->phone ?? '—' }}</span>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full
                            {{ $user->is_active ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Edit --}}
                            <a href="{{ route('users.edit', $user->id) }}"
                               title="Edit Data"
                               class="p-1.5 text-sage-600 bg-sage-50 border border-sage-200 rounded-lg hover:bg-forest hover:text-white hover:border-forest transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </a>

                            {{-- Reset Password --}}
                            <button onclick="bukaModalResetPassword({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    title="Reset Password"
                                    class="p-1.5 text-amber-600 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 7a2 2 0 012 2m-5 4a5 5 0 01-5-5 5 5 0 015-5 5 5 0 015 5 5 5 0 01-5 5zm0 0v8m0 0l-3-3m3 3l3-3"/>
                                </svg>
                            </button>

                            @if(auth()->id() !== $user->id)
                            {{-- Toggle Status --}}
                            <form action="{{ route('users.toggle', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                        class="p-1.5 text-xs font-bold border rounded-lg transition-all
                                               {{ $user->is_active
                                                  ? 'text-red-600 bg-red-50 border-red-200 hover:bg-red-500 hover:text-white hover:border-red-500'
                                                  : 'text-green-600 bg-green-50 border-green-200 hover:bg-green-500 hover:text-white hover:border-green-500' }}">
                                    @if($user->is_active)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                    @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </button>
                            </form>

                            {{-- Delete --}}
                            <button onclick="hapusUser({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                    title="Hapus User"
                                    class="p-1.5 text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-500 hover:text-white hover:border-red-500 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>

                            {{-- Hidden Delete Form --}}
                            <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="w-8 h-8 text-sage-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="font-semibold">Belum Ada User</span>
                            <span class="text-[10px] text-gray-400">Silakan tambahkan user baru melalui tombol di kanan atas.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Hidden Form for Reset Password --}}
<form id="reset-password-form" method="POST" class="hidden">
    @csrf
    <input type="password" name="password" id="reset-pass-field">
    <input type="password" name="password_confirmation" id="reset-pass-confirm-field">
</form>

@endsection

@section('scripts')
<script>
    /** Confirm and delete user */
    function hapusUser(id, name) {
        Swal.fire({
            title: 'Hapus User?',
            text: `Apakah Anda yakin ingin menghapus akun "${name}" secara permanen? Tindakan ini tidak dapat dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#87A996',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold text-white',
                cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold text-gray-700'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    /** Prompt and reset password via Swal */
    function bukaModalResetPassword(userId, name) {
        Swal.fire({
            title: `Reset Password "${name}"`,
            html: `
                <div class="text-left text-xs space-y-3">
                    <p class="text-gray-500 mb-4">Masukkan password baru untuk pengguna ini (minimal 8 karakter).</p>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Password Baru</label>
                        <input type="password" id="swal-password" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:border-forest" placeholder="Minimal 8 karakter">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" id="swal-password-confirm" class="w-full px-3 py-2 border rounded-xl focus:outline-none focus:border-forest" placeholder="Ulangi password baru">
                    </div>
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#096b68',
            cancelButtonColor: '#87A996',
            confirmButtonText: 'Reset Password',
            cancelButtonText: 'Batal',
            preConfirm: () => {
                const password = Swal.getPopup().querySelector('#swal-password').value;
                const passwordConfirm = Swal.getPopup().querySelector('#swal-password-confirm').value;
                
                if (!password || !passwordConfirm) {
                    Swal.showValidationMessage('Kedua kolom password wajib diisi.');
                    return false;
                }
                if (password.length < 8) {
                    Swal.showValidationMessage('Password minimal harus 8 karakter.');
                    return false;
                }
                if (password !== passwordConfirm) {
                    Swal.showValidationMessage('Konfirmasi password tidak cocok.');
                    return false;
                }
                
                return { password, passwordConfirm };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reset-password-form');
                form.action = `/users/${userId}/reset-password`;
                document.getElementById('reset-pass-field').value = result.value.password;
                document.getElementById('reset-pass-confirm-field').value = result.value.passwordConfirm;
                form.submit();
            }
        });
    }
</script>
@endsection
