<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Portal Desa Binangun</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { DEFAULT: '#1A362B', 50: '#E6F4ED', 100: '#CCE7D6' },
                        sage:   { DEFAULT: '#87A996', 200: '#CCE0D8', 500: '#87A996' },
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], display: ['"DM Serif Display"', 'serif'] }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .forest-gradient { background: linear-gradient(135deg, #0A1F17 0%, #1A362B 40%, #2A5042 70%, #3D7059 100%); }
        .forest-pattern {
            background-image: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.06) 0%, transparent 40%),
                              radial-gradient(circle at 85% 75%, rgba(255,255,255,0.04) 0%, transparent 40%);
        }
        .input-field {
            width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #e5e7eb;
            font-size: 14px; font-weight: 500; outline: none; transition: all 0.2s;
            background: white; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .input-field:focus { border-color: #1A362B; box-shadow: 0 0 0 3px rgba(26,54,43,0.08); }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .slide-in { animation: slideIn 0.4s ease forwards; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-4">

    <div class="w-full max-w-sm slide-in">
        {{-- Logo & Brand --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-forest shadow-xl shadow-forest/30 mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <h1 class="font-display text-3xl text-forest">Selamat Datang</h1>
            <p class="text-gray-500 text-sm mt-1.5">Portal Administrasi Desa Binangun</p>
        </div>

        {{-- Card Form --}}
        <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 p-8 border border-gray-100">

            {{-- Error Validasi --}}
            @if($errors->any())
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
                    <svg class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-xs text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login.store') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Email Administrator</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="admin@desabinangun.id"
                           class="input-field" required autofocus>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Kata Sandi Keamanan</label>
                    <div class="relative">
                        <input type="password" name="password" id="pwd"
                               placeholder="Masukkan kata sandi Anda"
                               class="input-field pr-12" required>
                        <button type="button"
                                onclick="const i=document.getElementById('pwd'); i.type=i.type==='password'?'text':'password';"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-forest p-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember Me + Atur Ulang --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-forest focus:ring-forest/20">
                        <span class="text-xs text-gray-600 font-medium">Tetap masuk</span>
                    </label>
                    <a href="#" class="text-xs text-forest font-semibold hover:underline">Atur Ulang Kredensial</a>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full py-3.5 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all duration-200
                               shadow-lg shadow-forest/25 mt-1">
                    Masuk ke Portal
                </button>
            </form>
        </div>

        {{-- Daftar link --}}
        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('auth.register') }}" class="text-forest font-bold hover:underline">Daftar sebagai Admin</a>
        </p>

        {{-- Info --}}
        <p class="text-center text-[11px] text-gray-400 mt-3">
            IP Anda: <span class="font-semibold">{{ request()->ip() }}</span> &mdash; Sesi ini dipantau & dicatat
        </p>
    </div>

    @if(session('logged_out'))
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        Swal.fire({
            toast: true, position: 'top-end', icon: 'success',
            title: 'Berhasil Keluar',
            text: 'Anda telah logout dari sistem.',
            showConfirmButton: false, timer: 3500, timerProgressBar: true,
        });
    });
    </script>
    @endif
</body>
</html>
