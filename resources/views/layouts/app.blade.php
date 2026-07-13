<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Desa Binangun</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS v4 via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        forest:  { DEFAULT: '#096b68', 50: '#E6F5F4', 100: '#CCE7E6', 200: '#99CFCD', 300: '#66B6B3', 400: '#3D9693', 500: '#096b68', 600: '#085F5C', 700: '#064E4B', 800: '#053C3A', 900: '#032B2A' },
                        sage:    { DEFAULT: '#87A996', 50: '#F2F7F5', 100: '#E6F0EB', 200: '#CCE0D8', 300: '#B3D1C4', 400: '#99C1B0', 500: '#87A996', 600: '#6C9480', 700: '#517F6A', 800: '#3A5B4C', 900: '#243730' },
                        mint:    { DEFAULT: '#CCE7D6', 50: '#F5FBF7', 100: '#EBF7EF', 200: '#D7EFE0', 300: '#CCE7D6', 400: '#A8D5BA', 500: '#84C39F' },
                    },
                    fontFamily: {
                        sans:    ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
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
        .font-display { font-weight: 700; }

        /* Scrollbar kustom hijau */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #F2F7F5; }
        ::-webkit-scrollbar-thumb { background: #87A996; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #096b68; }

        /* Sidebar transisi */
        #sidebar { transition: width 0.3s ease, opacity 0.3s ease, transform 0.3s ease; }
        #sidebar-overlay { transition: opacity 0.3s ease; }

        /* Desktop sidebar hide/show */
        @media (min-width: 1024px) {
            #sidebar.sidebar-hidden {
                width: 0 !important;
                min-width: 0 !important;
                opacity: 0;
                pointer-events: none;
                transform: translateX(-100%);
            }
        }

        /* Active nav item */
        .nav-item-active {
            background: #096b68;
            color: #EBF7EF !important;
            font-weight: 700;
        }
        .nav-item-active svg { color: #EBF7EF !important; }

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
        .swal2-confirm,
        .swal2-cancel {
            min-width: 120px;
            padding: 0.75rem 1.25rem !important;
            border-radius: 1rem !important;
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            text-transform: none !important;
        }
        .swal2-confirm {
            background-color: #096b68 !important;
            color: #ffffff !important;
            border: none !important;
        }
        .swal2-cancel {
            background-color: #ffffff !important;
            color: #096b68 !important;
            border: 1px solid #096b68 !important;
        }
        .swal2-actions {
            display: flex !important;
            justify-content: center !important;
            gap: 0.75rem !important;
            flex-wrap: wrap !important;
        }
        .swal2-title   { font-family: 'Plus Jakarta Sans', sans-serif !important; }
        .swal2-html-container { font-family: 'Plus Jakarta Sans', sans-serif !important; }

        /* Toast SweetAlert2 */
        .swal2-popup.swal2-toast { border-left: 4px solid #096b68 !important; }
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

<div id="main-layout" class="flex h-screen overflow-hidden transition-all duration-300 ease-in-out">

    {{-- ============================================================ --}}
    {{-- SIDEBAR NAVIGASI --}}
    {{-- ============================================================ --}}
    <aside id="sidebar"
           class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-white shadow-sidebar flex flex-col
                  -translate-x-full lg:translate-x-0 shrink-0">

        {{-- Logo & Brand --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-sage-100">
            <div class="w-12 h-12 flex items-center justify-center">
               <img src="/desa-binangun.png" alt="logo pemerintah desa binangun">
            </div>
            <div class="min-w-0">
                <p class="font-display text-sm font-semibold text-forest leading-tight">Desa Binangun</p>
                <p class="text-[10px] text-sage-600 font-medium tracking-wide uppercase mt-0.5">Portal Transparansi</p>
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

            @if(auth()->user()->isSuperAdmin())
            <div class="px-3 pt-4 pb-1">
                <span class="text-[9px] font-bold text-sage-500 uppercase tracking-widest">Sistem</span>
            </div>
            <x-nav-item route="users.index" icon="cog">Manajemen User</x-nav-item>
            <x-nav-item route="audit.index" icon="shield">Audit Log</x-nav-item>
            @endif
        </nav>

        {{-- Sidebar Footer User Info --}}
        <div class="mt-auto border-t border-sage-100/60 bg-forest-50/40 px-4 py-4 relative">
            <button id="sidebar-user-toggle" type="button" onclick="toggleSidebarUserMenu()"
                    class="w-full rounded-3xl bg-white/90 px-3 py-3 text-left shadow-sm transition hover:bg-sage-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-forest flex items-center justify-center shrink-0 shadow-sm">
                        <span class="text-white text-sm font-bold">{{ auth()->user()->initials }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-forest truncate">{{ auth()->user()->name }}</p>
                        <span class="text-[10px] text-sage-600 font-medium">{{ auth()->user()->role_label }}</span>
                    </div>
                    <svg class="w-4 h-4 text-sage-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
            </button>
            <div id="sidebar-user-menu" class="hidden bottom-5 absolute left-full  z-50 ml-3 w-64 overflow-hidden rounded-3xl border border-sage-200 bg-white shadow-card">
                <div class="border-b border-sage-100 px-4 py-3">
                    <p class="text-sm font-semibold text-forest truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[12px] text-sage-600">{{ auth()->user()->role_label }}</p>
                </div>
                <a href="{{ route('settings.index') }}" class="block px-4 py-3 text-sm text-forest hover:bg-sage-50">Pengaturan Akun</a>
                <a href="{{ route('help.index') }}" class="block px-4 py-3 text-sm text-forest hover:bg-sage-50">Pusat Bantuan</a>
                <button type="button" onclick="konfirmasiLogout()" class="w-full text-left px-4 py-3 text-sm text-red-600 hover:bg-red-50">Keluar</button>
            </div>
        </div>

        {{-- Hidden Logout Form --}}
        <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </aside>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT AREA --}}
    {{-- ============================================================ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Header Bar --}}
        <header class="shrink-0 bg-white border-b border-sage-100 px-4 sm:px-6 py-3 flex items-center justify-between shadow-sm z-20">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()"
                        class="lg:hidden p-2 rounded-lg hover:bg-forest-50 text-forest transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <button id="desktop-sidebar-toggle" type="button" onclick="toggleSidebarDesktop()"
                        class="hidden lg:inline-flex items-center justify-center  bg-white w-10 h-10 text-forest ">
                    <span id="desktop-sidebar-toggle-icon" class="inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left-close-icon lucide-panel-left-close">
                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                            <path d="M9 3v18"/>
                            <path d="m16 15-3-3 3-3"/>
                        </svg>
                    </span>
                </button>

                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-xs text-sage-500 hidden sm:inline">Portal Desa Binangun</span>
                    <svg class="w-3 h-3 text-sage-400 hidden sm:inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-sm font-semibold text-forest truncate">@yield('breadcrumb', 'Dashboard')</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 bg-forest-50 rounded-lg px-3 py-1.5">
                    <svg class="w-3.5 h-3.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs font-semibold text-forest" id="current-date">
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </div>
            </div>
        </header>

        {{-- Flash Message Notifications --}}
        @if(session('success_password'))
            <div id="flash-success" class="mx-4 sm:mx-6 mt-4 flex items-center gap-3 bg-forest-50 border border-forest-200 rounded-xl px-4 py-3">
                <svg class="w-5 h-5 text-forest shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <main class="flex-1 flex flex-col overflow-y-auto">
            <div class="flex-1 p-4 sm:p-6 lg:p-7 max-w-screen-2xl mx-auto w-full">
                @yield('content')
            </div>
            
            {{-- Footer global --}}
            <footer class="mt-auto  py-4 px-6 text-center text-xs text-sage-500">
                &copy; 2026 Desa Binangun Kec. Banyumas, Kabupaten Banyumas, Jawa Tengah. All rights reserved.
            </footer>
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

function toggleSidebarUserMenu() {
    const menu = document.getElementById('sidebar-user-menu');
    if (!menu) return;
    menu.classList.toggle('hidden');
}

document.addEventListener('click', function(event) {
    const menu = document.getElementById('sidebar-user-menu');
    const toggle = document.getElementById('sidebar-user-toggle');
    if (!menu || !toggle) return;
    if (!menu.classList.contains('hidden') && !menu.contains(event.target) && !toggle.contains(event.target)) {
        menu.classList.add('hidden');
    }
});

/* ---- Sidebar Toggle (Desktop) ---- */
function setSidebarDesktopState(isOpen) {
    const sidebar = document.getElementById('sidebar');
    const mainLayout = document.getElementById('main-layout');
    const toggleIcon = document.getElementById('desktop-sidebar-toggle-icon');

    if (!sidebar || !mainLayout || !toggleIcon) return;

    if (isOpen) {
        sidebar.classList.remove('sidebar-hidden');
        mainLayout.classList.remove('sidebar-closed');
        toggleIcon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left-close-icon lucide-panel-left-close">
                <rect width="18" height="18" x="3" y="3" rx="2"/>
                <path d="M9 3v18"/>
                <path d="m16 15-3-3 3-3"/>
            </svg>
        `;
    } else {
        sidebar.classList.add('sidebar-hidden');
        mainLayout.classList.add('sidebar-closed');
        toggleIcon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-right-close-icon lucide-panel-right-close">
                <rect width="18" height="18" x="3" y="3" rx="2"/>
                <path d="M15 3v18"/>
                <path d="m8 9 3 3-3 3"/>
            </svg>
        `;
    }
}

function toggleSidebarDesktop() {
    const isOpen = localStorage.getItem('sidebarDesktopOpen');
    const openState = isOpen === null ? true : isOpen === 'true';
    const nextState = !openState;
    setSidebarDesktopState(nextState);
    localStorage.setItem('sidebarDesktopOpen', String(nextState));
}

window.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem('sidebarDesktopOpen');
    const isOpen = saved === null ? true : saved === 'true';
    setSidebarDesktopState(isOpen);
});

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
