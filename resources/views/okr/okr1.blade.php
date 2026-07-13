@extends('layouts.app')
@section('title', 'OKR 1 — Partisipasi Masyarakat')
@section('breadcrumb', 'OKR 1 — Partisipasi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <div class="flex flex-col">
            <span class="text-xs font-bold text-sage-500 uppercase tracking-wider mb-0.5">OKR 1</span>
            <h1 class="font-display text-2xl text-forest">Partisipasi Masyarakat</h1>
        </div>
        <p class="text-sm text-sage-600 mt-0.5">Monitoring partisipasi dan kegiatan desa tahun {{ $year }}</p>
    </div>
    <div class="flex items-center gap-2">
        @if(auth()->user()->canMutateData())
        <button onclick="bukaModalImport()"
                class="flex items-center gap-2 border border-forest text-forest hover:bg-forest-50 text-xs font-bold px-4 py-2.5 rounded-xl
                       active:scale-[0.98] transition-all shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Import Excel (CSV)
        </button>
        @endif
        <span class="text-xs font-bold px-3 py-2.5 rounded-full
            {{ $capaian['status'] === 'ON TRACK' ? 'bg-green-50 text-green-700' :
               ($capaian['status'] === 'AT RISK' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
            {{ $capaian['status'] }} {{ $capaian['avg_partisipasi'] }}%
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ============================================================ --}}
    {{-- FORM INPUT PARTISIPASI (Kiri) --}}
    {{-- ============================================================ --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">Input Partisipasi Bulanan</h2>

            @if(auth()->user()->canMutateData())
            <div class="space-y-4">
                {{-- Bulan --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan</label>
                    <select id="part-month"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tahun</label>
                    <select id="part-year"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected':'' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Total Warga --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Total Warga Wajib Lapor</label>
                    <input type="number" id="total-warga" min="1" placeholder="e.g. 850"
                           oninput="hitungPersentase()"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>

                {{-- Warga Hadir --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jumlah Warga Hadir</label>
                    <input type="number" id="warga-hadir" min="0" placeholder="e.g. 720"
                           oninput="hitungPersentase()"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>

                {{-- Kalkulasi Langsung --}}
                <div class="bg-forest-50 rounded-xl p-4 border border-forest-200">
                    <p class="text-[11px] font-bold text-forest mb-1">Kalkulasi Otomatis</p>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-black text-forest" id="calc-persen">—</p>
                            <p class="text-[10px] text-sage-600">Persentase Partisipasi</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-semibold text-sage-600">Target: {{ $target?->target_partisipasi_persen ?? 90 }}%</p>
                            <p class="text-[10px] text-gray-400" id="status-label">—</p>
                        </div>
                    </div>
                    <div class="mt-2 h-1.5 bg-forest-100 rounded-full overflow-hidden">
                        <div id="calc-bar" class="h-full bg-forest rounded-full transition-all duration-500" style="width: 0%"></div>
                    </div>
                </div>

                <button onclick="simpanPartisipasi()"
                        class="w-full py-3 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                    Simpan Data Partisipasi
                </button>
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs text-amber-700 font-medium">Anda tidak memiliki akses untuk menginput data partisipasi.</p>
            </div>
            @endif
        </div>

        {{-- Ringkasan Capaian --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h3 class="font-bold text-forest text-sm mb-4">Ringkasan Capaian OKR 1</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Rata-rata Partisipasi</span>
                    <span class="text-sm font-black text-forest">{{ $capaian['avg_partisipasi'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Target Tahunan</span>
                    <span class="text-sm font-bold text-gray-700">{{ $capaian['target'] }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">Capaian terhadap Target</span>
                    <span class="text-sm font-black
                        {{ $capaian['capaian_persen'] >= 80 ? 'text-forest' : ($capaian['capaian_persen'] >= 60 ? 'text-amber-600' : 'text-red-500') }}">
                        {{ $capaian['capaian_persen'] }}%
                    </span>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden mt-1">
                    <div class="h-full bg-forest rounded-full progress-animated"
                         style="width: {{ $capaian['capaian_persen'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL & CHART RIWAYAT (Kanan) --}}
    {{-- ============================================================ --}}
    <div class="lg:col-span-3 space-y-5">

        {{-- Line Chart Tren --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-4">Tren Partisipasi {{ $year }}</h2>
            <div class="h-44">
                <canvas id="tren-chart"
                        data-labels="{{ json_encode($tren['labels'] ?? []) }}"
                        data-values="{{ json_encode($tren['values'] ?? []) }}"
                        data-target="{{ $target?->target_partisipasi_persen ?? 90 }}">
                </canvas>
            </div>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-sage-100/60">
                <h2 class="font-bold text-forest text-sm">Riwayat Data Partisipasi {{ $year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-forest-50/30 border-b border-sage-100/60">
                            <th class="text-left px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Bulan</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Total Warga</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Warga Hadir</th>
                            <th class="text-right px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Persentase</th>
                            <th class="text-center px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sage-50">
                        @forelse($data as $row)
                        <tr class="hover:bg-forest-50/20 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-gray-800">{{ $row->month }}</td>
                            <td class="px-4 py-3.5 text-right font-medium text-gray-600">{{ number_format($row->total_warga_wajib_lapor) }}</td>
                            <td class="px-4 py-3.5 text-right font-medium text-gray-600">{{ number_format($row->warga_hadir) }}</td>
                            <td class="px-4 py-3.5 text-right">
                                <span class="font-black {{ $row->calculated_percentage >= ($target?->target_partisipasi_persen ?? 90) ? 'text-forest' : 'text-amber-600' }}">
                                    {{ $row->calculated_percentage }}%
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @php $met = $row->calculated_percentage >= ($target?->target_partisipasi_persen ?? 90); @endphp
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full
                                    {{ $met ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                                    {{ $met ? 'Tercapai' : 'Belum Tercapai' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-xs text-gray-400">Belum ada data partisipasi untuk tahun {{ $year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL IMPORT CSV --}}
{{-- ============================================================ --}}
<div id="modal-import"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     onclick="if(event.target===this) tutupModalImport()">
    <div class="absolute inset-0 bg-forest-900/50 backdrop-blur-sm animate-fade-in"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-forest text-base">Import Partisipasi Bulanan (CSV)</h2>
            <button onclick="tutupModalImport()" class="p-2 text-gray-400 hover:text-forest hover:bg-forest-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <p class="text-xs text-gray-500 leading-relaxed">
                Pilih berkas template CSV yang sudah diisi data partisipasi bulanan. Pastikan kolom sesuai dengan format: 
                <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono text-[10px] text-forest">Bulan; Tahun; Warga Wajib Lapor; Warga Hadir</code>.
            </p>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Berkas CSV</label>
                <input type="file" id="import-file" accept=".csv,text/csv"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              file:mr-4 file:py-1.5 file:px-4 file:rounded-xl file:border-0
                              file:text-xs file:font-bold file:bg-forest-50 file:text-forest
                              hover:file:bg-forest-100 file:cursor-pointer cursor-pointer">
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <a href="/template_import_okr1.csv" download
               class="flex-1 py-2.5 border border-sage-200 text-sage-700 font-semibold rounded-xl text-xs hover:bg-sage-50 transition-all flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Unduh Template
            </a>
            <button onclick="prosesImportCSV()"
                    class="flex-1 py-2.5 bg-forest text-white font-bold rounded-xl text-sm
                           hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                Mulai Import
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ---- Kalkulasi Persentase Langsung ---- */
function hitungPersentase() {
    const total  = parseInt(document.getElementById('total-warga').value) || 0;
    const hadir  = parseInt(document.getElementById('warga-hadir').value) || 0;
    const target = {{ $target?->target_partisipasi_persen ?? 90 }};

    if (total > 0 && hadir >= 0) {
        const pct = Math.min((hadir / total) * 100, 100).toFixed(2);
        document.getElementById('calc-persen').textContent = pct + '%';
        document.getElementById('calc-bar').style.width = Math.min(pct, 100) + '%';
        document.getElementById('status-label').textContent = pct >= target ? '✓ Memenuhi Target' : '↓ Di Bawah Target';
    } else {
        document.getElementById('calc-persen').textContent = '—';
        document.getElementById('calc-bar').style.width = '0%';
    }
}

/* ---- Simpan Data Partisipasi ---- */
async function simpanPartisipasi() {
    const total = parseInt(document.getElementById('total-warga').value);
    const hadir = parseInt(document.getElementById('warga-hadir').value);

    if (!total || total <= 0 || hadir < 0 || hadir > total) {
        Swal.fire({ icon: 'warning', title: 'Data Tidak Valid', text: 'Periksa kembali jumlah warga wajib lapor dan warga hadir.', confirmButtonColor: '#096b68' });
        return;
    }

    const res = await apiFetch('{{ route('okr1.store') }}', {
        method: 'POST',
        body: JSON.stringify({
            month:                   document.getElementById('part-month').value,
            year:                    parseInt(document.getElementById('part-year').value),
            total_warga_wajib_lapor: total,
            warga_hadir:             hadir,
        }),
    });

    if (res.ok && res.data.success) {
        showSuccessModal('Data Partisipasi Tersimpan',
            `Persentase partisipasi: ${res.data.percentage}%`,
            new Date().toLocaleString('id-ID'));
        setTimeout(() => location.reload(), 2000);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message, confirmButtonColor: '#096b68' });
    }
}

/* ---- Import Excel (CSV) JavaScript Helpers ---- */
function bukaModalImport() {
    document.getElementById('modal-import').classList.remove('hidden');
}

function tutupModalImport() {
    document.getElementById('modal-import').classList.add('hidden');
    document.getElementById('import-file').value = '';
}

async function prosesImportCSV() {
    const fileInput = document.getElementById('import-file');
    const file = fileInput.files[0];
    if (!file) {
        Swal.fire({ icon: 'warning', title: 'Pilih Berkas', text: 'Silakan pilih berkas CSV terlebih dahulu.', confirmButtonColor: '#096b68' });
        return;
    }

    Swal.fire({
        title: 'Mengimpor Data...',
        text: 'Sedang memproses berkas CSV partisipasi.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('file_import', file);

    try {
        const response = await fetch('{{ route('okr1.import') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        const result = await response.json();

        if (response.ok && result.success) {
            tutupModalImport();
            let msg = result.message;
            if (result.errors && result.errors.length > 0) {
                msg += "<br><br><strong>Peringatan beberapa baris tidak valid:</strong><br><ul class='list-disc text-left pl-4 max-h-40 overflow-y-auto text-xs space-y-1 text-amber-700'>";
                result.errors.forEach(err => {
                    msg += `<li>${err}</li>`;
                });
                msg += "</ul>";
                
                Swal.fire({
                    icon: 'warning',
                    title: 'Impor Berhasil dengan Catatan',
                    html: `<div class="text-sm text-gray-600">${msg}</div>`,
                    confirmButtonColor: '#096b68'
                }).then(() => {
                    location.reload();
                });
            } else {
                showSuccessModal('Impor Berhasil', msg, new Date().toLocaleString('id-ID'));
                setTimeout(() => location.reload(), 2000);
            }
        } else {
            let errMsg = result.message || 'Gagal memproses impor berkas.';
            if (result.errors && result.errors.length > 0) {
                errMsg += "<br><br><strong class='text-red-600'>Detail Kesalahan:</strong><br><ul class='list-disc text-left pl-4 max-h-40 overflow-y-auto text-xs space-y-1 text-red-500'>";
                result.errors.forEach(err => {
                    errMsg += `<li>${err}</li>`;
                });
                errMsg += "</ul>";
            }
            Swal.fire({
                icon: 'error',
                title: 'Impor Gagal',
                html: `<div class="text-sm text-gray-600">${errMsg}</div>`,
                confirmButtonColor: '#096b68'
            });
        }
    } catch (err) {
        Swal.fire({ icon: 'error', title: 'Kesalahan Sistem', text: 'Koneksi gagal atau sesi habis. Silakan coba kembali.', confirmButtonColor: '#096b68' });
    }
}


/* ---- Render Chart ---- */
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    const canvas = document.getElementById('tren-chart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const values = JSON.parse(canvas.dataset.values || '[]');
    const target = parseFloat(canvas.dataset.target) || 90;

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Partisipasi (%)',
                    data: values,
                    borderColor: '#096b68',
                    backgroundColor: 'rgba(9,107,104,0.07)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#096b68',
                    pointRadius: 5,
                    tension: 0.4,
                    fill: true,
                },
                {
                    label: 'Target (%)',
                    data: labels.map(() => target),
                    borderColor: '#87A996',
                    borderWidth: 1.5,
                    borderDash: [5,4],
                    pointRadius: 0,
                    fill: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y}%` } }
            },
            scales: {
                y: { min: 60, max: 100, ticks: { callback: v => v + '%' }, grid: { color: 'rgba(135,169,150,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
