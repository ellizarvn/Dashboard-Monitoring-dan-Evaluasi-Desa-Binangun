@extends('layouts.app')
@section('title', 'OKR 3 — Kapasitas SDM')
@section('breadcrumb', 'OKR 3 — Kapasitas SDM')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">OKR 3 — Peningkatan Kapasitas SDM</h1>
        <p class="text-sm text-sage-600 mt-0.5">Monitoring kompetensi dan keaktifan perangkat desa</p>
    </div>
    @if(isset($capaian['tren_persen']) && $capaian['tren_persen'] != 0)
    <span class="flex items-center gap-1.5 text-sm font-bold px-3 py-1.5 rounded-full
        {{ $capaian['tren_persen'] > 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="{{ $capaian['tren_persen'] > 0 ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/>
        </svg>
        {{ $capaian['tren_persen'] > 0 ? '+' : '' }}{{ $capaian['tren_persen'] }}% vs bulan lalu
    </span>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ============================================================ --}}
    {{-- FORM INPUT SDM (Kiri) --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        @if(auth()->user()->canMutateData())
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">Input Data Kapasitas SDM</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Periode Tanggal</label>
                    <input type="date" id="sdm-date" value="{{ now()->format('Y-m-d') }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Total Perangkat Desa</label>
                        <input type="number" id="sdm-perangkat" min="1"
                               value="{{ $capaian['latest']?->total_perangkat ?? 30 }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Staf Terlatih</label>
                        <input type="number" id="sdm-staf" min="0"
                               value="{{ $capaian['latest']?->total_staf_terlatih ?? 26 }}"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Rata-rata Skor Kompetensi
                        <span class="text-sage-500 font-normal">(skala 0–10)</span>
                    </label>
                    <input type="number" id="sdm-kompetensi" step="0.1" min="0" max="10"
                           value="{{ $capaian['latest']?->avg_competency_score ?? 8.0 }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Persentase Keaktifan Kinerja
                        <span class="text-sage-500 font-normal">(%)</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="sdm-keaktifan" step="0.01" min="0" max="100"
                               value="{{ $capaian['latest']?->keaktifan_kinerja_persen ?? 87.5 }}"
                               class="w-full px-4 py-2.5 pr-10 rounded-xl border border-gray-200 text-sm font-semibold
                                      focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">%</span>
                    </div>
                </div>
                <button onclick="simpanSdm()"
                        class="w-full py-3 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                    Simpan Data SDM
                </button>
            </div>
        </div>
        @endif

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-sage-100/60">
                <h2 class="font-bold text-forest text-sm">Riwayat Data SDM {{ now()->year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-forest-50/30 border-b border-sage-100/60">
                            <th class="text-left px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Periode</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Staf</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Kompetensi</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Keaktifan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sage-50">
                        @forelse($history as $row)
                        <tr class="hover:bg-forest-50/20 transition-colors">
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $row->period_date->format('M Y') }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-600">{{ $row->total_staf_terlatih }}/{{ $row->total_perangkat }}</td>
                            <td class="px-4 py-3 text-right font-black text-forest">{{ $row->avg_competency_score }}/10</td>
                            <td class="px-4 py-3 text-right font-black text-forest">{{ $row->keaktifan_kinerja_persen }}%</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">Belum ada data SDM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- 3 PROGRESS CIRCLES (Kanan) --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        {{-- Alert Tren --}}
        @if(isset($capaian['tren_persen']))
        <div class="flex items-center gap-3 p-4 rounded-xl border
            {{ $capaian['tren_persen'] > 0 ? 'bg-green-50 border-green-200' : ($capaian['tren_persen'] < 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
            <svg class="w-5 h-5 {{ $capaian['tren_persen'] > 0 ? 'text-green-500' : ($capaian['tren_persen'] < 0 ? 'text-red-500' : 'text-gray-400') }} flex-shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="{{ $capaian['tren_persen'] > 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"/>
            </svg>
            <div>
                <p class="text-sm font-bold {{ $capaian['tren_persen'] > 0 ? 'text-green-700' : ($capaian['tren_persen'] < 0 ? 'text-red-700' : 'text-gray-700') }}">
                    {{ $capaian['tren_persen'] > 0 ? '+' : '' }}{{ $capaian['tren_persen'] }}% dibanding bulan lalu
                </p>
                <p class="text-xs {{ $capaian['tren_persen'] > 0 ? 'text-green-600' : 'text-red-600' }} mt-0.5">
                    {{ $capaian['tren_persen'] > 0 ? 'Kapasitas SDM terus meningkat. Pertahankan momentum ini.' : 'Perlu perhatian lebih pada program pelatihan.' }}
                </p>
            </div>
        </div>
        @endif

        {{-- 3 Radial Progress Circles --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-6">Analitik Kapasitas SDM Real-Time</h2>
            <div class="grid grid-cols-3 gap-4">
                @foreach([
                    ['Staf Terlatih', $capaian['skor_staf_persen'] ?? 0, 'chart-sdm-staf'],
                    ['Skor Kompetensi', $capaian['skor_kompetensi'] ?? 0, 'chart-sdm-komp'],
                    ['Keaktifan Kinerja', $capaian['skor_keaktifan'] ?? 0, 'chart-sdm-aktif'],
                ] as [$lbl, $val, $chartId])
                <div class="flex flex-col items-center gap-3">
                    <div class="relative">
                        <canvas id="{{ $chartId }}" width="90" height="90"
                                data-value="{{ round($val, 1) }}"
                                data-color="{{ $val >= 75 ? '#096b68' : ($val >= 60 ? '#87A996' : '#f59e0b') }}">
                        </canvas>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-black text-forest">{{ round($val) }}%</span>
                        </div>
                    </div>
                    <p class="text-[10px] font-bold text-gray-600 text-center leading-tight">{{ $lbl }}</p>
                </div>
                @endforeach
            </div>

            {{-- Data Detail --}}
            @if(isset($capaian['latest']))
            <div class="mt-6 pt-5 border-t border-sage-100 space-y-3">
                @php $latest = $capaian['latest']; @endphp
                @foreach([
                    ['Total Perangkat Aktif', $latest->total_perangkat . ' orang'],
                    ['Staf Telah Dilatih', $latest->total_staf_terlatih . ' orang (' . round(($latest->total_staf_terlatih/$latest->total_perangkat)*100,1) . '%)'],
                    ['Rata-rata Skor', $latest->avg_competency_score . ' / 10'],
                    ['Keaktifan Kinerja', $latest->keaktifan_kinerja_persen . '%'],
                    ['Periode Data', $latest->period_date->format('d F Y')],
                ] as [$k, $v])
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">{{ $k }}</span>
                    <span class="text-xs font-bold text-forest">{{ $v }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ---- Render Radial Charts ---- */
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    document.querySelectorAll('[id^="chart-sdm-"]').forEach(canvas => {
        const val   = parseFloat(canvas.dataset.value) || 0;
        const color = canvas.dataset.color || '#096b68';
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
});

/* ---- Simpan SDM ---- */
async function simpanSdm() {
    const perangkat = parseInt(document.getElementById('sdm-perangkat').value);
    const staf      = parseInt(document.getElementById('sdm-staf').value);

    if (staf > perangkat) {
        Swal.fire({ icon: 'warning', title: 'Data Tidak Valid', text: 'Jumlah staf terlatih tidak boleh melebihi total perangkat.', confirmButtonColor: '#096b68' });
        return;
    }

    const res = await apiFetch('{{ route('okr3.store') }}', {
        method: 'POST',
        body: JSON.stringify({
            period_date:              document.getElementById('sdm-date').value,
            total_perangkat:          perangkat,
            total_staf_terlatih:      staf,
            avg_competency_score:     parseFloat(document.getElementById('sdm-kompetensi').value),
            keaktifan_kinerja_persen: parseFloat(document.getElementById('sdm-keaktifan').value),
        }),
    });

    if (res.ok && res.data.success) {
        showToast('Data SDM berhasil disimpan.', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message, confirmButtonColor: '#096b68' });
    }
}
</script>
@endpush
