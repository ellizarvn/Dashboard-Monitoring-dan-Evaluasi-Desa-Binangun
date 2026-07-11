<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Administrator — Desa Binangun</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { DEFAULT: '#096b68', 50: '#E6F5F4', 100: '#CCE7E6', 500: '#096b68' },
                        sage: { DEFAULT: '#87A996', 200: '#CCE0D8', 400: '#99C1B0' },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        :root { color-scheme: light; }
        .font-display { font-weight: 800; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f7f4e8 0%, #eef6ef 45%, #f6efe2 100%);
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at top left, rgba(255,255,255,0.75), transparent 32%),
                radial-gradient(circle at bottom right, rgba(9,107,104,0.08), transparent 28%);
            pointer-events: none;
        }
        .forest-gradient {
            background: linear-gradient(135deg, #032a29 0%, #096b68 38%, #1a8f8b 72%, #2bb3b0 100%);
        }
        .forest-pattern {
            background-image: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.09) 0%, transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(255,255,255,0.05) 0%, transparent 40%),
                linear-gradient(135deg, rgba(255,255,255,0.02), rgba(255,255,255,0));
        }
        .input-field {
            width: 100%;
            padding: 0.75rem 0.95rem;
            border-radius: 0.95rem;
            border: 1px solid #dfe8e2;
            font-size: 0.9rem;
            font-weight: 500;
            outline: none;
            transition: all 0.2s ease;
            background: #fffdf9;
            color: #163126;
            font-family: 'Plus Jakarta Sans', sans-serif;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
        }
        .input-field:focus {
            border-color: #096b68;
            box-shadow: 0 0 0 4px rgba(9,107,104,0.08);
            background: white;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in { animation: slideIn 0.4s ease forwards; }
    </style>
</head>
<body class="min-h-screen">
<div class="flex min-h-screen items-stretch">
    <div class="flex flex-1 items-center justify-center p-4 sm:px-8 lg:px-10">
        <div class="w-full max-w-xl slide-in rounded-[32px] bg-white border border-gray-200/80 shadow-xl shadow-forest/10 p-10 max-h-[calc(100vh-3rem)]  overflow-y-auto">
            <div class="mb-5 text-center">
                <h2 class="font-display text-4xl text-forest">Daftar Akun</h2>
                <p class="mt-1 text-xs text-gray-700">Registrasi khusus administrator portal desa</p>
            </div>

            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                    <p class="mb-1 text-sm font-semibold text-red-700">Terjadi Kesalahan Input:</p>
                    <ul class="list-inside list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-xs text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="register-form" class="space-y-3">
                @csrf

                <div>
                    <label class="mb-1 block text-[11px] font-semibold  text-gray-600">Nama Lengkap</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="Masukkan nama lengkap Anda"
                           class="input-field @error('name') border-red-400 @enderror"
                           required>
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-gray-600">
                        NIP Administrator
                        <span class="font-normal text-sage-500">(13 digit)</span>
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

                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-gray-600">Alamat Email</label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email') }}"
                           placeholder="admin@desabinangun.id"
                           class="input-field @error('email') border-red-400 @enderror"
                           required>
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-gray-600">Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               placeholder="Min. 8 karakter (huruf besar, kecil, angka)"
                               class="input-field pr-12 @error('password') border-red-400 @enderror"
                               required>
                        <button type="button" onclick="togglePassword('password', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 transition-colors hover:text-forest">
                            <svg class="h-4 w-4" id="eye-password" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-[11px] font-semibold text-gray-600">Konfirmasi Kata Sandi</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               placeholder="Ulangi kata sandi"
                               class="input-field pr-12"
                               required>
                        <button type="button" onclick="togglePassword('password_confirmation', this)"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-gray-400 transition-colors hover:text-forest">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-start gap-2 pt-1.5">
                    <input type="checkbox" name="syarat_transparansi" id="syarat"
                           class="mt-0.5 h-4 w-4 rounded border-gray-300 text-forest focus:ring-forest/20 cursor-pointer"
                           required>
                    <label for="syarat" class="cursor-pointer text-[12px] leading-snug text-gray-600">
                        Saya menyetujui
                        <a href="#" class="font-semibold text-forest hover:underline">Syarat Transparansi</a>
                        &amp;
                        <a href="#" class="font-semibold text-forest hover:underline">Protokol Privasi</a>
                        Portal Desa Binangun. Data yang didaftarkan merupakan data resmi penyelenggaraan pemerintahan desa.
                    </label>
                </div>

                <button type="button" onclick="submitRegistrasi()"
                        class="mt-0.5 w-full rounded-2xl bg-forest px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-forest/20 transition-all duration-200 hover:bg-[#075956] active:scale-[0.98]">
                    <span id="btn-text">Daftar Sekarang</span>
                    <span id="btn-loading" class="hidden">Memproses...</span>
                </button>
            </form>

            <p class="mt-3 text-center text-[13px] text-gray-500">
                Sudah punya akun?
                <a href="{{ route('auth.login') }}" class="font-bold text-forest hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

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
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 space-y-2">
                    <div class="flex justify-between text-xs">
                        <span class="font-medium text-gray-500">Email</span>
                        <span class="font-bold text-gray-800">${data.email}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="font-medium text-gray-500">NIP</span>
                        <span class="font-bold text-gray-800">${data.nip}</span>
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

    const nip = document.getElementById('nip').value;
    if (!/^\d{13}$/.test(nip)) {
        Swal.fire({ icon: 'error', title: 'NIP Tidak Valid', text: 'NIP harus tepat 13 digit angka.', confirmButtonColor: '#096b68' });
        return;
    }

    const pwd  = document.getElementById('password').value;
    const pwd2 = document.getElementById('password_confirmation').value;
    if (pwd !== pwd2) {
        Swal.fire({ icon: 'error', title: 'Kata Sandi Tidak Cocok', text: 'Pastikan konfirmasi kata sandi sama.', confirmButtonColor: '#096b68' });
        return;
    }

    if (!document.getElementById('syarat').checked) {
        Swal.fire({ icon: 'warning', title: 'Persetujuan Diperlukan', text: 'Centang syarat transparansi untuk melanjutkan.', confirmButtonColor: '#096b68' });
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
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 space-y-2">
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
            Swal.fire({ icon: 'error', title: 'Gagal Mendaftar', text: errs, confirmButtonColor: '#096b68' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Periksa koneksi internet Anda.', confirmButtonColor: '#096b68' });
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
