@extends('layouts.app')
@section('title', 'Pusat Bantuan')
@section('breadcrumb', 'Pusat Bantuan')

@section('content')
<div class="mb-7">
    <h1 class="font-display text-2xl text-forest">Pusat Bantuan</h1>
    <p class="text-sm text-sage-600 mt-0.5">Panduan penggunaan dan FAQ sistem dashboard kinerja desa</p>
</div>

{{-- Search Bar --}}
<div class="relative max-w-xl mb-8">
    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-sage-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
    </svg>
    <input type="text" id="faq-search" placeholder="Cari pertanyaan atau topik bantuan..."
           oninput="filterFaq(this.value)"
           class="w-full pl-11 pr-4 py-3.5 rounded-2xl border border-sage-200 text-sm font-medium bg-white shadow-sm
                  focus:outline-none focus:border-forest focus:ring-2 focus:ring-forest/10">
</div>

{{-- Menu Panduan Grid --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-10">
    @foreach([
        ['Dashboard', 'Memahami halaman utama dan cara membaca grafik kinerja', 'chart-bar', 'bg-forest-50 border-forest-200'],
        ['Target OKR', 'Panduan menetapkan dan mengubah target tahunan desa', 'target', 'bg-blue-50 border-blue-200'],
        ['Input Data', 'Cara memasukkan data partisipasi, transaksi, dan SDM', 'edit', 'bg-amber-50 border-amber-200'],
        ['BUMDes', 'Pengelolaan unit usaha dan keuangan BUMDes', 'currency', 'bg-green-50 border-green-200'],
        ['Program Desa', 'Mengelola dan monitoring program desa aktif', 'clipboard', 'bg-purple-50 border-purple-200'],
        ['Laporan', 'Cara membuat dan menerbitkan laporan desa', 'document', 'bg-rose-50 border-rose-200'],
        ['Audit Log', 'Memantau riwayat aktivitas sistem dan pengguna', 'shield', 'bg-indigo-50 border-indigo-200'],
        ['Keamanan', 'Pengaturan kata sandi dan otentikasi dua faktor', 'cog', 'bg-gray-50 border-gray-200'],
    ] as [$title, $desc, $icon, $style])
    <div class="bg-white rounded-2xl p-5 shadow-card border {{ $style }} hover:shadow-card-hover transition-shadow cursor-pointer group">
        <div class="w-10 h-10 rounded-xl {{ explode(' ', $style)[0] }} flex items-center justify-center mb-3">
            <svg class="w-5 h-5 text-forest" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($icon === 'chart-bar')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                @elseif($icon === 'currency')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @elseif($icon === 'clipboard')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                @elseif($icon === 'document')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                @elseif($icon === 'shield')
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                @endif
            </svg>
        </div>
        <p class="text-xs font-bold text-forest mb-1">{{ $title }}</p>
        <p class="text-[10px] text-gray-500 leading-relaxed">{{ $desc }}</p>
    </div>
    @endforeach
</div>

{{-- ============================================================ --}}
{{-- FAQ ACCORDION --}}
{{-- ============================================================ --}}
<div class="max-w-3xl">
    <h2 class="font-bold text-forest text-base mb-5">Pertanyaan yang Sering Diajukan</h2>

    <div class="space-y-3" id="faq-container">
        @foreach([
            ['Bagaimana cara menginput data partisipasi bulanan?',
             'Buka menu OKR 1 — Partisipasi, kemudian pilih bulan dan tahun yang sesuai. Masukkan total warga wajib lapor dan jumlah warga yang hadir. Sistem akan otomatis menghitung persentase partisipasi. Klik "Simpan Data Partisipasi" untuk menyimpan. Hanya pengguna dengan role Admin atau Tim Monitoring yang dapat menginput data.'],

            ['Apa aturan minimal setoran PADes dari BUMDes?',
             'Berdasarkan Peraturan Desa (PerDes) yang berlaku, setiap unit usaha BUMDes wajib menyetorkan minimal 25% dari total laba bersih bulanan ke PADes (Pendapatan Asli Desa). Sistem akan otomatis memvalidasi nominal setoran terhadap aturan ini. Setoran yang kurang dari minimum akan ditolak oleh sistem beserta penjelasan kekurangannya.'],

            ['Siapa saja yang bisa mengakses halaman Audit Log?',
             'Halaman Audit Log hanya dapat diakses oleh pengguna dengan role Administrator dan Kepala Desa. Pengguna dengan role Tim Monitoring dan BPD tidak dapat mengakses halaman ini. Ekspor log ke format CSV hanya bisa dilakukan oleh Administrator.'],

            ['Bagaimana cara mengaktifkan Otentikasi Dua Faktor (2FA)?',
             'Buka menu Pengaturan Akun, kemudian klik toggle switch Otentikasi Dua Faktor. Sistem akan meminta konfirmasi sebelum mengaktifkan. Setelah aktif, setiap proses login akan memerlukan kode verifikasi tambahan. Disarankan untuk menggunakan aplikasi autentikator seperti Google Authenticator atau Authy.'],

            ['Bagaimana cara mengubah status program dari Aktif ke Selesai?',
             'Buka halaman Program Desa, temukan program yang ingin diubah, klik ikon tiga titik di kolom Aksi, lalu pilih "Edit Program". Ubah status menjadi SELESAI. Perhatian: ketika status diubah ke SELESAI, progress otomatis akan diatur ke 100%. Status SELESAI bersifat final dan tidak dapat dikembalikan ke status sebelumnya melalui sistem.'],

            ['Apa perbedaan role Administrator, Kepala Desa, Tim Monitoring, dan BPD?',
             'Administrator: akses penuh ke semua fitur termasuk delete data dan ekspor log. Kepala Desa: dapat melihat semua data, menerbitkan laporan, dan menyetujui target OKR. Tim Monitoring: dapat menginput dan mengupdate data OKR dan program desa. BPD: akses read-only untuk memantau kinerja desa tanpa dapat mengubah data.'],

            ['Bagaimana cara mengekspor log sistem ke CSV?',
             'Buka halaman Audit Log, gunakan filter tanggal jika diperlukan untuk mempersempit hasil, kemudian klik tombol "Ekspor Log (CSV)" di pojok kanan atas. File CSV akan otomatis terunduh. Fitur ini hanya tersedia untuk Administrator. File CSV dapat dibuka dengan Microsoft Excel atau Google Sheets.'],

            ['Mengapa data partisipasi tidak bisa diinput dua kali untuk bulan yang sama?',
             'Sistem menggunakan constraint unique per kombinasi bulan dan tahun untuk mencegah duplikasi data. Jika Anda ingin mengubah data yang sudah diinput, sistem akan otomatis melakukan pembaruan (update) pada data yang sudah ada, bukan membuat entri baru.'],
        ] as $i => [$q, $a])
        <div class="faq-item bg-white rounded-2xl border border-sage-200 overflow-hidden shadow-sm"
             data-question="{{ strtolower($q) }}">
            <button onclick="toggleFaq(this)"
                    class="w-full flex items-center justify-between gap-4 px-6 py-4 text-left hover:bg-forest-50/30 transition-colors">
                <span class="text-sm font-bold text-forest leading-snug">{{ $q }}</span>
                <svg class="w-4 h-4 text-sage flex-shrink-0 transition-transform duration-300 faq-chevron"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="faq-answer hidden px-6 pb-5 border-t border-sage-100">
                <p class="text-sm text-gray-600 leading-relaxed pt-4">{{ $a }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ============================================================ --}}
{{-- KONTAK SUPPORT --}}
{{-- ============================================================ --}}
<div class="mt-10 bg-gradient-to-r from-forest to-forest-600 rounded-2xl p-6 max-w-3xl">
    <div class="flex flex-col sm:flex-row items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div class="flex-1 text-center sm:text-left">
            <p class="text-white font-bold text-base">Butuh Bantuan Lebih Lanjut?</p>
            <p class="text-white/70 text-sm mt-1">Hubungi tim teknis Desa Binangun untuk dukungan langsung.</p>
        </div>
        <div class="flex flex-col gap-2 flex-shrink-0">
            <a href="mailto:support@desabinangun.id"
               class="flex items-center gap-2 bg-white text-forest font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-forest-50 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email Support
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
/* ---- Toggle FAQ Accordion ---- */
function toggleFaq(btn) {
    const answer  = btn.nextElementSibling;
    const chevron = btn.querySelector('.faq-chevron');
    const isOpen  = !answer.classList.contains('hidden');

    // Tutup semua yang lain
    document.querySelectorAll('.faq-answer').forEach(a => a.classList.add('hidden'));
    document.querySelectorAll('.faq-chevron').forEach(c => c.style.transform = '');

    if (!isOpen) {
        answer.classList.remove('hidden');
        chevron.style.transform = 'rotate(180deg)';
    }
}

/* ---- Filter FAQ berdasarkan pencarian ---- */
function filterFaq(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.faq-item').forEach(item => {
        const text = item.dataset.question + ' ' +
                     item.querySelector('.faq-answer p')?.textContent.toLowerCase();
        item.style.display = !q || text.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
