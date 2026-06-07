<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Administrator — Desa Binangun</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { DEFAULT: '#1A362B', 50: '#E6F4ED', 100: '#CCE7D6', 500: '#1A362B' },
                        sage:   { DEFAULT: '#87A996', 200: '#CCE0D8', 400: '#99C1B0' },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"DM Serif Display"', 'serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .forest-gradient {
            background: linear-gradient(135deg, #0A1F17 0%, #1A362B 40%, #2A5042 70%, #3D7059 100%);
        }
        .input-field {
            @apply w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium
                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10
                   transition-all duration-200 bg-white placeholder-gray-400;
        }
        .forest-pattern {
            background-image:
                radial-gradient(circle at 15% 25%, rgba(255,255,255,0.06) 0%, transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(255,255,255,0.04) 0%, transparent 40%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .slide-in { animation: slideInRight 0.5s cubic-bezier(0.4,0,0.2,1) forwards; }
    </style>
</head>
<body class="h-full">
<div class="min-h-screen flex">

    {{-- ============================================================ --}}
    {{-- SISI KIRI: Branding Forest Green --}}
    {{-- ============================================================ --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 forest-gradient forest-pattern flex-col justify-between p-12 relative overflow-hidden">

        {{-- Dekoratif Lingkaran --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white/5 -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 rounded-full bg-white/3 translate-y-1/3 -translate-x-1/3"></div>

        {{-- Logo Top --}}
        <div class="flex items-center gap-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center border border-white/20">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-bold text-lg leading-tight">Desa Binangun</p>
                <p class="text-white/60 text-xs font-medium tracking-widest uppercase">Sistem Informasi Desa</p>
            </div>
        </div>

        {{-- Konten Tengah --}}
        <div class="relative z-10 space-y-8">
            <div>
                <h1 class="font-display text-4xl xl:text-5xl text-white leading-tight">
                    Portal<br>
                    <span class="text-sage-200">Transparansi</span><br>
                    Publik
                </h1>
                <p class="mt-4 text-white/60 text-base leading-relaxed max-w-sm">
                    Sistem terpadu monitoring dan evaluasi program desa berbasis data untuk tata kelola yang lebih baik, akuntabel, dan transparan.
                </p>
            </div>

            {{-- Fitur bullets --}}
            <div class="space-y-3">
                @foreach([
                    ['Monitoring OKR real-time berbasis data', 'chart'],
                    ['Manajemen BUMDes terintegrasi', 'currency'],
                    ['Laporan otomatis & audit trail', 'document'],
                ] as [$text, $icon])
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-white/75 text-sm">{{ $text }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer Kiri --}}
        <p class="text-white/30 text-xs relative z-10">© {{ date('Y') }} Pemerintah Desa Binangun. Semua hak dilindungi.</p>
    </div>

    {{-- ============================================================ --}}
    {{-- SISI KANAN: Form Registrasi --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 bg-gray-50 overflow-y-auto">
        <div class="w-full max-w-md slide-in">

            {{-- Header Form --}}
            <div class="mb-8">
                {{-- Mobile Logo --}}
                <div class="lg:hidden flex items-center gap-3 mb-6">
                    <div class="w-9 h-9 rounded-xl bg-forest flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <span class="font-bold text-forest">Desa Binangun</span>
                </div>

                <h2 class="font-display text-3xl text-forest">Daftar Akun</h2>
                <p class="text-gray-500 text-sm mt-2">Registrasi khusus administrator portal desa</p>
            </div>

            {{-- Tampilkan error validasi server --}}
            @if($errors->any())
                <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
                    <p class="text-sm font-semibold text-red-700 mb-1">Terjadi Kesalahan Input:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM REGISTRASI --}}
            <form id="register-form" class="space-y-4">
                @csrf

                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="Masukkan nama lengkap Anda"
                           class="input-field @error('name') border-red-400 @enderror"
                           required>
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- NIP 13 Digit --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        NIP Administrator
                        <span class="text-sage-500 font-normal">(13 digit)</span>
                    </label>
                    <input type="text" name="nip_administrator" id="nip"
                           value="{{ old('nip_administrator') }}"
                           placeholder="1234567890123"
                           maxlength="13"
                           class="input-field @error('nip_administrator') border-red-400 @enderror"
                           pattern="\d{13}"
                           required>
                    <p class="mt-1 text-[11px] text-gray-400" id="nip-counter">0 / 13 digit</p>
                    @error('nip_administrator') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           placeholder="admin@desabinangun.id"
                           class="input-field @error('email') border-red-400 @enderror"
                           required>
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Kata Sandi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                               class="input-field pr-12 @error('password') border-red-400 @enderror"
                               required>
                        <button type="button" onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest transition-colors p-1">
                            <svg class="w-4 h-4" id="eye-password" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Konfirmasi Kata Sandi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Ulangi kata sandi"
                               class="input-field pr-12"
                               required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest transition-colors p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Checkbox Syarat Transparansi --}}
                <div class="flex items-start gap-3 pt-2">
                    <input type="checkbox" name="syarat_transparansi" id="syarat"
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-forest focus:ring-forest/20 cursor-pointer"
                           required>
                    <label for="syarat" class="text-xs text-gray-600 leading-relaxed cursor-pointer">
                        Saya menyetujui
                        <a href="#" class="text-forest font-semibold hover:underline">Syarat Transparansi</a>
                        &amp;
                        <a href="#" class="text-forest font-semibold hover:underline">Protokol Privasi</a>
                        Portal Desa Binangun. Data yang didaftarkan merupakan data resmi penyelenggaraan pemerintahan desa.
                    </label>
                </div>

                {{-- Submit --}}
                <button type="button" onclick="submitRegistrasi()"
                        class="w-full py-3.5 px-6 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all duration-200
                               shadow-lg shadow-forest/20 mt-2">
                    <span id="btn-text">Daftar Sekarang</span>
                    <span id="btn-loading" class="hidden">Memproses...</span>
                </button>
            </form>

            {{-- Link Login --}}
            <p class="text-center text-sm text-gray-500 mt-6">
                Sudah punya akun?
                <a href="{{ route('auth.login') }}" class="text-forest font-bold hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

{{-- Modal Registrasi Berhasil (dari session server) --}}
@if(session('registration_success'))
<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json(session('registration_success'));
    Swal.fire({
        icon: 'success',
        title: 'Registrasi Berhasil!',
        html: `
            <div class="text-left space-y-3 mt-2">
                <p class="text-sm text-gray-500">Akun administrator Anda telah berhasil dibuat. Silakan gunakan data berikut untuk masuk:</p>
                <div class="bg-gray-50 rounded-xl p-4 space-y-2 border border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 font-medium">Email</span>
                        <span class="text-gray-800 font-bold">${data.email}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500 font-medium">NIP</span>
                        <span class="text-gray-800 font-bold">${data.nip}</span>
                    </div>
                </div>
            </div>`,
        confirmButtonText: 'Masuk ke Portal',
        customClass: {
            confirmButton: 'bg-forest text-white font-bold px-8 py-3 rounded-xl text-sm hover:bg-forest-600',
        },
        buttonsStyling: false,
    }).then(() => {
        window.location = '{{ route('auth.login') }}';
    });
});
</script>
@endif

<script>
/* ---- Validasi & Submit AJAX ---- */
async function submitRegistrasi() {
    const btn     = document.querySelector('#btn-text');
    const loading = document.querySelector('#btn-loading');
    const form    = document.getElementById('register-form');

    // Validasi client-side
    const nip = document.getElementById('nip').value;
    if (!/^\d{13}$/.test(nip)) {
        Swal.fire({ icon: 'error', title: 'NIP Tidak Valid', text: 'NIP harus tepat 13 digit angka.', confirmButtonColor: '#1A362B' });
        return;
    }

    const pwd  = document.getElementById('password').value;
    const pwd2 = document.getElementById('password_confirmation').value;
    if (pwd !== pwd2) {
        Swal.fire({ icon: 'error', title: 'Kata Sandi Tidak Cocok', text: 'Pastikan konfirmasi kata sandi sama.', confirmButtonColor: '#1A362B' });
        return;
    }

    if (!document.getElementById('syarat').checked) {
        Swal.fire({ icon: 'warning', title: 'Persetujuan Diperlukan', text: 'Centang syarat transparansi untuk melanjutkan.', confirmButtonColor: '#1A362B' });
        return;
    }

    btn.classList.add('hidden');
    loading.classList.remove('hidden');

    const formData = new FormData(form);
    formData.append('_token', document.querySelector('[name=_token]').value);

    try {
        const res  = await fetch('{{ route('auth.register.store') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': formData.get('_token') },
            body: formData,
        });
        const json = await res.json();

        if (json.success) {
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                html: `
                    <div class="text-left space-y-3 mt-2">
                        <p class="text-sm text-gray-500">Akun Anda telah berhasil dibuat.</p>
                        <div class="bg-gray-50 rounded-xl p-4 space-y-2 border border-gray-100">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">Email</span>
                                <span class="font-bold text-gray-800">${json.data.email}</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-500">NIP</span>
                                <span class="font-bold text-gray-800">${json.data.nip}</span>
                            </div>
                        </div>
                    </div>`,
                confirmButtonText: 'Masuk ke Portal',
                customClass: { confirmButton: 'bg-forest text-white font-bold px-8 py-3 rounded-xl text-sm' },
                buttonsStyling: false,
            }).then(() => window.location = '{{ route('auth.login') }}');
        } else {
            const errs = json.errors ? Object.values(json.errors).flat().join('\n') : json.message;
            Swal.fire({ icon: 'error', title: 'Gagal Mendaftar', text: errs, confirmButtonColor: '#1A362B' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Periksa koneksi internet Anda.', confirmButtonColor: '#1A362B' });
    } finally {
        btn.classList.remove('hidden');
        loading.classList.add('hidden');
    }
}

/* ---- Counter NIP ---- */
document.getElementById('nip').addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 13);
    document.getElementById('nip-counter').textContent = `${this.value.length} / 13 digit`;
});

/* ---- Toggle Visibilitas Password ---- */
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>
