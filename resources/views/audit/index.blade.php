@extends('layouts.app')
@section('title', 'Audit Log Sistem')
@section('breadcrumb', 'Audit Log Sistem')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="font-display text-2xl text-forest">Log Aktivitas Sistem</h1>
        <p class="text-sm text-sage-600 mt-0.5">Rekam jejak seluruh aktivitas pengguna dan sistem</p>
    </div>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('audit.export.csv', request()->only(['start_date','end_date'])) }}"
       class="flex items-center gap-2 bg-forest text-white text-sm font-bold px-5 py-2.5 rounded-xl
              hover:bg-forest-600 active:scale-[0.98] transition-all shadow-md shadow-forest/20">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Ekspor Log (CSV)
    </a>
    @endif
</div>

{{-- ============================================================ --}}
{{-- TOP METRICS --}}
{{-- ============================================================ --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Log Hari Ini --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">Total Log Hari Ini</p>
                <p class="text-3xl font-black text-forest">{{ $metrics['total_hari_ini'] }}</p>
                <div class="flex items-center gap-1 mt-1">
                    @if($metrics['trend_persen'] >= 0)
                        <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/>
                        </svg>
                        <span class="text-xs font-bold text-green-600">+{{ $metrics['trend_persen'] }}%</span>
                    @else
                        <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <span class="text-xs font-bold text-red-500">{{ $metrics['trend_persen'] }}%</span>
                    @endif
                    <span class="text-xs text-gray-400">vs kemarin ({{ $metrics['total_kemarin'] }})</span>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Aktivitas Kritis --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">Aktivitas Kritis</p>
                <p class="text-3xl font-black {{ $metrics['kritis'] > 0 ? 'text-red-600' : 'text-forest' }}">
                    {{ $metrics['kritis'] }}
                </p>
                @if($metrics['kritis'] > 0)
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full mt-1 badge-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                    Perlu Perhatian
                </span>
                @else
                <span class="text-[10px] text-sage-500 mt-1 block">Tidak ada insiden hari ini</span>
                @endif
            </div>
            <div class="w-10 h-10 rounded-xl {{ $metrics['kritis'] > 0 ? 'bg-red-50' : 'bg-forest-50' }} flex items-center justify-center">
                <svg class="w-5 h-5 {{ $metrics['kritis'] > 0 ? 'text-red-500' : 'text-forest' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Pengguna Aktif --}}
    <div class="bg-white rounded-2xl p-5 shadow-card border border-sage-100/60">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">Pengguna Aktif Hari Ini</p>
                <p class="text-3xl font-black text-forest">{{ $metrics['pengguna_aktif'] }}</p>
                <span class="text-[10px] text-sage-500 mt-1 block">Pengguna unik login hari ini</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-forest-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- FILTER PANEL --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl shadow-card border border-sage-100/60 p-5 mb-6">
    <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-40">
            <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Tanggal Akhir</label>
            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                   class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Pengguna</label>
            <select name="user_id"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium bg-white focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                <option value="">Semua Pengguna</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Tipe Aktivitas</label>
            <select name="activity_type"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium bg-white focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
                <option value="">Semua Tipe</option>
                @foreach($activityTypes as $type)
                    <option value="{{ $type }}" {{ ($filters['activity_type'] ?? '') === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-forest text-white text-xs font-bold rounded-xl hover:bg-forest-600 transition-all">
                Terapkan Filter
            </button>
            <a href="{{ route('audit.index') }}"
               class="px-5 py-2.5 border border-sage-200 text-sage-700 text-xs font-bold rounded-xl hover:bg-sage-50 transition-all">
                Reset
            </a>
        </div>
    </form>
</div>

{{-- ============================================================ --}}
{{-- DATATABLE LOG --}}
{{-- ============================================================ --}}
<div class="bg-white rounded-2xl shadow-card border border-sage-100/60 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-sage-100/60 bg-forest-50/30">
                    <th class="text-left px-5 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide whitespace-nowrap">Waktu</th>
                    <th class="text-left px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Pengguna</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide whitespace-nowrap">Tipe Aktivitas</th>
                    <th class="text-left px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Modul</th>
                    <th class="text-left px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Deskripsi</th>
                    <th class="text-center px-4 py-3.5 font-bold text-forest text-[11px] uppercase tracking-wide">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-sage-50">
                @forelse($logs as $log)
                <tr class="hover:bg-forest-50/20 transition-colors">
                    {{-- Waktu --}}
                    <td class="px-5 py-4 whitespace-nowrap">
                        <p class="font-semibold text-gray-800">{{ $log->created_at->format('H:i:s') }}</p>
                        <p class="text-[10px] text-gray-400">{{ $log->created_at->format('d/m/Y') }}</p>
                    </td>

                    {{-- Pengguna --}}
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-forest flex items-center justify-center flex-shrink-0">
                                <span class="text-[9px] font-black text-white">
                                    {{ $log->user?->initials ?? 'SY' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate max-w-28">{{ $log->user?->name ?? 'Sistem' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $log->user?->role_label ?? 'Otomatis' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Tipe Badge --}}
                    <td class="px-4 py-4 text-center">
                        @php
                            $actColors = [
                                'LOGIN'               => 'bg-blue-100 text-blue-700',
                                'LOGOUT'              => 'bg-gray-100 text-gray-600',
                                'UPDATE DATA'         => 'bg-amber-100 text-amber-700',
                                'CREATE DATA'         => 'bg-green-100 text-green-700',
                                'DELETE DATA'         => 'bg-red-100 text-red-700',
                                'UPLOAD FILE'         => 'bg-purple-100 text-purple-700',
                                'EXPORT DATA'         => 'bg-indigo-100 text-indigo-700',
                                'UNAUTHORIZED ACCESS' => 'bg-red-100 text-red-700',
                            ];
                            $color = $actColors[$log->activity_type] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-block text-[9px] font-bold px-2 py-1 rounded-lg whitespace-nowrap {{ $color }}">
                            {{ $log->activity_type }}
                        </span>
                    </td>

                    {{-- Modul --}}
                    <td class="px-4 py-4">
                        <span class="text-xs text-gray-600 font-medium">{{ $log->module }}</span>
                    </td>

                    {{-- Deskripsi --}}
                    <td class="px-4 py-4">
                        <p class="text-xs text-gray-600 max-w-56 line-clamp-2">{{ $log->description }}</p>
                        @if($log->ip_address)
                        <p class="text-[10px] text-gray-400 mt-0.5 font-mono">{{ $log->ip_address }}</p>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2.5 py-1 rounded-full
                            {{ $log->status === 'BERHASIL' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $log->status === 'BERHASIL' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $log->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-forest-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-sage" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-500">Tidak ada log ditemukan</p>
                        <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian Anda</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi --}}
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-sage-100/60 flex items-center justify-between">
        <p class="text-xs text-sage-500">
            Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }} entri log
        </p>
        <div class="flex gap-1">
            @if($logs->onFirstPage())
                <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">‹ Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">‹ Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-forest border border-sage-200 rounded-lg hover:bg-forest-50">Next ›</a>
            @else
                <span class="px-3 py-1.5 text-xs text-gray-300 border border-gray-100 rounded-lg">Next ›</span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
