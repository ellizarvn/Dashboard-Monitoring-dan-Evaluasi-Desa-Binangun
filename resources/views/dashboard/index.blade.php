@extends('layouts.app')
@section('title', 'Dashboard Utama')
@section('breadcrumb', 'Dashboard Utama')

@section('content')
{{-- ============================================================ --}}
{{-- HEADER DASHBOARD: Judul + Filter Tahun/Bulan --}}
{{-- ============================================================ --}}
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Ringkasan Kinerja Desa</h1>
        <p class="text-sm text-sage-600 mt-0.5">Monitoring dan Evaluasi Program — Desa Binangun</p>
    </div>
    {{-- Filter Tahun & Bulan --}}
    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 flex-shrink-0">
        <select name="month"
                class="text-xs font-semibold border border-sage-200 rounded-xl px-3 py-2 bg-white text-forest
                       focus:outline-none focus:ring-2 focus:ring-forest/10 focus:border-forest cursor-pointer"
                onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $val => $label)
                <option value="{{ $val }}" {{ $month == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="year"
                class="text-xs font-semibold border border-sage-200 rounded-xl px-3 py-2 bg-white text-forest
                       focus:outline-none focus:ring-2 focus:ring-forest/10 focus:border-forest cursor-pointer"
                onchange="this.form.submit()">
            @foreach($tahunOptions as $y)
                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
    </form>
</div>

{{-- ============================================================ --}}
{{-- TOP CARDS: 5 Matriks Kinerja Utama --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-7">

    {{-- Card 1: Partisipasi Masyarakat --}}
    @php $partCard = $topCards['partisipasi']; @endphp
    <div class="bg-white rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-shadow duration-300 card-pattern border border-sage-100/60">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            @if($partCard['tren'] >= 0)
                <span class="flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                    +{{ $partCard['tren'] }}%
                </span>
            @else
                <span class="flex items-center gap-1 text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-lg">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    {{ $partCard['tren'] }}%
                </span>
            @endif
        </div>
        <p class="text-2xl font-black text-forest">{{ number_format($partCard['nilai'], 1) }}%</p>
        <p class="text-xs font-semibold text-gray-700 mt-0.5">Partisipasi Masyarakat</p>
        <div class="mt-3">
            <div class="flex justify-between text-[10px] text-gray-400 font-medium mb-1">
                <span>Capaian</span><span>Target: {{ $partCard['target'] }}%</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-forest rounded-full progress-animated"
                     style="width: {{ min(($partCard['nilai']/$partCard['target'])*100, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Card 2: Kegiatan Terlaksana --}}
    <div class="bg-white rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-shadow duration-300 card-pattern border border-sage-100/60">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-sage-600 bg-sage-50 px-2 py-1 rounded-lg">{{ $trenKegiatan }}%</span>
        </div>
        <p class="text-2xl font-black text-forest">{{ $kegiatanTerlaksana }}</p>
        <p class="text-xs font-semibold text-gray-700 mt-0.5">Kegiatan Terlaksana</p>
        <div class="mt-3">
            <div class="flex justify-between text-[10px] text-gray-400 font-medium mb-1">
                <span>Selesai</span><span>Target: {{ $target?->target_total_kegiatan ?? '—' }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full {{ $trenKegiatan >= 80 ? 'bg-forest' : ($trenKegiatan >= 50 ? 'bg-sage' : 'bg-amber-400') }} rounded-full progress-animated"
                     style="width: {{ min($trenKegiatan, 100) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Card 3: Omzet BUMDes --}}
    @php $omzetCard = $topCards['omzet_bumdes']; @endphp
    <div class="bg-white rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-shadow duration-300 card-pattern border border-sage-100/60">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-forest">{{ $omzetCard['formatted'] }}</p>
        <p class="text-xs font-semibold text-gray-700 mt-0.5">Omzet BUMDes</p>
        <div class="mt-3">
            @php $targetFmt = $omzetCard['target'] >= 1000000 ? 'Rp '.number_format($omzetCard['target']/1000000,0).'jt' : '—'; @endphp
            <div class="flex justify-between text-[10px] text-gray-400 font-medium mb-1">
                <span>Realisasi</span><span>Target: {{ $targetFmt }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                @php $pct = $omzetCard['target'] > 0 ? min(($omzetCard['nilai']/$omzetCard['target'])*100,100) : 0 @endphp
                <div class="h-full bg-forest rounded-full progress-animated" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Card 4: Laba Bersih --}}
    @php $labaCard = $topCards['laba_bersih']; @endphp
    <div class="bg-white rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-shadow duration-300 card-pattern border border-sage-100/60">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-forest">{{ $labaCard['formatted'] }}</p>
        <p class="text-xs font-semibold text-gray-700 mt-0.5">Laba Bersih BUMDes</p>
        <div class="mt-3">
            @php $targetLabaFmt = $labaCard['target'] >= 1000000 ? 'Rp '.number_format($labaCard['target']/1000000,0).'jt' : '—'; @endphp
            <div class="flex justify-between text-[10px] text-gray-400 font-medium mb-1">
                <span>Realisasi</span><span>Target: {{ $targetLabaFmt }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                @php $pctLaba = $labaCard['target'] > 0 ? min(($labaCard['nilai']/$labaCard['target'])*100,100) : 0 @endphp
                <div class="h-full {{ $pctLaba >= 80 ? 'bg-forest' : 'bg-amber-400' }} rounded-full progress-animated"
                     style="width: {{ $pctLaba }}%"></div>
            </div>
        </div>
    </div>

    {{-- Card 5: Kontribusi PADes --}}
    @php $padesCard = $topCards['kontribusi_pades']; @endphp
    <div class="bg-white rounded-2xl p-5 shadow-card hover:shadow-card-hover transition-shadow duration-300 card-pattern border border-sage-100/60">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                +15%
            </span>
        </div>
        <p class="text-2xl font-black text-forest">{{ $padesCard['formatted'] }}</p>
        <p class="text-xs font-semibold text-gray-700 mt-0.5">Kontribusi PADes</p>
        <div class="mt-3">
            @php $targetPadesFmt = $padesCard['target'] >= 1000000 ? 'Rp '.number_format($padesCard['target']/1000000,0).'jt' : '—'; @endphp
            <div class="flex justify-between text-[10px] text-gray-400 font-medium mb-1">
                <span>Realisasi</span><span>Target: {{ $targetPadesFmt }}</span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                @php $pctPades = $padesCard['target'] > 0 ? min(($padesCard['nilai']/$padesCard['target'])*100,100) : 0 @endphp
                <div class="h-full bg-forest rounded-full progress-animated" style="width: {{ $pctPades }}%"></div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- SEKSI OKR PROGRESS: 3 Radial Charts --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6 mb-7 card-pattern">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-bold text-forest text-base">Progress Capaian OKR {{ $year }}</h2>
            <p class="text-xs text-sage-600 mt-0.5">Ringkasan agregat 3 pilar OKR desa</p>
        </div>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full
            {{ $okrSummary['total_capaian'] >= 80 ? 'bg-green-50 text-green-700' : ($okrSummary['total_capaian'] >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
            Total: {{ $okrSummary['total_capaian'] }}%
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        @foreach([
            ['okr1', 'OKR 1', 'Partisipasi & Kegiatan', $okrSummary['okr1']['capaian_persen'], $okrSummary['okr1']['status'],
             [['Partisipasi Masyarakat', $okrSummary['okr1']['avg_partisipasi'] >= $okrSummary['okr1']['target'] * 0.9],
              ['Kehadiran Musyawarah', true]]],
            ['okr2', 'OKR 2', 'Ekonomi BUMDes', $okrSummary['okr2']['avg_capaian'], $okrSummary['okr2']['status'],
             [['Target Omzet BUMDes', $okrSummary['okr2']['capaian_omzet'] >= 80],
              ['Laba Bersih', $okrSummary['okr2']['capaian_laba'] >= 80],
              ['Kontribusi PADes', $okrSummary['okr2']['capaian_pades'] >= 100]]],
            ['okr3', 'OKR 3', 'Kapasitas SDM', $okrSummary['okr3']['capaian_persen'], $okrSummary['okr3']['status'],
             [['Staf Terlatih', isset($okrSummary['okr3']['skor_staf_persen']) && $okrSummary['okr3']['skor_staf_persen'] >= 75],
              ['Skor Kompetensi', isset($okrSummary['okr3']['skor_kompetensi']) && $okrSummary['okr3']['skor_kompetensi'] >= 70],
              ['Keaktifan Kinerja', isset($okrSummary['okr3']['skor_keaktifan']) && $okrSummary['okr3']['skor_keaktifan'] >= 80]]],
        ] as [$key, $label, $sublabel, $capaian, $status, $indicators])
        <div class="flex flex-col sm:flex-row items-center gap-5 p-5 bg-forest-50/40 rounded-2xl border border-sage-100/60">
            {{-- Radial Chart --}}
            <div class="relative flex-shrink-0">
                <canvas id="chart-{{ $key }}" width="90" height="90"
                        data-value="{{ $capaian }}"
                        data-color="{{ $capaian >= 80 ? '#1A362B' : ($capaian >= 60 ? '#87A996' : '#f59e0b') }}">
                </canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-sm font-black text-forest leading-none">{{ round($capaian) }}%</span>
                </div>
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="font-bold text-forest text-sm">{{ $label }}</p>
                <p class="text-[11px] text-sage-600 mb-2">{{ $sublabel }}</p>

                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full mb-3
                    {{ $status === 'ON TRACK' ? 'bg-green-100 text-green-700' : ($status === 'AT RISK' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600') }}">
                    {{ $status }}
                </span>

                <div class="space-y-1.5">
                    @foreach($indicators as [$indLabel, $indMet])
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0
                            {{ $indMet ? 'bg-green-100' : 'bg-gray-100' }}">
                            <svg class="w-2.5 h-2.5 {{ $indMet ? 'text-green-600' : 'text-gray-400' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($indMet)
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4m0 4h.01"/>
                                @endif
                            </svg>
                        </div>
                        <span class="text-[11px] text-gray-600 truncate">{{ $indLabel }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ============================================================ --}}
{{-- GRAFIK ANALITIK --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-7">

    {{-- Line Chart: Tren OKR Partisipasi --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-forest text-sm">Tren Capaian Partisipasi {{ $year }}</h3>
                <p class="text-[11px] text-sage-600 mt-0.5">Persentase kehadiran bulanan vs. target</p>
            </div>
            <div class="flex items-center gap-4 text-[10px] font-semibold text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-forest rounded inline-block"></span>Realisasi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-0.5 bg-sage-300 rounded inline-block border-dashed border-t"></span>Target</span>
            </div>
        </div>
        <div class="h-52">
            <canvas id="lineChart" data-tren="{{ json_encode($trenPartisipasi) }}"
                    data-target="{{ $target?->target_partisipasi_persen ?? 90 }}"></canvas>
        </div>
    </div>

    {{-- Donut Chart: Status Program --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <div class="mb-5">
            <h3 class="font-bold text-forest text-sm">Status Program Desa</h3>
            <p class="text-[11px] text-sage-600 mt-0.5">Distribusi {{ $distribusiProgram['total'] }} program</p>
        </div>
        <div class="h-36 flex items-center justify-center">
            <canvas id="donutChart" data-values="{{ json_encode([$distribusiProgram['AKTIF'], $distribusiProgram['PENDING'], $distribusiProgram['SELESAI']]) }}"></canvas>
        </div>
        <div class="mt-4 space-y-2">
            @foreach([
                ['AKTIF', $distribusiProgram['AKTIF'], '#1A362B'],
                ['PENDING', $distribusiProgram['PENDING'], '#87A996'],
                ['SELESAI', $distribusiProgram['SELESAI'], '#CCE7D6'],
            ] as [$label, $val, $color])
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-sm" style="background: {{ $color }}"></div>
                    <span class="text-xs text-gray-600 font-medium">{{ $label }}</span>
                </div>
                <span class="text-xs font-bold text-forest">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Bar Chart + Feed Aktivitas --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Bar Chart: Target vs Realisasi Bulanan --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-forest text-sm">Target vs Realisasi Omzet BUMDes</h3>
                <p class="text-[11px] text-sage-600 mt-0.5">Perbandingan bulanan tahun {{ $year }}</p>
            </div>
            <div class="flex items-center gap-4 text-[10px] font-semibold text-gray-500">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-forest inline-block"></span>Realisasi</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-sage-200 inline-block"></span>Target</span>
            </div>
        </div>
        <div class="h-52">
            <canvas id="barChart" data-bar="{{ json_encode($barChartData) }}"></canvas>
        </div>
    </div>

    {{-- Feed Aktivitas Terbaru --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-forest text-sm">Aktivitas Terbaru</h3>
            <a href="{{ route('audit.index') }}" class="text-[11px] text-forest font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3 overflow-y-auto max-h-64">
            @forelse($feedAktivitas as $log)
            <div class="flex items-start gap-3">
                <div class="w-7 h-7 rounded-full bg-forest-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-[9px] font-bold text-forest">{{ $log->user?->initials ?? 'SY' }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] font-semibold text-gray-800 truncate">{{ $log->user?->name ?? 'Sistem' }}</p>
                    <p class="text-[11px] text-gray-500 leading-snug line-clamp-2">{{ $log->description }}</p>
                    <p class="text-[10px] text-sage-500 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex-shrink-0">
                    <span class="inline-block w-1.5 h-1.5 rounded-full {{ $log->status === 'BERHASIL' ? 'bg-green-400' : 'bg-red-400' }}"></span>
                </div>
            </div>
            @empty
            <p class="text-xs text-sage-500 text-center py-4">Belum ada aktivitas hari ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---- Defaults Chart.js ----
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#87A996';

    // ---- Radial Charts (OKR 1,2,3) ----
    document.querySelectorAll('[id^="chart-okr"]').forEach(canvas => {
        const val   = parseFloat(canvas.dataset.value) || 0;
        const color = canvas.dataset.color || '#1A362B';
        new Chart(canvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [val, 100 - val],
                    backgroundColor: [color, '#F2F7F5'],
                    borderWidth: 0,
                    borderRadius: 4,
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                animation: { animateRotate: true, duration: 1200 },
            }
        });
    });

    // ---- Line Chart: Tren Partisipasi ----
    const lineCanvas = document.getElementById('lineChart');
    if (lineCanvas) {
        const tren   = JSON.parse(lineCanvas.dataset.tren || '{}');
        const target = parseFloat(lineCanvas.dataset.target) || 90;
        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels: tren.labels || [],
                datasets: [
                    {
                        label: 'Realisasi (%)',
                        data: tren.values || [],
                        borderColor: '#1A362B',
                        backgroundColor: 'rgba(26,54,43,0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#1A362B',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Target (%)',
                        data: (tren.labels || []).map(() => target),
                        borderColor: '#87A996',
                        borderWidth: 1.5,
                        borderDash: [5, 4],
                        pointRadius: 0,
                        tension: 0,
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y}%`
                        }
                    }
                },
                scales: {
                    y: {
                        min: 60, max: 100,
                        grid: { color: 'rgba(135,169,150,0.1)' },
                        ticks: { callback: v => v + '%' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ---- Bar Chart: Target vs Realisasi ----
    const barCanvas = document.getElementById('barChart');
    if (barCanvas) {
        const bar = JSON.parse(barCanvas.dataset.bar || '{}');
        new Chart(barCanvas, {
            type: 'bar',
            data: {
                labels: bar.labels || [],
                datasets: [
                    {
                        label: 'Realisasi',
                        data: bar.actual || [],
                        backgroundColor: '#1A362B',
                        borderRadius: 6,
                        barThickness: 14,
                    },
                    {
                        label: 'Target',
                        data: bar.targets || [],
                        backgroundColor: '#CCE7D6',
                        borderRadius: 6,
                        barThickness: 14,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const v = ctx.parsed.y;
                                return ` ${ctx.dataset.label}: Rp ${(v/1000000).toFixed(1)}jt`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(135,169,150,0.1)' },
                        ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(0) + 'jt' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // ---- Donut Chart: Status Program ----
    const donutCanvas = document.getElementById('donutChart');
    if (donutCanvas) {
        const values = JSON.parse(donutCanvas.dataset.values || '[0,0,0]');
        new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Aktif', 'Pending', 'Selesai'],
                datasets: [{
                    data: values,
                    backgroundColor: ['#1A362B', '#87A996', '#CCE7D6'],
                    borderWidth: 0,
                    borderRadius: 3,
                }]
            },
            options: {
                cutout: '70%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} program` } }
                }
            }
        });
    }
});
</script>
@endpush
