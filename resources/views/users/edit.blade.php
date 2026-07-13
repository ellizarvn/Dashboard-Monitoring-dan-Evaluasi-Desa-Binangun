@extends('layouts.app')
@section('title', 'Edit Data User')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="mb-7">
    <a href="{{ route('users.index') }}"
       class="inline-flex items-center gap-1.5 text-xs font-bold text-sage-600 hover:text-forest transition-colors mb-3">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar User
    </a>
    <h1 class="font-display text-2xl text-forest">Ubah Pengguna</h1>
    <p class="text-sm text-sage-600 mt-0.5">Edit detail akun, hak akses, dan jabatan untuk akun "{{ $user->name }}"</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form Card --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <h2 class="font-bold text-forest text-sm mb-5 pb-3 border-b border-sage-50">Informasi Pengguna</h2>

        <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nama lengkap staf"
                       class="w-full px-4 py-3 rounded-xl border @error('name') border-red-300 focus:ring-red-100 @else border-gray-200 focus:ring-forest/10 @enderror text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2">
                @error('name')
                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="email@desabinangun.id"
                       class="w-full px-4 py-3 rounded-xl border @error('email') border-red-300 focus:ring-red-100 @else border-gray-200 focus:ring-forest/10 @enderror text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2">
                @error('email')
                <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- No Telepon --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">No. Telepon / WA (Opsional)</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08123456789"
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium focus:ring-forest/10
                              focus:outline-none focus:border-forest focus:ring-2">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Hak Akses (Role)</label>
                    <select name="role" id="role-select" required onchange="toggleJabatanField()"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium bg-white focus:ring-forest/10
                                   focus:outline-none focus:border-forest focus:ring-2">
                        <option value="admin" {{ old('role', $user->db_role) === 'admin' ? 'selected' : '' }}>Admin (Staf Desa)</option>
                        <option value="super_admin" {{ old('role', $user->db_role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                </div>

                {{-- Jabatan --}}
                <div id="jabatan-container">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jabatan</label>
                    <select name="jabatan" id="jabatan-select"
                            class="w-full px-4 py-3 rounded-xl border @error('jabatan') border-red-300 focus:ring-red-100 @else border-gray-200 focus:ring-forest/10 @enderror text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2">
                        <option value="">Pilih Jabatan</option>
                        <option value="Administrator" {{ old('jabatan', $user->jabatan) === 'Administrator' ? 'selected' : '' }}>Administrator</option>
                        <option value="Kepala Desa" {{ old('jabatan', $user->jabatan) === 'Kepala Desa' ? 'selected' : '' }}>Kepala Desa</option>
                        <option value="Tim Monitoring" {{ old('jabatan', $user->jabatan) === 'Tim Monitoring' ? 'selected' : '' }}>Tim Monitoring</option>
                        <option value="BPD" {{ old('jabatan', $user->jabatan) === 'BPD' ? 'selected' : '' }}>BPD</option>
                    </select>
                    @error('jabatan')
                    <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-sage-50/50 rounded-xl p-4 border border-sage-100/60 space-y-3">
                <p class="text-xs font-bold text-forest">Ubah Kata Sandi (Opsional)</p>
                <p class="text-[11px] text-sage-600">Biarkan kolom di bawah ini kosong jika Anda tidak ingin merubah kata sandi pengguna ini.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter"
                               class="w-full px-4 py-3 rounded-xl border @error('password') border-red-300 focus:ring-red-100 @else border-gray-200 focus:ring-forest/10 @enderror text-sm font-medium
                                      focus:outline-none focus:border-forest focus:ring-2">
                        @error('password')
                        <p class="text-red-500 text-[10px] font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium focus:ring-forest/10
                                      focus:outline-none focus:border-forest focus:ring-2">
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="pt-3 flex justify-end gap-3">
                <a href="{{ route('users.index') }}"
                   class="px-5 py-3 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-bold transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-3 bg-forest hover:bg-[#075956] text-white text-xs font-bold rounded-xl shadow-md transition-all active:scale-[0.98]">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Info Card --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h3 class="font-bold text-forest text-xs mb-3">Ketentuan Peran & Otorisasi</h3>
            <ul class="text-xs text-sage-700 space-y-2.5 list-disc pl-4">
                <li>
                    <strong class="text-gray-800">Super Admin:</strong> Memiliki kontrol penuh atas manajemen akun pengguna (tambah, edit, status aktif, hapus, reset password).
                </li>
                <li>
                    <strong class="text-gray-800">Admin (Staf Desa):</strong> Dibatasi berdasarkan Jabatan masing-masing.
                </li>
                <li>
                    <strong class="text-gray-800">Jabatan:</strong>
                    <ul class="pl-3 mt-1 space-y-1 list-circle text-[11px] text-sage-600">
                        <li><strong>Administrator:</strong> Memiliki akses penuh mutasi data OKR dan program.</li>
                        <li><strong>Kepala Desa:</strong> Memiliki hak verifikasi OKR dan laporan, serta ekspor log.</li>
                        <li><strong>Tim Monitoring:</strong> Berwenang menginput data program kerja dan OKR.</li>
                        <li><strong>BPD:</strong> Akses read-only monitoring kinerja & visualisasi.</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function toggleJabatanField() {
        const role = document.getElementById('role-select').value;
        const container = document.getElementById('jabatan-container');
        const select = document.getElementById('jabatan-select');
        
        if (role === 'super_admin') {
            container.classList.add('opacity-40');
            select.value = '';
            select.disabled = true;
            select.required = false;
        } else {
            container.classList.remove('opacity-40');
            select.disabled = false;
            select.required = true;
        }
    }

    // Trigger on page load
    document.addEventListener('DOMContentLoaded', toggleJabatanField);
</script>
@endsection
