@extends('layouts.app')
@section('title', 'OKR 2 — Ekonomi BUMDes')
@section('breadcrumb', 'OKR 2 — Ekonomi BUMDes')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <div class="flex flex-col">
            <span class="text-xs font-bold text-sage-500 uppercase tracking-wider mb-0.5">OKR 2</span>
            <h1 class="font-display text-2xl text-forest">Penguatan Ekonomi Lokal</h1>
        </div>
        <p class="text-sm text-sage-600 mt-0.5">Monitoring BUMDes, transaksi keuangan, dan kontribusi PADes tahun {{ $year }}</p>
    </div>
    <span class="text-xs font-bold px-3 py-1.5 rounded-full
        {{ $capaian['status'] === 'ON TRACK' ? 'bg-green-50 text-green-700' :
           ($capaian['status'] === 'AT RISK' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
        {{ $capaian['status'] }} {{ $capaian['avg_capaian'] }}%
    </span>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    @foreach([
        ['Total Omzet', 'Rp '.number_format($capaian['agregat']['total_omzet'],0,',','.'), 'Target: Rp '.number_format($target?->target_omzet_bumdes??0,0,',','.'), $capaian['capaian_omzet']],
        ['Laba Bersih', 'Rp '.number_format($capaian['agregat']['laba_bersih'],0,',','.'), 'Target: Rp '.number_format($target?->target_laba_bersih??0,0,',','.'), $capaian['capaian_laba']],
        ['Kontribusi PADes', 'Rp '.number_format($capaian['agregat']['total_pades'],0,',','.'), 'Target: Rp '.number_format($target?->target_kontribusi_pades??0,0,',','.'), $capaian['capaian_pades']],
    ] as [$lbl, $val, $tgt, $pct])
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <p class="text-[11px] font-semibold text-gray-500 mb-1">{{ $lbl }}</p>
        <p class="text-xl font-black text-forest leading-tight">{{ $val }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5 mb-2">{{ $tgt }}</p>
        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-forest rounded-full progress-animated" style="width: {{ min($pct, 100) }}%"></div>
        </div>
        <p class="text-[10px] font-bold text-forest mt-1">{{ $pct }}% tercapai</p>
    </div>
    @endforeach
</div>

@if(auth()->user()->canMutateData())
<div class="flex items-center gap-3 mb-6 flex-wrap">
    <button onclick="bukaModalTransaksi()"
            class="flex items-center gap-2 bg-forest text-white text-sm font-bold px-5 py-2.5 rounded-xl
                   hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Transaksi Baru
    </button>
    <button onclick="bukaModalPades()"
            class="flex items-center gap-2 border border-forest text-forest hover:bg-forest-50 text-sm font-bold px-5 py-2.5 rounded-xl
                   active:scale-[0.98] transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Setoran PADes
    </button>
    <button onclick="bukaModalImport()"
            class="flex items-center gap-2 border border-forest text-forest hover:bg-forest-50 text-sm font-bold px-5 py-2.5 rounded-xl
                   active:scale-[0.98] transition-all shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Import Excel (CSV)
    </button>
</div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ============================================================ --}}
    {{-- DAFTAR UNIT USAHA BUMDES (Kiri) --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-sage-100/60">
                <h2 class="font-bold text-forest text-sm">Unit Usaha BUMDes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-forest-50/30 border-b border-sage-100/60">
                            <th class="text-left px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Nama Unit / PIC</th>
                            <th class="text-center px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Sektor</th>
                            <th class="text-right px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Modal Awal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sage-50">
                        @forelse($units as $unit)
                        <tr class="hover:bg-forest-50/20 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-bold text-forest text-[13px]">{{ $unit->name_unit }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">PIC: {{ $unit->pic_name }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full
                                    {{ ['Perdagangan'=>'bg-blue-50 text-blue-700','Pertanian'=>'bg-green-50 text-green-700','Pariwisata'=>'bg-purple-50 text-purple-700','Jasa'=>'bg-amber-50 text-amber-700'][$unit->sector] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ $unit->sector }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-gray-700">
                                Rp {{ number_format($unit->initial_capital,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-xs text-gray-400">
                                Belum ada unit usaha BUMDes terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL TRANSAKSI TERBARU (Kanan) --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-sage-100/60">
                <h2 class="font-bold text-forest text-sm">20 Transaksi Terbaru {{ $year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="bg-forest-50/30 border-b border-sage-100/60">
                            <th class="text-left px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Tanggal</th>
                            <th class="text-left px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Unit</th>
                            <th class="text-center px-4 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Jenis</th>
                            <th class="text-right px-5 py-3 font-bold text-forest text-[11px] uppercase tracking-wide">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sage-50">
                        @forelse($transaksiTerbaru as $trx)
                        <tr class="hover:bg-forest-50/20 transition-colors">
                            <td class="px-5 py-3 font-medium text-gray-600">{{ $trx->transaction_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800 truncate max-w-28">{{ $trx->bumdesUnit?->name_unit ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full
                                    {{ $trx->type === 'Pemasukan' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                                    {{ $trx->type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="font-black {{ $trx->type === 'Pemasukan' ? 'text-green-700' : 'text-red-600' }}">
                                    {{ $trx->type === 'Pemasukan' ? '+' : '-' }}{{ $trx->nominal_formatted }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-xs text-gray-400">Belum ada transaksi tahun {{ $year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->canMutateData())
{{-- ============================================================ --}}
{{-- MODAL INPUT TRANSAKSI --}}
{{-- ============================================================ --}}
<div id="modal-transaksi"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     onclick="if(event.target===this) tutupModalTransaksi()">
    <div class="absolute inset-0 bg-forest-900/50 backdrop-blur-sm animate-fade-in"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-forest text-base">Input Transaksi BUMDes Baru</h2>
            <button onclick="tutupModalTransaksi()" class="p-2 text-gray-400 hover:text-forest hover:bg-forest-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Usaha</label>
                <select id="trx-unit"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                               focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    <option value="">Pilih Unit Usaha</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name_unit }} ({{ $unit->sector }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Transaksi</label>
                <input type="date" id="trx-date" value="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-2">Jenis Transaksi</label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-center gap-2.5 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-forest transition-colors has-[:checked]:border-forest has-[:checked]:bg-forest-50">
                        <input type="radio" name="trx-type" value="Pemasukan" checked class="text-forest focus:ring-forest/20">
                        <span class="text-xs font-bold text-gray-700">Pemasukan</span>
                    </label>
                    <label class="flex-1 flex items-center gap-2.5 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-red-300 transition-colors has-[:checked]:border-red-400 has-[:checked]:bg-red-50">
                        <input type="radio" name="trx-type" value="Pengeluaran" class="text-red-500 focus:ring-red-300">
                        <span class="text-xs font-bold text-gray-700">Pengeluaran</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nominal (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">Rp</span>
                    <input type="number" id="trx-nominal" step="1000" min="1" placeholder="0"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <input type="text" id="trx-desc" placeholder="Keterangan singkat transaksi..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="tutupModalTransaksi()"
                    class="flex-1 py-2.5 border border-sage-200 text-sage-700 font-semibold rounded-xl text-sm hover:bg-sage-50 transition-all">
                Batal
            </button>
            <button onclick="simpanTransaksi()"
                    class="flex-1 py-2.5 bg-forest text-white font-bold rounded-xl text-sm
                           hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                Simpan Transaksi
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL SETORAN PADES --}}
{{-- ============================================================ --}}
<div id="modal-pades"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     onclick="if(event.target===this) tutupModalPades()">
    <div class="absolute inset-0 bg-forest-900/50 backdrop-blur-sm animate-fade-in"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-forest text-base">Setoran Kontribusi PADes</h2>
            <button onclick="tutupModalPades()" class="p-2 text-gray-400 hover:text-forest hover:bg-forest-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="flex items-start gap-2 mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3">
            <svg class="w-3.5 h-3.5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-[10px] text-amber-700 font-medium leading-relaxed">
                <strong>PerDes:</strong> Setoran PADes minimal <strong>25% dari total laba bersih bulanan</strong> unit usaha BUMDes. Wajib dilampirkan bukti transfer.
            </p>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Usaha BUMDes</label>
                <select id="pades-unit"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                               focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    <option value="">Pilih Unit</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name_unit }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Periode (Bulan-Tahun)</label>
                <input type="month" id="pades-period" value="{{ now()->format('Y-m') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nominal Setoran (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">Rp</span>
                    <input type="number" id="pades-nominal" step="1000" min="1" placeholder="0"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                    Bukti Transfer
                    <span class="text-red-500 font-bold">*</span>
                </label>
                <label class="flex flex-col items-center gap-2 border-2 border-dashed border-sage-200 rounded-xl p-4 cursor-pointer hover:border-forest hover:bg-forest-50 transition-all">
                    <svg class="w-6 h-6 text-sage-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span class="text-xs font-semibold text-sage-500" id="file-label">Klik atau drag file (PDF/JPG, maks. 5MB)</span>
                    <input type="file" id="pades-file" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                           onchange="document.getElementById('file-label').textContent = this.files[0]?.name || 'Klik atau drag file'">
                </label>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="tutupModalPades()"
                    class="flex-1 py-2.5 border border-sage-200 text-sage-700 font-semibold rounded-xl text-sm hover:bg-sage-50 transition-all">
                Batal
            </button>
            <button onclick="simpanPades()"
                    class="flex-1 py-2.5 bg-forest text-white font-bold rounded-xl text-sm
                           hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                Kirim Setoran PADes
            </button>
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
            <h2 class="font-bold text-forest text-base">Import Transaksi BUMDes (CSV)</h2>
            <button onclick="tutupModalImport()" class="p-2 text-gray-400 hover:text-forest hover:bg-forest-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <p class="text-xs text-gray-500 leading-relaxed">
                Pilih berkas template CSV yang sudah diisi data transaksi. Pastikan kolom sesuai dengan format: 
                <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono text-[10px] text-forest">Nama Unit; Tanggal Transaksi; Jenis; Nominal; Keterangan</code>.
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
            <a href="/template_import_okr2.csv" download
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
@endif
@endsection

@push('scripts')
<script>
function bukaModalTransaksi() {
    const modal = document.getElementById('modal-transaksi');
    if (modal) modal.classList.remove('hidden');
}
function tutupModalTransaksi() {
    const modal = document.getElementById('modal-transaksi');
    if (modal) modal.classList.add('hidden');
    // Clear inputs
    document.getElementById('trx-unit').value = '';
    document.getElementById('trx-nominal').value = '';
    document.getElementById('trx-desc').value = '';
}

function bukaModalPades() {
    const modal = document.getElementById('modal-pades');
    if (modal) modal.classList.remove('hidden');
}
function tutupModalPades() {
    const modal = document.getElementById('modal-pades');
    if (modal) modal.classList.add('hidden');
    // Clear inputs
    document.getElementById('pades-unit').value = '';
    document.getElementById('pades-nominal').value = '';
    document.getElementById('pades-file').value = '';
    document.getElementById('file-label').textContent = 'Klik atau drag file (PDF/JPG, maks. 5MB)';
}

async function simpanTransaksi() {
    const unitId = document.getElementById('trx-unit').value;
    const date   = document.getElementById('trx-date').value;
    const type   = document.querySelector('input[name="trx-type"]:checked')?.value;
    const nominal = parseFloat(document.getElementById('trx-nominal').value);
    const desc   = document.getElementById('trx-desc').value;

    if (!unitId || !date || !type || !nominal || nominal <= 0) {
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Isi semua field yang diperlukan.', confirmButtonColor: '#096b68' });
        return;
    }

    const res = await apiFetch('{{ route('okr2.transaksi.store') }}', {
        method: 'POST',
        body: JSON.stringify({ bumdes_unit_id: unitId, transaction_date: date, type, nominal, description: desc }),
    });

    if (res.ok && res.data.success) {
        tutupModalTransaksi();
        showSuccessModal('Transaksi Berhasil', 'Transaksi BUMDes baru telah berhasil dicatat ke sistem.', new Date().toLocaleString('id-ID'));
        setTimeout(() => location.reload(), 2000);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message, confirmButtonColor: '#096b68' });
    }
}

async function simpanPades() {
    const unitId  = document.getElementById('pades-unit').value;
    const period  = document.getElementById('pades-period').value;
    const nominal = parseFloat(document.getElementById('pades-nominal').value);
    const file    = document.getElementById('pades-file').files[0];

    if (!unitId || !period || !nominal || nominal <= 0) {
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Isi semua field setoran PADes.', confirmButtonColor: '#096b68' });
        return;
    }

    const formData = new FormData();
    formData.append('bumdes_unit_id',    unitId);
    formData.append('period_year_month', period);
    formData.append('nominal_setoran',   nominal);
    if (file) formData.append('file_proof', file);

    try {
        const res = await fetch('{{ route('okr2.pades.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken, 'Accept': 'application/json' },
            body: formData,
        });
        const json = await res.json();

        if (json.success) {
            tutupModalPades();
            showSuccessModal('Setoran Terkirim', 'Laporan setoran PADes dari unit usaha BUMDes berhasil disimpan.', new Date().toLocaleString('id-ID'));
            setTimeout(() => location.reload(), 2000);
        } else {
            Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: json.message, confirmButtonColor: '#096b68' });
        }
    } catch (e) {
        showToast('Koneksi gagal. Coba lagi.', 'error');
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
        text: 'Sedang memproses berkas CSV transaksi BUMDes.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const formData = new FormData();
    formData.append('file_import', file);

    try {
        const response = await fetch('{{ route('okr2.import') }}', {
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
</script>
@endpush
