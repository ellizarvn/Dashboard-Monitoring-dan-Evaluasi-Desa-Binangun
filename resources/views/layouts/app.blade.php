<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Desa Binangun</title>

    {{-- Google Fonts: Plus Jakarta Sans + DM Serif Display --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    {{-- Tailwind CSS v4 via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest:  { DEFAULT: '#1A362B', 50: '#E6F4ED', 100: '#CCE7D6', 200: '#99CFAC', 300: '#66B783', 400: '#3D965B', 500: '#1A362B', 600: '#162D24', 700: '#12251D', 800: '#0E1C16', 900: '#0A130F' },
                        sage:    { DEFAULT: '#87A996', 50: '#F2F7F5', 100: '#E6F0EB', 200: '#CCE0D8', 300: '#B3D1C4', 400: '#99C1B0', 500: '#87A996', 600: '#6C9480', 700: '#517F6A', 800: '#3A5B4C', 900: '#243730' },
                        mint:    { DEFAULT: '#CCE7D6', 50: '#F5FBF7', 100: '#EBF7EF', 200: '#D7EFE0', 300: '#CCE7D6', 400: '#A8D5BA', 500: '#84C39F' },
                    },
                    fontFamily: {
                        sans:    ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"DM Serif Display"', 'serif'],
                    },
                    boxShadow: {
                        'card': '0 1px 3px 0 rgba(26,54,43,0.08), 0 4px 16px -4px rgba(26,54,43,0.06)',
                        'card-hover': '0 4px 12px 0 rgba(26,54,43,0.12), 0 8px 32px -8px rgba(26,54,43,0.10)',
                        'sidebar': '4px 0 24px -4px rgba(26,54,43,0.15)',
                    }
                }
            }
        }
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Scrollbar kustom hijau */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #F2F7F5; }
        ::-webkit-scrollbar-thumb { background: #87A996; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #1A362B; }

        /* Sidebar transisi */
        #sidebar { transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }
        #sidebar-overlay { transition: opacity 0.3s ease; }

        /* Active nav item */
        .nav-item-active {
            background: linear-gradient(90deg, rgba(204,231,214,0.7) 0%, rgba(230,244,237,0.4) 100%);
            border-left: 4px solid #1A362B;
            color: #1A362B !important;
            font-weight: 700;
        }
        .nav-item-active svg { color: #1A362B !important; }

        /* Progress bar animasi */
        @keyframes progressFill {
            from { width: 0%; }
        }
        .progress-animated { animation: progressFill 1.2s cubic-bezier(0.4,0,0.2,1) forwards; }

        /* Radial chart container */
        .radial-ring {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .radial-ring canvas { transform: rotate(-90deg); }

        /* Card subtle pattern */
        .card-pattern {
            background-image: radial-gradient(circle at 20% 50%, rgba(135,169,150,0.05) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(26,54,43,0.03) 0%, transparent 40%);
        }

        /* Badge animasi */
        @keyframes badgePulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .badge-pulse { animation: badgePulse 2s ease-in-out infinite; }

        /* SweetAlert2 kustom */
        .swal2-confirm { background-color: #1A362B !important; }
        .swal2-cancel  { background-color: #ffffff !important; color: #1A362B !important; border: 1px solid #1A362B !important; }
        .swal2-title   { font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .swal2-html-container { font-family: 'Plus Jakarta Sans', sans-serif !important; }

        /* Toast SweetAlert2 */
        .swal2-popup.swal2-toast { border-left: 4px solid #1A362B !important; }
    </style>

    @stack('head-scripts')
</head>
<body class="h-full bg-forest-50/30 text-gray-800 antialiased">

{{-- ============================================================ --}}
{{-- SIDEBAR OVERLAY (Mobile) --}}
{{-- ============================================================ --}}
<div id="sidebar-overlay"
     class="fixed inset-0 bg-forest-900/50 z-30 hidden opacity-0 lg:hidden"
     onclick="closeSidebar()">
</div>

<div id="main-layout" class="flex h-screen overflow-hidden">

    {{-- ============================================================ --}}
    {{-- SIDEBAR NAVIGASI --}}
    {{-- ============================================================ --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white shadow-sidebar flex flex-col
                  -translate-x-full lg:translate-x-0 flex-shrink-0">

        {{-- Logo & Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-sage-100">
            <div class="w-10 h-10 rounded-xl bg-forest flex items-center justify-center flex-shrink-0 shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="font-display text-sm font-semibold text-forest leading-tight">Desa Binangun</p>
                <p class="text-[10px] text-sage-600 font-medium tracking-wide uppercase mt-0.5">Portal Transparansi</p>
            </div>
        </div>

        {{-- User Info Mini --}}
        <div class="px-4 py-3 border-b border-sage-100/60 bg-forest-50/40">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-forest flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">{{ auth()->user()->initials }}</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-forest truncate">{{ auth()->user()->name }}</p>
                    <span class="text-[10px] text-sage-600 font-medium">{{ auth()->user()->role_label }}</span>
                </div>
            </div>
        </div>

        {{-- Navigasi Utama --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

            {{-- Dashboard --}}
            <x-nav-item route="dashboard" icon="chart-bar">Dashboard Utama</x-nav-item>

            {{-- Divider Label --}}
            <div class="px-3 pt-4 pb-1">
                <span class="text-[9px] font-bold text-sage-500 uppercase tracking-widest">Manajemen OKR</span>
            </div>

            <x-nav-item route="target.index" icon="target">Target Tahunan</x-nav-item>
            <x-nav-item route="okr1.index" icon="users">OKR 1 — Partisipasi</x-nav-item>
            <x-nav-item route="okr2.index" icon="currency">OKR 2 — Ekonomi BUMDes</x-nav-item>
            <x-nav-item route="okr3.index" icon="academic">OKR 3 — Kapasitas SDM</x-nav-item>

            <div class="px-3 pt-4 pb-1">
                <span class="text-[9px] font-bold text-sage-500 uppercase tracking-widest">Monitoring</span>
            </div>

            <x-nav-item route="programs.index" icon="clipboard">Program Desa</x-nav-item>
            <x-nav-item route="reports.index" icon="document">Laporan Desa</x-nav-item>

            @if(in_array(auth()->user()->role, ['admin','kepala_desa']))
            <x-nav-item route="audit.index" icon="shield">Audit Log</x-nav-item>
            @endif

            <div class="px-3 pt-4 pb-1">
                <span class="text-[9px] font-bold text-sage-500 uppercase tracking-widest">Lainnya</span>
            </div>

            <x-nav-item route="settings.index" icon="cog">Pengaturan Akun</x-nav-item>
            <x-nav-item route="help.index" icon="question">Pusat Bantuan</x-nav-item>
        </nav>

        {{-- Tombol Logout di Bawah --}}
        <div class="px-3 py-4 border-t border-sage-100">
            <button onclick="konfirmasiLogout()"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-600 hover:bg-red-50
                           transition-all duration-200 group text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span>Keluar</span>
            </button>

            {{-- Form logout tersembunyi --}}
            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Header Bar --}}
        <header class="flex-shrink-0 bg-white border-b border-sage-100 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm z-20">
            {{-- Hamburger (Mobile) --}}
            <button onclick="toggleSidebar()"
                    class="lg:hidden p-2 rounded-lg hover:bg-forest-50 text-forest transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Breadcrumb / Page Title --}}
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-xs text-sage-500 hidden sm:inline">Portal Desa Binangun</span>
                <svg class="w-3 h-3 text-sage-400 hidden sm:inline flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-xs font-semibold text-forest truncate">@yield('breadcrumb', 'Dashboard')</span>
            </div>

            {{-- Right: Tanggal & Notifikasi --}}
            <div class="flex items-center gap-3">
                {{-- Tanggal Aktif --}}
                <div class="hidden sm:flex items-center gap-2 bg-forest-50 rounded-lg px-3 py-1.5">
                    <svg class="w-3.5 h-3.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-semibold text-forest" id="current-date">
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </div>

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-full bg-forest flex items-center justify-center flex-shrink-0 cursor-pointer"
                     onclick="window.location='{{ route('settings.index') }}'">
                    <span class="text-white text-xs font-bold">{{ auth()->user()->initials }}</span>
                </div>
            </div>
        </header>

        {{-- Flash Message Notifications --}}
        @if(session('success_password'))
            <div id="flash-success" class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 bg-forest-50 border border-forest-200 rounded-xl px-4 py-3">
                <svg class="w-5 h-5 text-forest flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-forest">{{ session('success_password') }}</p>
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-sage-500 hover:text-forest">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto">
            <div class="p-4 sm:p-6 lg:p-7 max-w-screen-2xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

{{-- ============================================================ --}}
{{-- GLOBAL JAVASCRIPT --}}
{{-- ============================================================ --}}
<script>
/* ---- Sidebar Toggle (Mobile) ---- */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const isOpen  = !sidebar.classList.contains('-translate-x-full');

    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden', 'opacity-0');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.remove('opacity-0'), 10);
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('opacity-0');
    setTimeout(() => overlay.classList.add('hidden'), 300);
}

/* ---- Modal Konfirmasi Logout ---- */
function konfirmasiLogout() {
    Swal.fire({
        title: 'Keluar dari Sistem?',
        html: '<p class="text-sm text-gray-500 mt-1">Anda akan keluar dari sesi aktif ini. Pastikan semua data telah disimpan sebelum melanjutkan.</p>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            confirmButton: 'swal2-confirm',
            cancelButton:  'swal2-cancel',
        },
        buttonsStyling: false,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}

/* ---- Toast Notifikasi Global ---- */
window.showToast = function(message, type = 'success') {
    const icons    = { success: 'success', error: 'error', warning: 'warning', info: 'info' };
    const titles   = { success: 'Berhasil', error: 'Gagal', warning: 'Perhatian', info: 'Informasi' };

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: icons[type] || 'info',
        title: titles[type] || 'Info',
        text: message,
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        customClass: { popup: 'swal2-toast font-sans text-sm' },
    });
};

/* ---- Modal Sukses Tengah Layar ---- */
window.showSuccessModal = function(title, message, timestamp = null) {
    const timestampHtml = timestamp
        ? `<div class="mt-3 inline-flex items-center gap-2 bg-forest-50 rounded-lg px-3 py-1.5">
               <svg class="w-3.5 h-3.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
               </svg>
               <span class="text-xs font-semibold text-forest">${timestamp}</span>
           </div>`
        : '';

    Swal.fire({
        icon: 'success',
        title: title,
        html: `<p class="text-sm text-gray-500">${message}</p>
               ${timestampHtml}
               <div class="mt-2">
                   <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                       <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                       </svg>
                       Tervalidasi
                   </span>
               </div>`,
        confirmButtonText: 'Oke, Mengerti',
        customClass: {
            confirmButton: 'swal2-confirm px-8 py-2.5 rounded-xl text-sm font-semibold text-white',
            title: 'text-forest font-display',
        },
        buttonsStyling: false,
    });
};

/* ---- CSRF helper untuk Fetch API ---- */
window.csrfToken = document.querySelector('meta[name="csrf-token"]').content;
window.apiFetch = async function(url, options = {}) {
    const defaults = {
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    };

    const merged = { ...defaults, ...options };
    if (options.headers) {
        merged.headers = { ...defaults.headers, ...options.headers };
    }

    try {
        const res = await fetch(url, merged);
        const json = await res.json();
        return { ok: res.ok, status: res.status, data: json };
    } catch (err) {
        console.error('apiFetch error:', err);
        return { ok: false, status: 0, data: { message: 'Koneksi gagal. Periksa jaringan Anda.' } };
    }
};

/* ---- Aktifkan flash toast dari sesi server jika ada ---- */
@if(session('logged_out'))
    document.addEventListener('DOMContentLoaded', () => {
        showToast('Anda telah berhasil keluar dari sistem.', 'info');
    });
@endif
</script>

{{-- Komponen NavItem (Blade Component) --}}
@stack('scripts')
</body>
</html>
