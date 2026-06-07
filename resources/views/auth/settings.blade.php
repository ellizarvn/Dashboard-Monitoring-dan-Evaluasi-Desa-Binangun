@extends('layouts.app')
@section('title', 'Pengaturan Akun')
@section('breadcrumb', 'Pengaturan Akun')

@section('content')
<div class="mb-7">
    <h1 class="font-display text-2xl text-forest">Pengaturan Keamanan Akun</h1>
    <p class="text-sm text-sage-600 mt-0.5">Kelola kata sandi, otentikasi dua faktor, dan sesi aktif Anda</p>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- ============================================================ --}}
    {{-- KOLOM KIRI: Profil + Ubah Password + 2FA --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- Profil Info Card --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">Informasi Profil</h2>
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 rounded-2xl bg-forest flex items-center justify-center flex-shrink-0 shadow-lg shadow-forest/20">
                    <span class="text-xl font-black text-white">{{ $user->initials }}</span>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-forest text-lg">{{ $user->name }}</p>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-forest-50 text-forest">
                            <span class="w-1.5 h-1.5 rounded-full bg-forest"></span>
                            {{ $user->role_label }}
                        </span>
                        @if($user->nip_administrator)
                        <span class="text-xs text-sage-500 font-medium">NIP: {{ $user->nip_administrator }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Ubah Kata Sandi --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">Ubah Kata Sandi</h2>

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                @foreach($errors->all() as $error)
                    <p class="text-xs text-red-600 font-medium">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4">
                @csrf

                {{-- Kata Sandi Saat Ini --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current-pwd"
                               placeholder="Masukkan kata sandi saat ini"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium pr-12
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10" required>
                        <button type="button" onclick="togglePwd('current-pwd')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest p-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Kata Sandi Baru --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="new-pwd"
                               placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium pr-12
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10" required>
                        <button type="button" onclick="togglePwd('new-pwd')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest p-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Strength Indicator --}}
                    <div class="flex gap-1 mt-2" id="strength-bars">
                        <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar-1"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar-2"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar-3"></div>
                        <div class="h-1 flex-1 rounded-full bg-gray-100" id="bar-4"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1" id="strength-label">Masukkan kata sandi baru</p>
                </div>

                {{-- Konfirmasi Kata Sandi Baru --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="conf-pwd"
                               placeholder="Ulangi kata sandi baru"
                               class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium pr-12
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10" required>
                        <button type="button" onclick="togglePwd('conf-pwd')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest p-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                    Perbarui Kata Sandi
                </button>
            </form>
        </div>

        {{-- Otentikasi Dua Faktor (2FA) --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <h2 class="font-bold text-forest text-sm">Otentikasi Dua Faktor (2FA)</h2>
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                        Tambahkan lapisan keamanan ekstra pada akun Anda. Setiap login akan memerlukan kode verifikasi tambahan dari aplikasi autentikator.
                    </p>
                    <div class="mt-3 flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $user->two_factor_enabled ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $user->two_factor_enabled ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            {{ $user->two_factor_enabled ? '2FA Aktif' : '2FA Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                {{-- Toggle Switch --}}
                <div class="flex-shrink-0 mt-1">
                    <button type="button" id="toggle-2fa"
                            onclick="toggle2FA()"
                            class="relative inline-flex w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none
                                   {{ $user->two_factor_enabled ? 'bg-forest' : 'bg-gray-300' }}">
                        <span id="toggle-thumb"
                              class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transform transition-transform duration-300
                                     {{ $user->two_factor_enabled ? 'translate-x-6' : 'translate-x-0' }}">
                        </span>
                    </button>
                </div>
            </div>

            {{-- Info 2FA --}}
            @if($user->two_factor_enabled)
            <div class="mt-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-xs text-green-700 font-medium leading-relaxed">
                    Akun Anda dilindungi dengan otentikasi dua faktor. Setiap login memerlukan kode dari aplikasi autentikator Anda.
                </p>
            </div>
            @else
            <div class="mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs text-amber-700 font-medium leading-relaxed">
                    Aktifkan 2FA untuk meningkatkan keamanan akun administrator Anda secara signifikan.
                </p>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- KOLOM KANAN: Sesi Aktif --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6 sticky top-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-forest text-sm">Sesi Aktif</h2>
                <span class="text-[10px] font-bold text-sage-500 bg-sage-50 px-2 py-1 rounded-lg">
                    {{ count($activeSessions) }} Sesi
                </span>
            </div>

            <div class="space-y-4">
                @forelse($activeSessions as $session)
                <div class="relative bg-{{ $session['is_current'] ? 'forest-50' : 'gray-50' }} rounded-xl p-4 border
                            {{ $session['is_current'] ? 'border-forest-200' : 'border-gray-100' }}">

                    {{-- Badge Sesi Ini --}}
                    @if($session['is_current'])
                    <span class="absolute top-3 right-3 text-[9px] font-black text-white bg-forest px-2 py-0.5 rounded-full">
                        Sesi Ini
                    </span>
                    @endif

                    {{-- Ikon Perangkat --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg {{ $session['is_current'] ? 'bg-forest' : 'bg-gray-300' }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if(str_contains(strtolower($session['device']), 'mobile'))
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                @endif
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-800">{{ $session['browser'] }}</p>
                            <p class="text-[11px] text-gray-500">{{ $session['device'] }}</p>
                            <p class="text-[11px] font-mono text-sage-500 mt-1">{{ $session['ip_address'] ?? 'IP Tidak Diketahui' }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">
                                Aktif {{ $session['last_activity']->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Cabut (bukan sesi aktif) --}}
                    @if(!$session['is_current'])
                    <button onclick="cabutSesi('{{ $session['id'] }}')"
                            class="mt-3 w-full py-1.5 text-[11px] font-bold text-red-500 border border-red-200
                                   rounded-lg hover:bg-red-50 transition-colors">
                        Cabut Sesi Ini
                    </button>
                    @endif
                </div>
                @empty
                <div class="text-center py-6">
                    <p class="text-xs text-gray-400">Tidak ada sesi aktif lainnya</p>
                </div>
                @endforelse
            </div>

            {{-- Info Driver Sesi --}}
            <div class="mt-5 pt-5 border-t border-sage-100">
                <p class="text-[10px] text-gray-400 leading-relaxed">
                    Sesi tidak aktif selama lebih dari 2 jam akan otomatis dihapus oleh sistem. Jika Anda menemukan sesi mencurigakan, segera cabut dan ubah kata sandi Anda.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ---- Toggle Visibilitas Password ---- */
function togglePwd(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

/* ---- Strength Indicator Kata Sandi ---- */
document.getElementById('new-pwd')?.addEventListener('input', function() {
    const val = this.value;
    let score = 0;
    if (val.length >= 8)          score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors  = ['bg-red-400', 'bg-amber-400', 'bg-yellow-400', 'bg-forest'];
    const labels  = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'];

    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById(`bar-${i}`);
        bar.className = `h-1 flex-1 rounded-full ${i <= score ? colors[score - 1] : 'bg-gray-100'}`;
    }

    const lbl = document.getElementById('strength-label');
    lbl.textContent = score > 0 ? `Kekuatan: ${labels[score - 1]}` : 'Masukkan kata sandi baru';
    lbl.className = `text-[10px] mt-1 ${score >= 3 ? 'text-forest' : score >= 2 ? 'text-amber-600' : 'text-red-500'}`;
});

/* ---- Toggle 2FA via AJAX ---- */
async function toggle2FA() {
    const btn   = document.getElementById('toggle-2fa');
    const thumb = document.getElementById('toggle-thumb');
    const isOn  = btn.classList.contains('bg-forest');

    const result = await Swal.fire({
        icon: isOn ? 'warning' : 'question',
        title: isOn ? 'Nonaktifkan 2FA?' : 'Aktifkan 2FA?',
        html: isOn
            ? '<p class="text-sm text-gray-500">Menonaktifkan 2FA akan mengurangi keamanan akun Anda.</p>'
            : '<p class="text-sm text-gray-500">Aktifkan otentikasi dua faktor untuk perlindungan ekstra pada akun Anda.</p>',
        showCancelButton: true,
        confirmButtonText: isOn ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
        buttonsStyling: false,
    });

    if (!result.isConfirmed) return;

    const res = await apiFetch('{{ route('settings.2fa.toggle') }}', { method: 'POST' });

    if (res.ok && res.data.success) {
        if (res.data.enabled) {
            btn.classList.replace('bg-gray-300', 'bg-forest');
            thumb.classList.replace('translate-x-0', 'translate-x-6');
        } else {
            btn.classList.replace('bg-forest', 'bg-gray-300');
            thumb.classList.replace('translate-x-6', 'translate-x-0');
        }
        showToast(res.data.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showToast(res.data.message || 'Gagal mengubah status 2FA.', 'error');
    }
}

/* ---- Cabut Sesi ---- */
async function cabutSesi(sessionId) {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Cabut Sesi?',
        html: '<p class="text-sm text-gray-500">Perangkat yang menggunakan sesi ini akan otomatis keluar.</p>',
        showCancelButton: true,
        confirmButtonText: 'Ya, Cabut',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
        buttonsStyling: false,
    });

    if (!result.isConfirmed) return;

    const url = `{{ url('/pengaturan/sesi') }}/${sessionId}`;
    const res = await apiFetch(url, { method: 'DELETE' });

    if (res.ok && res.data.success) {
        showToast('Sesi berhasil dicabut.', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showToast(res.data.message || 'Gagal mencabut sesi.', 'error');
    }
}

/* ---- Flash message dari server ---- */
@if(session('success_password'))
document.addEventListener('DOMContentLoaded', () => {
    showToast('{{ session('success_password') }}', 'success');
});
@endif
</script>
@endpush
