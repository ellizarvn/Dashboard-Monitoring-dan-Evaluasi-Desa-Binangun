<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — Portal Desa Binangun</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest: { DEFAULT: '#096b68', 50: '#E6F5F4', 100: '#CCE7E6' },
                        sage: { DEFAULT: '#87A996', 200: '#CCE0D8', 500: '#87A996' },
                    },
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'sans-serif'], display: ['"Plus Jakarta Sans"', 'sans-serif'] }
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
            background: linear-gradient(135deg, #f8f4e8 0%, #eef6ef 45%, #f6efe2 100%);
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at top left, rgba(255,255,255,0.7), transparent 32%),
                radial-gradient(circle at bottom right, rgba(9,107,104,0.08), transparent 26%);
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
            padding: 0.95rem 1rem;
            border-radius: 0.95rem;
            border: 1px solid #dfe8e2;
            font-size: 0.95rem;
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
<body class="min-h-screen flex items-center justify-center  sm:p-6 lg:p-8 relative overflow-hidden">
        <div class="flex items-center justify-center px-4 py-8 sm:px-8 lg:px-10">
            <div class="w-full max-w-xl slide-in rounded-[32px] bg-white border border-gray-200/80 shadow-xl shadow-forest/10 p-8">
                <div class="text-center ">
                    <h1 class="font-display text-3xl text-forest">Selamat Datang</h1>
                    <p class="mt-2 text-sm text-gray-700">Masuk untuk mengakses portal administrasi Desa Binangun.</p>
                </div>

                @if($errors->any())
                    <div class="mt-6 flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 p-4">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p class="text-xs text-red-600">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.login.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label class="mb-2 block text-xs font-semibold text-gray-600">Email Administrator</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="admin@desabinangun.id"
                               class="input-field" required autofocus>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-semibold text-gray-600">Kata Sandi</label>
                        <div class="relative">
                            <input type="password" name="password" id="pwd"
                                   placeholder="Masukkan kata sandi Anda"
                                   class="input-field pr-12" required>
                            <button type="button"
                                    onclick="const i=document.getElementById('pwd'); i.type=i.type==='password'?'text':'password';"
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

                    <div class="flex items-center justify-between">
                        <label class="flex cursor-pointer items-center gap-2">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-forest focus:ring-forest/20">
                            <span class="text-xs font-medium text-gray-600">Tetap masuk</span>
                        </label>
                        <a href="#" class="text-xs font-semibold text-forest hover:underline">Atur Ulang Kredensial</a>
                    </div>

                    <button type="submit"
                            class="mt-1 w-full rounded-2xl bg-forest py-3.5 text-sm font-bold text-white shadow-lg shadow-forest/20 transition-all duration-200 hover:bg-[#075956] active:scale-[0.98]">
                        Masuk ke Portal
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="{{ route('auth.register') }}" class="font-bold text-forest hover:underline">Daftar sebagai Admin</a>
                </p>

                <p class="mt-3 text-center text-[11px] text-gray-400">
                    IP Anda: <span class="font-semibold">{{ request()->ip() }}</span> &mdash; Sesi ini dipantau & dicatat
                </p>
            </div>
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
