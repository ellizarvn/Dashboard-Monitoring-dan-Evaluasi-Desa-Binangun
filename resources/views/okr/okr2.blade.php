@extends('layouts.app')
@section('title', 'OKR 2 — Ekonomi BUMDes')
@section('breadcrumb', 'OKR 2 — Ekonomi BUMDes')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">OKR 2 — Penguatan Ekonomi Lokal</h1>
        <p class="text-sm text-sage-600 mt-0.5">Monitoring BUMDes, transaksi keuangan, dan kontribusi PADes tahun {{ $year }}</p>
    </div>
    <span class="text-xs font-bold px-3 py-1.5 rounded-full
        {{ $capaian['status'] === 'ON TRACK' ? 'bg-green-50 text-green-700' :
           ($capaian['status'] === 'AT RISK' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
        {{ $capaian['status'] }} — {{ $capaian['avg_capaian'] }}%
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

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- ============================================================ --}}
    {{-- FORM INPUT TRANSAKSI --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">Input Transaksi BUMDes Baru</h2>

            @if(auth()->user()->canMutateData())
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Usaha</label>
                    <select id="trx-unit"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        <option value="">-- Pilih Unit Usaha --</option>
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
                {{-- Tipe Transaksi (Radio Button) --}}
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
                <button onclick="simpanTransaksi()"
                        class="w-full py-3 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                    Simpan Transaksi
                </button>
            </div>
            @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs text-amber-700">Anda tidak memiliki akses untuk menginput transaksi.</p>
            </div>
            @endif
        </div>

        {{-- Form Setoran PADes --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-1">Setoran Kontribusi PADes</h2>
            <div class="flex items-start gap-2 mb-4 bg-amber-50 border border-amber-200 rounded-xl p-3">
                <svg class="w-3.5 h-3.5 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-[10px] text-amber-700 font-medium leading-relaxed">
                    <strong>PerDes:</strong> Setoran PADes minimal <strong>25% dari total laba bersih bulanan</strong> unit usaha BUMDes. Wajib dilampirkan bukti transfer.
                </p>
            </div>

            @if(auth()->user()->canMutateData())
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Usaha BUMDes</label>
                    <select id="pades-unit"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        <option value="">-- Pilih Unit --</option>
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
                <button onclick="simpanPades()"
                        class="w-full py-3 bg-forest text-white font-bold rounded-xl text-sm
                               hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                    Kirim Setoran PADes
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- TABEL TRANSAKSI TERBARU --}}
    {{-- ============================================================ --}}
    <div class="space-y-5">
        {{-- Daftar Unit Usaha --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-sage-100/60">
                <h2 class="font-bold text-forest text-sm">Unit Usaha BUMDes</h2>
            </div>
            <div class="divide-y divide-sage-50">
                @foreach($units as $unit)
                <div class="px-6 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-forest">{{ $unit->name_unit }}</p>
                        <p class="text-xs text-gray-500">PIC: {{ $unit->pic_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full
                            {{ ['Perdagangan'=>'bg-blue-50 text-blue-700','Pertanian'=>'bg-green-50 text-green-700','Pariwisata'=>'bg-purple-50 text-purple-700','Jasa'=>'bg-amber-50 text-amber-700'][$unit->sector] ?? 'bg-gray-50 text-gray-600' }}">
                            {{ $unit->sector }}
                        </span>
                        <p class="text-[10px] text-gray-400 mt-0.5">Modal: Rp {{ number_format($unit->initial_capital,0,',','.') }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Transaksi Terbaru --}}
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
@endsection

@push('scripts')
<script>
async function simpanTransaksi() {
    const unitId = document.getElementById('trx-unit').value;
    const date   = document.getElementById('trx-date').value;
    const type   = document.querySelector('input[name="trx-type"]:checked')?.value;
    const nominal = parseFloat(document.getElementById('trx-nominal').value);
    const desc   = document.getElementById('trx-desc').value;

    if (!unitId || !date || !type || !nominal || nominal <= 0) {
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Isi semua field yang diperlukan.', confirmButtonColor: '#1A362B' });
        return;
    }

    const res = await apiFetch('{{ route('okr2.transaksi.store') }}', {
        method: 'POST',
        body: JSON.stringify({ bumdes_unit_id: unitId, transaction_date: date, type, nominal, description: desc }),
    });

    if (res.ok && res.data.success) {
        showToast('Transaksi berhasil disimpan.', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message, confirmButtonColor: '#1A362B' });
    }
}

async function simpanPades() {
    const unitId  = document.getElementById('pades-unit').value;
    const period  = document.getElementById('pades-period').value;
    const nominal = parseFloat(document.getElementById('pades-nominal').value);
    const file    = document.getElementById('pades-file').files[0];

    if (!unitId || !period || !nominal || nominal <= 0) {
        Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Isi semua field setoran PADes.', confirmButtonColor: '#1A362B' });
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
            showToast('Setoran PADes berhasil disimpan.', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: json.message, confirmButtonColor: '#1A362B' });
        }
    } catch (e) {
        showToast('Koneksi gagal. Coba lagi.', 'error');
    }
}
</script>
@endpush
