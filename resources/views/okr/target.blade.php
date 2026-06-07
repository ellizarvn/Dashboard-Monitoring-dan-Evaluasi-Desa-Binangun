@extends('layouts.app')
@section('title', 'Target Tahunan OKR')
@section('breadcrumb', 'Target Tahunan OKR')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Manajemen Target Tahunan</h1>
        <p class="text-sm text-sage-600 mt-0.5">Penetapan indikator OKR tahunan desa — Tahun {{ $year }}</p>
    </div>
    @if($target && $target->isFullyVerified())
    <span class="flex items-center gap-2 text-sm font-bold text-green-700 bg-green-50 border border-green-200 px-4 py-2.5 rounded-xl">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Target Terverifikasi Penuh
    </span>
    @endif
</div>

{{-- ============================================================ --}}
{{-- 3 PILAR OKR CARDS --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- PILAR 1: Partisipasi & Kegiatan --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
        <div class="bg-gradient-to-r from-forest to-forest-600 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">OKR 1</p>
                    <p class="text-white/70 text-[11px]">Partisipasi & Kegiatan Desa</p>
                </div>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Tingkat Partisipasi (%)</label>
                <div class="relative">
                    <input type="number" id="target_partisipasi_persen" step="0.01" min="0" max="100"
                           value="{{ $target?->target_partisipasi_persen ?? 90 }}"
                           placeholder="e.g. 90.00"
                           class="w-full px-4 py-2.5 pr-10 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">%</span>
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Total Kegiatan Desa</label>
                <input type="number" id="target_total_kegiatan" min="1"
                       value="{{ $target?->target_total_kegiatan ?? 24 }}"
                       placeholder="e.g. 24"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Kehadiran Musyawarah (Orang/Sesi)</label>
                <input type="number" id="target_kehadiran_musyawarah" min="1"
                       value="{{ $target?->target_kehadiran_musyawarah ?? 150 }}"
                       placeholder="e.g. 150"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
        </div>
    </div>

    {{-- PILAR 2: Omzet, Laba, PADes --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
        <div class="bg-gradient-to-r from-sage-600 to-sage px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">OKR 2</p>
                    <p class="text-white/70 text-[11px]">Omzet, Laba & Kontribusi PADes</p>
                </div>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Target Omzet BUMDes (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">Rp</span>
                    <input type="number" id="target_omzet_bumdes" step="1000" min="0"
                           value="{{ $target?->target_omzet_bumdes ?? 180000000 }}"
                           placeholder="e.g. 180000000"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
                <p class="text-[10px] text-sage-500 mt-1" id="omzet-preview">
                    = {{ $target ? 'Rp '.number_format($target->target_omzet_bumdes,0,',','.') : 'Rp 0' }}
                </p>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Proyeksi Laba Bersih (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">Rp</span>
                    <input type="number" id="target_laba_bersih" step="1000" min="0"
                           value="{{ $target?->target_laba_bersih ?? 45000000 }}"
                           placeholder="e.g. 45000000"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Kontribusi PADes (Rp)</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">Rp</span>
                    <input type="number" id="target_kontribusi_pades" step="1000" min="0"
                           value="{{ $target?->target_kontribusi_pades ?? 12000000 }}"
                           placeholder="e.g. 12000000"
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>
            </div>
        </div>
    </div>

    {{-- PILAR 3: Pelatihan & Inovasi --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
        <div class="bg-gradient-to-r from-teal-700 to-teal-600 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm">OKR 3</p>
                    <p class="text-white/70 text-[11px]">Pelatihan, Inovasi & Kepuasan</p>
                </div>
            </div>
        </div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Target Pelatihan Masyarakat (Orang)</label>
                <input type="number" id="target_pelatihan_masyarakat" min="0"
                       value="{{ $target?->target_pelatihan_masyarakat ?? 200 }}"
                       placeholder="e.g. 200"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Indeks Inovasi Desa</label>
                <select id="target_indeks_inovasi"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold bg-white
                               focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    @foreach(['Rendah','Sedang','Tinggi','Sangat Tinggi'] as $idx)
                    <option value="{{ $idx }}" {{ ($target?->target_indeks_inovasi ?? 'Tinggi') === $idx ? 'selected' : '' }}>
                        {{ $idx }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Kepuasan Masyarakat (Skor /5.0)</label>
                <div class="relative">
                    <input type="number" id="target_kepuasan_masyarakat" step="0.1" min="0" max="5"
                           value="{{ $target?->target_kepuasan_masyarakat ?? 4.2 }}"
                           placeholder="e.g. 4.2"
                           class="w-full px-4 py-2.5 pr-12 rounded-xl border border-gray-200 text-sm font-semibold
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-sage-500">/ 5.0</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CATATAN STRATEGIS + VERIFIKASI --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    {{-- Catatan Strategis --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <h2 class="font-bold text-forest text-sm mb-4">Catatan Strategis</h2>
        <p class="text-xs text-gray-500 mb-3">Landasan dan pertimbangan penetapan target tahun berjalan (RKP, RPJM, kondisi lapangan).</p>
        <textarea id="catatan_strategis" rows="6"
                  placeholder="Tuliskan catatan strategis, dasar hukum, dan pertimbangan penetapan target tahun {{ $year }}..."
                  class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium resize-none
                         focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">{{ $target?->catatan_strategis }}</textarea>
    </div>

    {{-- Widget Verifikasi --}}
    <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
        <h2 class="font-bold text-forest text-sm mb-4">Widget Verifikasi Data</h2>
        <p class="text-xs text-gray-500 mb-4">Centang syarat pemenuhan sebelum data resmi disimpan.</p>

        <div class="space-y-4">
            @foreach([
                ['is_verified_rkp',  'Sinkronisasi dengan RKP Desa', 'Target selaras dengan Rencana Kerja Pemerintah Desa tahun berjalan.', $target?->is_verified_rkp],
                ['is_verified_pagu', 'Sesuai Pagu Indikatif',        'Anggaran sesuai dengan pagu yang ditetapkan dalam APBDes.', $target?->is_verified_pagu],
                ['is_verified_bpd',  'Telah Disetujui BPD',          'Target telah mendapat persetujuan dari Badan Permusyawaratan Desa.', $target?->is_verified_bpd],
            ] as [$id, $label, $hint, $checked])
            <div class="flex items-start gap-3 p-3.5 rounded-xl border {{ $checked ? 'border-green-200 bg-green-50' : 'border-gray-100 bg-gray-50' }} transition-colors">
                <input type="checkbox" id="{{ $id }}" name="{{ $id }}"
                       {{ $checked ? 'checked' : '' }}
                       class="mt-0.5 w-4 h-4 rounded border-gray-300 text-forest focus:ring-forest/20 cursor-pointer flex-shrink-0"
                       onchange="updateVerifikasiStyle('{{ $id }}', this.checked)">
                <div>
                    <label for="{{ $id }}" class="text-xs font-bold text-gray-700 cursor-pointer">{{ $label }}</label>
                    <p class="text-[10px] text-gray-400 mt-0.5 leading-relaxed">{{ $hint }}</p>
                </div>
                @if($checked)
                <svg class="w-4 h-4 text-green-500 flex-shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Status Verifikasi Overall --}}
        <div class="mt-5 pt-4 border-t border-sage-100">
            <div id="verifikasi-status" class="flex items-center gap-2 text-xs font-bold
                {{ $target?->isFullyVerified() ? 'text-green-700' : 'text-amber-600' }}">
                @if($target?->isFullyVerified())
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Semua verifikasi terpenuhi
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Verifikasi belum lengkap
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TOMBOL SIMPAN --}}
{{-- ============================================================ --}}
@if(auth()->user()->isAdmin() || auth()->user()->isKepala())
<div class="flex items-center justify-end gap-4">
    <p class="text-xs text-gray-400">Tahun Target: <span class="font-bold text-forest">{{ $year }}</span></p>
    <button onclick="simpanTarget()"
            class="flex items-center gap-2 bg-forest text-white font-bold px-8 py-3.5 rounded-xl text-sm
                   hover:bg-forest-600 active:scale-[0.98] transition-all shadow-lg shadow-forest/25">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
        </svg>
        Simpan Data Target
    </button>
</div>
@else
<div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <p class="text-sm font-medium text-amber-700">Hanya Administrator dan Kepala Desa yang dapat mengubah target tahunan.</p>
</div>
@endif
@endsection

@push('scripts')
<script>
/* ---- Format angka Rupiah real-time untuk field Omzet ---- */
document.getElementById('target_omzet_bumdes')?.addEventListener('input', function() {
    const val = parseFloat(this.value) || 0;
    document.getElementById('omzet-preview').textContent =
        '= Rp ' + val.toLocaleString('id-ID');
});

/* ---- Update style card verifikasi saat di-klik ---- */
function updateVerifikasiStyle(id, checked) {
    const wrapper = document.getElementById(id).closest('div.flex');
    if (checked) {
        wrapper.classList.replace('border-gray-100', 'border-green-200');
        wrapper.classList.replace('bg-gray-50', 'bg-green-50');
    } else {
        wrapper.classList.replace('border-green-200', 'border-gray-100');
        wrapper.classList.replace('bg-green-50', 'bg-gray-50');
    }
}

/* ---- Simpan Target via AJAX ---- */
async function simpanTarget() {
    const payload = {
        year:                        {{ $year }},
        target_partisipasi_persen:   parseFloat(document.getElementById('target_partisipasi_persen').value) || 0,
        target_total_kegiatan:       parseInt(document.getElementById('target_total_kegiatan').value) || 0,
        target_kehadiran_musyawarah: parseInt(document.getElementById('target_kehadiran_musyawarah').value) || 0,
        target_omzet_bumdes:         parseFloat(document.getElementById('target_omzet_bumdes').value) || 0,
        target_laba_bersih:          parseFloat(document.getElementById('target_laba_bersih').value) || 0,
        target_kontribusi_pades:     parseFloat(document.getElementById('target_kontribusi_pades').value) || 0,
        target_pelatihan_masyarakat: parseInt(document.getElementById('target_pelatihan_masyarakat').value) || 0,
        target_indeks_inovasi:       document.getElementById('target_indeks_inovasi').value,
        target_kepuasan_masyarakat:  parseFloat(document.getElementById('target_kepuasan_masyarakat').value) || 0,
        catatan_strategis:           document.getElementById('catatan_strategis').value,
        is_verified_rkp:             document.getElementById('is_verified_rkp').checked ? 1 : 0,
        is_verified_pagu:            document.getElementById('is_verified_pagu').checked ? 1 : 0,
        is_verified_bpd:             document.getElementById('is_verified_bpd').checked ? 1 : 0,
    };

    const res = await apiFetch('{{ route('target.store') }}', {
        method: 'POST',
        body: JSON.stringify(payload),
    });

    if (res.ok && res.data.success) {
        showSuccessModal(
            'Data Berhasil Disimpan',
            'Target OKR tahunan desa telah berhasil diperbarui dan tersimpan dalam sistem.',
            res.data.timestamp
        );
    } else {
        const msg = res.data.errors
            ? Object.values(res.data.errors).flat().join('\n')
            : (res.data.message || 'Terjadi kesalahan saat menyimpan.');
        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: msg, confirmButtonColor: '#1A362B' });
    }
}
</script>
@endpush
