@extends('layouts.app')
@section('title', 'Program Desa')
@section('breadcrumb', 'Program Desa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Status Program Desa</h1>
        <p class="text-sm text-sage-600 mt-0.5">Manajemen dan monitoring seluruh program kegiatan desa</p>
    </div>
    @if(auth()->user()->canMutateData())
    <button onclick="bukaModalProgram()"
            class="flex items-center gap-2 bg-forest text-white text-sm font-bold px-5 py-2.5 rounded-xl
                   hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Program Baru
    </button>
    @endif
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-7">
    @foreach([
        ['Total Program', $distribusi['total'], 'text-forest', 'bg-forest-50'],
        ['Aktif', $distribusi['AKTIF'], 'text-green-700', 'bg-green-50'],
        ['Pending', $distribusi['PENDING'], 'text-amber-700', 'bg-amber-50'],
        ['Selesai', $distribusi['SELESAI'], 'text-blue-700', 'bg-blue-50'],
    ] as [$label, $val, $textColor, $bgColor])
    <div class="bg-white rounded-2xl p-4 shadow-card border border-sage-100/60 flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $bgColor }} flex items-center justify-center flex-shrink-0">
            <span class="text-xl font-black {{ $textColor }}">{{ $val }}</span>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">{{ $label }}</p>
            <p class="text-[10px] text-sage-500">Program</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

    {{-- ============================================================ --}}
    {{-- FORM UPDATE PROGRAM (Kiri) --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-6">
            <h2 class="font-bold text-forest text-sm mb-5">
                <span id="form-title">Form Program Baru</span>
            </h2>

            <form id="program-form" class="space-y-4">
                @csrf
                <input type="hidden" id="program-id" value="">
                <input type="hidden" id="form-method" value="POST">

                {{-- Nama Program --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Program</label>
                    <input type="text" id="prog-name" placeholder="Nama kegiatan/program desa"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium
                                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                </div>

                {{-- Linked OKR --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Terhubung OKR</label>
                    <select id="prog-okr"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium bg-white
                                   focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                        <option value="">-- Pilih OKR --</option>
                        <option value="OKR1">OKR 1 — Partisipasi & Kegiatan</option>
                        <option value="OKR2">OKR 2 — Ekonomi BUMDes</option>
                        <option value="OKR3">OKR 3 — Kapasitas SDM</option>
                    </select>
                </div>

                {{-- Status (Button Group) --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status Program</label>
                    <div class="flex rounded-xl overflow-hidden border border-gray-200">
                        @foreach(['AKTIF'=>'Aktif','PENDING'=>'Pending','SELESAI'=>'Selesai'] as $val=>$label)
                        <button type="button"
                                data-status="{{ $val }}"
                                onclick="setStatus('{{ $val }}')"
                                class="status-btn flex-1 py-2.5 text-xs font-semibold transition-all duration-200
                                       {{ $val === 'PENDING' ? 'bg-forest text-white' : 'bg-white text-gray-500 hover:bg-forest-50' }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                    <input type="hidden" id="prog-status" value="PENDING">
                </div>

                {{-- Progress Slider --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-semibold text-gray-700">Progress Realisasi</label>
                        <span id="progress-display"
                              class="text-sm font-black text-forest bg-forest-50 px-2.5 py-0.5 rounded-lg">0%</span>
                    </div>
                    <input type="range" id="prog-progress" min="0" max="100" value="0"
                           class="w-full h-2 rounded-full appearance-none cursor-pointer"
                           style="accent-color: #1A362B;"
                           oninput="updateProgress(this.value)">
                    <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                        <span>0%</span><span>50%</span><span>100%</span>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea id="prog-desc" rows="3" placeholder="Deskripsi singkat program..."
                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm font-medium resize-none
                                     focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10"></textarea>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="simpanProgram()"
                            class="flex-1 py-3 bg-forest text-white font-bold rounded-xl text-sm
                                   hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
                        Simpan Program
                    </button>
                    <button type="button" onclick="resetForm()"
                            class="px-4 py-3 border border-sage-200 text-sage-700 font-semibold rounded-xl text-sm
                                   hover:bg-sage-50 transition-all">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        {{-- Card Target Triwulan --}}
        <div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-forest text-sm">Target Triwulan</h3>
                <span class="text-[10px] font-bold bg-green-50 text-green-700 px-2.5 py-1 rounded-full">ON TRACK</span>
            </div>
            <div class="space-y-3">
                @foreach(['Q1 (Jan-Mar)' => 85, 'Q2 (Apr-Jun)' => 72, 'Q3 (Jul-Sep)' => 45, 'Q4 (Okt-Des)' => 10] as $q => $pct)
                <div>
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-gray-600">{{ $q }}</span>
                        <span class="text-forest">{{ $pct }}%</span>
                    </div>
                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-forest rounded-full progress-animated" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- DATATABLE PROGRAM (Kanan) --}}
    {{-- ============================================================ --}}
    <div class="xl:col-span-3 bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">

        {{-- Filter Bar --}}
        <div class="flex flex-wrap items-center gap-3 px-6 py-4 border-b border-sage-100/60">
            <h2 class="font-bold text-forest text-sm flex-1">Daftar Program Desa</h2>
            <form method="GET" action="{{ route('programs.index') }}" class="flex gap-2">
                <select name="status" onchange="this.form.submit()"
                        class="text-xs font-semibold border border-sage-200 rounded-xl px-3 py-2 bg-white text-forest
                               focus:outline-none focus:ring-2 focus:ring-forest/10 cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="AKTIF" {{ $status==='AKTIF' ? 'selected':'' }}>Aktif</option>
                    <option value="PENDING" {{ $status==='PENDING' ? 'selected':'' }}>Pending</option>
                    <option value="SELESAI" {{ $status==='SELESAI' ? 'selected':'' }}>Selesai</option>
                </select>
                <select name="linked_okr" onchange="this.form.submit()"
                        class="text-xs font-semibold border border-sage-200 rounded-xl px-3 py-2 bg-white text-forest
                               focus:outline-none focus:ring-2 focus:ring-forest/10 cursor-pointer">
                    <option value="">Semua OKR</option>
                    <option value="OKR1" {{ $linkedOkr==='OKR1' ? 'selected':'' }}>OKR 1</option>
                    <option value="OKR2" {{ $linkedOkr==='OKR2' ? 'selected':'' }}>OKR 2</option>
                    <option value="OKR3" {{ $linkedOkr==='OKR3' ? 'selected':'' }}>OKR 3</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-sage-100/60 bg-forest-50/30">
                        <th class="text-left px-5 py-3 font-bold text-forest-600 text-[11px] uppercase tracking-wide">Program & OKR</th>
                        <th class="text-center px-4 py-3 font-bold text-forest-600 text-[11px] uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 font-bold text-forest-600 text-[11px] uppercase tracking-wide w-36">Progress</th>
                        <th class="text-center px-4 py-3 font-bold text-forest-600 text-[11px] uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sage-50">
                    @forelse($programs as $program)
                    <tr class="hover:bg-forest-50/30 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-800 text-xs leading-tight mb-0.5">{{ $program->name }}</p>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full
                                {{ $program->linked_okr === 'OKR1' ? 'bg-blue-50 text-blue-600' :
                                   ($program->linked_okr === 'OKR2' ? 'bg-amber-50 text-amber-600' : 'bg-purple-50 text-purple-600') }}">
                                {{ $program->linked_okr }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full
                                {{ $program->status === 'AKTIF' ? 'bg-green-100 text-green-700' :
                                   ($program->status === 'PENDING' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $program->status === 'AKTIF' ? 'bg-green-500' :
                                       ($program->status === 'PENDING' ? 'bg-amber-500' : 'bg-blue-500') }}"></span>
                                {{ $program->status }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full progress-animated"
                                         style="width: {{ $program->progress_percentage }}%; background: {{ $program->progress_color }}"></div>
                                </div>
                                <span class="text-[10px] font-bold text-forest w-7 text-right">{{ $program->progress_percentage }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="relative inline-block" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="p-2 rounded-lg hover:bg-forest-50 text-gray-400 hover:text-forest transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                                    </svg>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border border-sage-100 z-10 py-1">
                                    @if(auth()->user()->canMutateData())
                                    <button onclick="editProgram({{ $program->id }}, '{{ addslashes($program->name) }}', '{{ $program->linked_okr }}', '{{ $program->status }}', {{ $program->progress_percentage }}, '{{ addslashes($program->description ?? '') }}')"
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-forest-50 hover:text-forest transition-colors">
                                        Edit Program
                                    </button>
                                    @endif
                                    @if(auth()->user()->isAdmin())
                                    <button onclick="hapusProgram({{ $program->id }}, '{{ addslashes($program->name) }}')"
                                            class="w-full text-left px-4 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <div class="w-12 h-12 rounded-full bg-forest-50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-500">Belum ada program desa</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginasi --}}
        @if($programs->hasPages())
        <div class="px-6 py-4 border-t border-sage-100/60 flex items-center justify-between">
            <p class="text-xs text-sage-500">Menampilkan {{ $programs->firstItem() }}-{{ $programs->lastItem() }} dari {{ $programs->total() }} program</p>
            <div class="flex gap-1">
                @if($programs->onFirstPage())
                    <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">‹</span>
                @else
                    <a href="{{ $programs->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">‹</a>
                @endif
                @if($programs->hasMorePages())
                    <a href="{{ $programs->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">›</a>
                @else
                    <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Alpine.js untuk dropdown --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
<script>
/* ---- Progress Slider Real-time ---- */
function updateProgress(val) {
    document.getElementById('progress-display').textContent = val + '%';
}

/* ---- Status Button Group ---- */
function setStatus(status) {
    document.getElementById('prog-status').value = status;
    document.querySelectorAll('.status-btn').forEach(btn => {
        if (btn.dataset.status === status) {
            btn.classList.remove('bg-white', 'text-gray-500', 'hover:bg-forest-50');
            btn.classList.add('bg-forest', 'text-white');
        } else {
            btn.classList.remove('bg-forest', 'text-white');
            btn.classList.add('bg-white', 'text-gray-500', 'hover:bg-forest-50');
        }
    });
}

/* ---- Edit Program: Isi form dari data baris tabel ---- */
function editProgram(id, name, okr, status, progress, desc) {
    document.getElementById('program-id').value   = id;
    document.getElementById('form-method').value  = 'PUT';
    document.getElementById('form-title').textContent = 'Edit Program';
    document.getElementById('prog-name').value    = name;
    document.getElementById('prog-okr').value     = okr;
    document.getElementById('prog-desc').value    = desc;
    document.getElementById('prog-progress').value = progress;
    document.getElementById('progress-display').textContent = progress + '%';
    setStatus(status);
    document.getElementById('program-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ---- Reset Form ---- */
function resetForm() {
    document.getElementById('program-id').value   = '';
    document.getElementById('form-method').value  = 'POST';
    document.getElementById('form-title').textContent = 'Form Program Baru';
    document.getElementById('prog-name').value    = '';
    document.getElementById('prog-okr').value     = '';
    document.getElementById('prog-desc').value    = '';
    document.getElementById('prog-progress').value = 0;
    document.getElementById('progress-display').textContent = '0%';
    setStatus('PENDING');
}

/* ---- Simpan / Update Program ---- */
async function simpanProgram() {
    const id     = document.getElementById('program-id').value;
    const method = id ? 'PUT' : 'POST';
    const url    = id
        ? `{{ url('/program') }}/${id}`
        : '{{ route('programs.store') }}';

    const body = JSON.stringify({
        name:                document.getElementById('prog-name').value,
        linked_okr:          document.getElementById('prog-okr').value,
        status:              document.getElementById('prog-status').value,
        progress_percentage: parseInt(document.getElementById('prog-progress').value),
        description:         document.getElementById('prog-desc').value,
    });

    const res = await apiFetch(url, { method, body });

    if (res.ok && res.data.success) {
        showSuccessModal('Program Berhasil Disimpan', res.data.message, new Date().toLocaleString('id-ID'));
        setTimeout(() => location.reload(), 2000);
    } else {
        Swal.fire({ icon: 'error', title: 'Gagal', text: res.data.message || 'Terjadi kesalahan.', confirmButtonColor: '#1A362B' });
    }
}

/* ---- Hapus Program ---- */
async function hapusProgram(id, name) {
    const result = await Swal.fire({
        icon: 'warning',
        title: 'Hapus Program?',
        html: `<p class="text-sm text-gray-500">Program <strong>${name}</strong> akan dihapus permanen.</p>`,
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: { confirmButton: 'swal2-confirm', cancelButton: 'swal2-cancel' },
        buttonsStyling: false,
    });

    if (!result.isConfirmed) return;

    const res = await apiFetch(`{{ url('/program') }}/${id}`, { method: 'DELETE' });
    if (res.ok && res.data.success) {
        showToast(res.data.message, 'success');
        setTimeout(() => location.reload(), 1500);
    } else {
        showToast(res.data.message || 'Gagal menghapus program.', 'error');
    }
}
</script>
@endpush
