@extends('layouts.app')
@section('title', 'Laporan Desa')
@section('breadcrumb', 'Laporan Desa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Manajemen Laporan Desa</h1>
        <p class="text-sm text-sage-600 mt-0.5">Kelola dokumen laporan bulanan, triwulan, dan tahunan desa</p>
    </div>
    @if(in_array(auth()->user()->role, ['admin','tim_monitoring','kepala_desa']))
    <button onclick="bukaModalLaporan()"
            class="flex items-center gap-2 bg-forest text-white text-sm font-bold px-5 py-2.5 rounded-xl
                   hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Laporan Baru
    </button>
    @endif
</div>

{{-- ============================================================ --}}
{{-- STATISTIK CARDS --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-7">
    {{-- Total Laporan --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 mb-1">Total Laporan</p>
                <p class="text-3xl font-black text-forest">{{ $totalLaporan }}</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-forest-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Terpublikasi --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 mb-1">Terpublikasi</p>
                <p class="text-3xl font-black text-green-700">{{ $terpublikasi }}</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Draf --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 mb-1">Draf Laporan</p>
                <p class="text-3xl font-black text-amber-700">{{ $draf }}</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Menunggu Review --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-red-200">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 mb-1">Menunggu Review</p>
                <p class="text-3xl font-black text-red-600">{{ $menungguReview }}</p>
                @if($menungguReview > 0)
                @if(in_array(auth()->user()->role, ['admin','kepala_desa']))
                <button onclick="tinjauDraf()"
                        class="mt-2 text-[10px] font-bold text-red-600 underline hover:no-underline">
                    Tinjau Sekarang →
                </button>
                @endif
                @endif
            </div>
            <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-4.5 h-4.5 text-red-500 {{ $menungguReview > 0 ? 'badge-pulse' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- TABEL LAPORAN --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
    <div class="px-6 py-4 border-b border-sage-100/60 flex items-center justify-between">
        <h2 class="font-bold text-forest text-sm">Riwayat Berkas Laporan</h2>
        <p class="text-xs text-sage-500">{{ $reports->total() }} laporan</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-forest-50/30 border-b border-sage-100/60">
                    <th class="text-left px-6 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Judul Laporan</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Jenis</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Tanggal</th>
                    <th class="text-left px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Penulis</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Status</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sage-50">
                @forelse($reports as $report)
                <tr class="hover:bg-forest-50/20 transition-colors">
                    {{-- Judul --}}
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-800 max-w-xs leading-snug">{{ $report->title }}</p>
                    </td>

                    {{-- Jenis --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full bg-sage-50 text-sage-700">
                            {{ $report->type }}
                        </span>
                    </td>

                    {{-- Tanggal --}}
                    <td class="px-4 py-4 text-center">
                        <p class="font-medium text-gray-600">{{ $report->report_date->format('d/m/Y') }}</p>
                    </td>

                    {{-- Penulis --}}
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-forest flex items-center justify-center flex-shrink-0">
                                <span class="text-[9px] font-black text-white">{{ $report->author?->initials ?? '?' }}</span>
                            </div>
                            <span class="font-medium text-gray-700 truncate max-w-24">{{ $report->author?->name ?? '—' }}</span>
                        </div>
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full
                            {{ $report->status === 'Published' ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $report->status === 'Published' ? 'bg-green-500' : 'bg-amber-500' }}"></span>
                            {{ $report->status }}
                        </span>
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-4 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            {{-- Publish (jika masih Draft & punya izin) --}}
                            @if($report->status === 'Draft' && in_array(auth()->user()->role, ['admin','kepala_desa']))
                            <button onclick="publishLaporan({{ $report->id }}, '{{ addslashes($report->title) }}')"
                                    class="px-2.5 py-1.5 text-[10px] font-bold text-forest bg-forest-50 border border-forest-200 rounded-lg hover:bg-forest hover:text-white transition-all">
                                Terbitkan
                            </button>
                            @endif

                            {{-- Hapus (admin only) --}}
                            @if(auth()->user()->isAdmin())
                            <button onclick="hapusLaporan({{ $report->id }}, '{{ addslashes($report->title) }}')"
                                    class="px-2.5 py-1.5 text-[10px] font-bold text-red-500 bg-red-50 border border-red-200 rounded-lg hover:bg-red-500 hover:text-white transition-all">
                                Hapus
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-forest-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Belum ada laporan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi --}}
    @if($reports->hasPages())
    <div class="px-6 py-4 border-t border-sage-100/60 flex items-center justify-between">
        <p class="text-xs text-sage-500">Menampilkan {{ $reports->firstItem() }}–{{ $reports->lastItem() }} dari {{ $reports->total() }}</p>
        <div class="flex gap-1">
            @if($reports->onFirstPage())
                <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">‹</span>
            @else
                <a href="{{ $reports->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">‹</a>
            @endif
            @if($reports->hasMorePages())
                <a href="{{ $reports->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">›</a>
            @else
                <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">›</span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- ============================================================ --}}
{{-- MODAL LAPORAN BARU --}}
{{-- ============================================================ --}}
<div id="modal-laporan"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden"
     onclick="if(event.target===this) tutupModal()">
    <div class="absolute inset-0 bg-forest-900/50 backdrop-blur-sm"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="font-bold text-forest text-base">Buat Laporan Baru</h2>
            <button onclick="tutupModal()" class="p-2 text-gray-400 hover:text-forest hover:bg-forest-50 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Judul Laporan</label>
                <input type="text" id="lap-title" placeholder="Nama lengkap laporan..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jenis Laporan</label>
                <select id="lap-type"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium bg-white
                               focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                    @foreach(['Bulanan','Triwulan','Semester','Tahunan','Evaluasi','Kegiatan','Keuangan'] as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tanggal Laporan</label>
                <input type="date" id="lap-date" value="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-medium
                              focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status Awal</label>
                <div class="flex gap-3">
                    <label class="flex-1 flex items-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-amber-300 transition-all has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50">
                        <input type="radio" name="lap-status" value="Draft" checked class="text-amber-500">
                        <span class="text-xs font-bold">Draf</span>
                    </label>
                    <label class="flex-1 flex items-center gap-2 p-3 border border-gray-200 rounded-xl cursor-pointer hover:border-forest transition-all has-[:checked]:border-forest has-[:checked]:bg-forest-50">
                        <input type="radio" name="lap-status" value="Published" class="text-forest">
                        <span class="text-xs font-bold">Langsung Terbitkan</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="tutupModal()"
                    class="flex-1 py-2.5 border border-sage-200 text-sage-700 font-semibold rounded-xl text-sm hover:bg-sage-50 transition-all">
                Batal
            </button>
            <button onclick="simpanLaporan()"
                    class="flex-1 py-2.5 bg-forest text-white font-bold rounded-xl text-sm
                           hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                Simpan Laporan
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bukaModalLaporan() {
    document.getElementById('modal-laporan').classList.remove('hidden');
}
function tutupModal() {
    document.getElementById('modal-laporan').classList.add('hidden');
}

async function simpanLaporan() {
    const title  = document.getElementById('lap-title').value.trim();
    const type   = document.getElementById('lap-type').value;
    const date   = document.getElementById('lap-date').value;
    const status = document.querySelector('input[name="lap-status"]:checked')?.value;

    if (!title) {
        Swal.fire({ icon: 'warning', title: 'Judul Kosong', text: 'Isi judul laporan terlebih dahulu.', confirmButtonColor: '#096b68' });
        return;
    }

    const res = await apiFetch('{{ route('reports.store') }}', {
        method: 'POST',
        body: JSON.stringify({ title, type, report_date: date, status }),
    });

    if (res.ok && res.data.success) {
        tutupModal();
        showToast(res.data.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message, confirmButtonColor: '#096b68' });
    }
}

async function publishLaporan(id, title) {
    const result = await Swal.fire({
        icon: 'question',
        title: 'Terbitkan Laporan?',
        html: `<p class="text-sm text-gray-500">Laporan <strong>${title}</strong> akan dipublikasikan dan dapat dilihat oleh seluruh pengguna.</p>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Terbitkan',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
        buttonsStyling: false,
    });
    if (!result.isConfirmed) return;

    const res = await apiFetch(`{{ url('/laporan') }}/${id}/publish`, { method: 'POST' });
    if (res.ok && res.data.success) {
        showToast('Laporan berhasil diterbitkan.', 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showToast(res.data.message || 'Gagal menerbitkan laporan.', 'error');
    }
}

async function hapusLaporan(id, title) {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Hapus Laporan?',
        html: `<p class="text-sm text-gray-500">Laporan <strong>${title}</strong> akan dihapus permanen.</p>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
        buttonsStyling: false,
    });
    if (!result.isConfirmed) return;

    const res = await apiFetch(`{{ url('/laporan') }}/${id}`, { method: 'DELETE' });
    if (res.ok && res.data.success) {
        showToast(res.data.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showToast(res.data.message || 'Gagal menghapus laporan.', 'error');
    }
}

function tinjauDraf() {
    // Scroll ke tabel dan filter draf
    window.location = '{{ route('reports.index') }}?status=Draft';
}
</script>
@endpush
