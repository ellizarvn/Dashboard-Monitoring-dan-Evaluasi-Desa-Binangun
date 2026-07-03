<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 — Akses Ditolak | Desa Binangun</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{forest:{DEFAULT:'#096b68',50:'#E6F5F4'},sage:{DEFAULT:'#87A996'}},fontFamily:{sans:['"Plus Jakarta Sans"','sans-serif'],display:['"DM Serif Display"','serif']}}}}</script>
    <style>body{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="min-h-screen bg-forest-50 flex items-center justify-center p-6">
<div class="text-center max-w-sm">
    <div class="w-24 h-24 rounded-3xl bg-red-50 border-2 border-red-200 flex items-center justify-center mx-auto mb-6">
        <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <p class="text-6xl font-black text-forest mb-3">403</p>
    <h1 class="font-display text-2xl text-forest mb-3">Akses Ditolak</h1>
    <p class="text-sm text-gray-500 leading-relaxed mb-8">Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <a href="{{ url()->previous() }}" class="px-6 py-3 border border-sage text-sage font-semibold rounded-xl text-sm hover:bg-sage hover:text-white transition-all">← Kembali</a>
        <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-forest text-white font-bold rounded-xl text-sm hover:bg-forest-600 transition-all shadow-md shadow-forest/20">Dashboard</a>
    </div>
</div>
</body>
</html>
