<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * DatabaseSeeder: Mengisi data operasional awal untuk semua tabel.
 * Mencakup user multi-role, target OKR, unit BUMDes, transaksi, program, dan log.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // =============================================
        // 1. USERS - Multi-Role RBAC
        // =============================================
        $users = [
            [
                'name'                => 'Ahmad Supriadi',
                'email'               => 'admin@desabinangun.id',
                'password'            => Hash::make('Admin@2024!'),
                'phone'               => '081234567890',
                'role'                => 'admin',
                'nip_administrator'   => '1234567890123',
                'two_factor_enabled'  => false,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'name'                => 'H. Bambang Sutrisno',
                'email'               => 'kepaladesa@desabinangun.id',
                'password'            => Hash::make('Kepala@2024!'),
                'phone'               => '082345678901',
                'role'                => 'kepala_desa',
                'nip_administrator'   => null,
                'two_factor_enabled'  => false,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'name'                => 'Siti Rahayu',
                'email'               => 'monitoring@desabinangun.id',
                'password'            => Hash::make('Monitor@2024!'),
                'phone'               => '083456789012',
                'role'                => 'tim_monitoring',
                'nip_administrator'   => null,
                'two_factor_enabled'  => false,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
            [
                'name'                => 'Drs. Hendra Wijaya',
                'email'               => 'bpd@desabinangun.id',
                'password'            => Hash::make('Bpd@2024!'),
                'phone'               => '084567890123',
                'role'                => 'bpd',
                'nip_administrator'   => null,
                'two_factor_enabled'  => false,
                'created_at'          => $now,
                'updated_at'          => $now,
            ],
        ];
        DB::table('users')->insert($users);

        // =============================================
        // 2. OKR TARGETS - Master Target Tahunan 2024
        // =============================================
        DB::table('okr_targets')->insert([
            'year'                        => 2024,
            'target_partisipasi_persen'   => 90.00,
            'target_total_kegiatan'       => 24,
            'target_kehadiran_musyawarah' => 150,
            'target_omzet_bumdes'         => 180000000.00,
            'target_laba_bersih'          => 45000000.00,
            'target_kontribusi_pades'     => 12000000.00,
            'target_pelatihan_masyarakat' => 200,
            'target_indeks_inovasi'       => 'Tinggi',
            'target_kepuasan_masyarakat'  => 4.2,
            'catatan_strategis'           => 'Target tahun 2024 difokuskan pada peningkatan partisipasi masyarakat dalam musyawarah desa dan penguatan ekonomi BUMDes melalui diversifikasi unit usaha. Mengacu pada RKP Desa 2024 dan RPJM Desa 2020-2026.',
            'is_verified_rkp'             => true,
            'is_verified_pagu'            => true,
            'is_verified_bpd'             => true,
            'created_at'                  => $now,
            'updated_at'                  => $now,
        ]);

        // =============================================
        // 3. OKR 1 - Partisipasi Bulanan 2024
        // =============================================
        $partisipasi = [
            ['month' => 'Januari',   'year' => 2024, 'total_warga_wajib_lapor' => 850, 'warga_hadir' => 680],
            ['month' => 'Februari',  'year' => 2024, 'total_warga_wajib_lapor' => 850, 'warga_hadir' => 710],
            ['month' => 'Maret',     'year' => 2024, 'total_warga_wajib_lapor' => 855, 'warga_hadir' => 745],
            ['month' => 'April',     'year' => 2024, 'total_warga_wajib_lapor' => 855, 'warga_hadir' => 730],
            ['month' => 'Mei',       'year' => 2024, 'total_warga_wajib_lapor' => 860, 'warga_hadir' => 760],
            ['month' => 'Juni',      'year' => 2024, 'total_warga_wajib_lapor' => 860, 'warga_hadir' => 780],
            ['month' => 'Juli',      'year' => 2024, 'total_warga_wajib_lapor' => 865, 'warga_hadir' => 720],
            ['month' => 'Agustus',   'year' => 2024, 'total_warga_wajib_lapor' => 865, 'warga_hadir' => 790],
        ];
        foreach ($partisipasi as &$p) {
            $p['calculated_percentage'] = round(($p['warga_hadir'] / $p['total_warga_wajib_lapor']) * 100, 2);
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
        }
        DB::table('okr_1_partisipasis')->insert($partisipasi);

        // =============================================
        // 4. OKR 2 - Unit BUMDes
        // =============================================
        $bumdesUnits = [
            ['name_unit' => 'Toko Sembako Mandiri',    'sector' => 'Perdagangan', 'pic_name' => 'Budi Santoso',    'initial_capital' => 25000000.00],
            ['name_unit' => 'Agrowisata Binangun',      'sector' => 'Pariwisata',  'pic_name' => 'Dewi Lestari',    'initial_capital' => 50000000.00],
            ['name_unit' => 'Koperasi Tani Sejahtera',  'sector' => 'Pertanian',   'pic_name' => 'Wahyu Prasetyo',  'initial_capital' => 30000000.00],
            ['name_unit' => 'Jasa Simpan Pinjam',       'sector' => 'Jasa',        'pic_name' => 'Eni Sumarni',     'initial_capital' => 40000000.00],
        ];
        foreach ($bumdesUnits as &$u) {
            $u['created_at'] = $now;
            $u['updated_at'] = $now;
        }
        DB::table('okr_2_bumdes_units')->insert($bumdesUnits);

        // =============================================
        // 5. OKR 2 - Transaksi BUMDes (6 bulan terakhir)
        // =============================================
        $transactions = [
            // Unit 1: Toko Sembako
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-03-01', 'type' => 'Pemasukan',   'nominal' => 18500000, 'description' => 'Penjualan sembako Maret'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-03-15', 'type' => 'Pengeluaran', 'nominal' => 12000000, 'description' => 'Restok barang dagangan'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-04-01', 'type' => 'Pemasukan',   'nominal' => 19200000, 'description' => 'Penjualan sembako April'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-04-20', 'type' => 'Pengeluaran', 'nominal' => 11500000, 'description' => 'Restok dan operasional'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-05-01', 'type' => 'Pemasukan',   'nominal' => 20100000, 'description' => 'Penjualan sembako Mei'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-05-18', 'type' => 'Pengeluaran', 'nominal' => 13200000, 'description' => 'Restok barang + gaji pengelola'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-06-01', 'type' => 'Pemasukan',   'nominal' => 21500000, 'description' => 'Penjualan sembako Juni'],
            ['bumdes_unit_id' => 1, 'transaction_date' => '2024-06-25', 'type' => 'Pengeluaran', 'nominal' => 14000000, 'description' => 'Operasional dan restok'],
            // Unit 2: Agrowisata
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-03-15', 'type' => 'Pemasukan',   'nominal' => 22000000, 'description' => 'Tiket masuk + sewa villa Maret'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-03-30', 'type' => 'Pengeluaran', 'nominal' => 8500000,  'description' => 'Biaya perawatan fasilitas'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-04-15', 'type' => 'Pemasukan',   'nominal' => 25000000, 'description' => 'Tiket masuk + kuliner April'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-04-28', 'type' => 'Pengeluaran', 'nominal' => 9200000,  'description' => 'Gaji karyawan dan utilitas'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-05-15', 'type' => 'Pemasukan',   'nominal' => 28000000, 'description' => 'Musim liburan Mei'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-05-30', 'type' => 'Pengeluaran', 'nominal' => 10500000, 'description' => 'Renovasi minor dan marketing'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-06-15', 'type' => 'Pemasukan',   'nominal' => 32000000, 'description' => 'Peak season Juni'],
            ['bumdes_unit_id' => 2, 'transaction_date' => '2024-06-28', 'type' => 'Pengeluaran', 'nominal' => 11000000, 'description' => 'Operasional Juni'],
            // Unit 3: Koperasi Tani
            ['bumdes_unit_id' => 3, 'transaction_date' => '2024-03-20', 'type' => 'Pemasukan',   'nominal' => 15000000, 'description' => 'Penjualan hasil panen gabah'],
            ['bumdes_unit_id' => 3, 'transaction_date' => '2024-03-25', 'type' => 'Pengeluaran', 'nominal' => 9000000,  'description' => 'Pembelian bibit dan pupuk'],
            ['bumdes_unit_id' => 3, 'transaction_date' => '2024-04-20', 'type' => 'Pemasukan',   'nominal' => 16500000, 'description' => 'Penjualan sayur dan palawija'],
            ['bumdes_unit_id' => 3, 'transaction_date' => '2024-04-28', 'type' => 'Pengeluaran', 'nominal' => 8800000,  'description' => 'Sarana produksi pertanian'],
            // Unit 4: Jasa Simpan Pinjam
            ['bumdes_unit_id' => 4, 'transaction_date' => '2024-03-31', 'type' => 'Pemasukan',   'nominal' => 12000000, 'description' => 'Bunga simpan pinjam Q1'],
            ['bumdes_unit_id' => 4, 'transaction_date' => '2024-04-30', 'type' => 'Pemasukan',   'nominal' => 13500000, 'description' => 'Bunga simpan pinjam April'],
            ['bumdes_unit_id' => 4, 'transaction_date' => '2024-05-31', 'type' => 'Pemasukan',   'nominal' => 14200000, 'description' => 'Bunga simpan pinjam Mei'],
        ];
        foreach ($transactions as &$t) {
            $t['created_at'] = $now;
            $t['updated_at'] = $now;
        }
        DB::table('okr_2_transactions')->insert($transactions);

        // =============================================
        // 6. OKR 2 - Kontribusi PADes
        // =============================================
        $padesContributions = [
            ['bumdes_unit_id' => 1, 'period_year_month' => '2024-03', 'nominal_setoran' => 1625000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 1, 'period_year_month' => '2024-04', 'nominal_setoran' => 1925000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 1, 'period_year_month' => '2024-05', 'nominal_setoran' => 1725000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 2, 'period_year_month' => '2024-03', 'nominal_setoran' => 3375000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 2, 'period_year_month' => '2024-04', 'nominal_setoran' => 3950000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 2, 'period_year_month' => '2024-05', 'nominal_setoran' => 4375000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 3, 'period_year_month' => '2024-03', 'nominal_setoran' => 1500000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 3, 'period_year_month' => '2024-04', 'nominal_setoran' => 1925000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 4, 'period_year_month' => '2024-03', 'nominal_setoran' => 3000000, 'file_proof_path' => null],
            ['bumdes_unit_id' => 4, 'period_year_month' => '2024-04', 'nominal_setoran' => 3375000, 'file_proof_path' => null],
        ];
        foreach ($padesContributions as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }
        DB::table('okr_2_pades_contributions')->insert($padesContributions);

        // =============================================
        // 7. OKR 3 - Kapasitas SDM
        // =============================================
        $sdmData = [
            ['period_date' => '2024-01-31', 'total_perangkat' => 28, 'total_staf_terlatih' => 18, 'avg_competency_score' => 6.8, 'keaktifan_kinerja_persen' => 71.43],
            ['period_date' => '2024-02-29', 'total_perangkat' => 28, 'total_staf_terlatih' => 19, 'avg_competency_score' => 7.0, 'keaktifan_kinerja_persen' => 75.00],
            ['period_date' => '2024-03-31', 'total_perangkat' => 29, 'total_staf_terlatih' => 21, 'avg_competency_score' => 7.2, 'keaktifan_kinerja_persen' => 78.57],
            ['period_date' => '2024-04-30', 'total_perangkat' => 29, 'total_staf_terlatih' => 23, 'avg_competency_score' => 7.5, 'keaktifan_kinerja_persen' => 82.14],
            ['period_date' => '2024-05-31', 'total_perangkat' => 30, 'total_staf_terlatih' => 25, 'avg_competency_score' => 7.8, 'keaktifan_kinerja_persen' => 85.00],
            ['period_date' => '2024-06-30', 'total_perangkat' => 30, 'total_staf_terlatih' => 26, 'avg_competency_score' => 8.0, 'keaktifan_kinerja_persen' => 87.50],
        ];
        foreach ($sdmData as &$s) {
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }
        DB::table('okr_3_sdm_capacities')->insert($sdmData);

        // =============================================
        // 8. VILLAGE PROGRAMS
        // =============================================
        $programs = [
            ['name' => 'Pembangunan Sarana Air Bersih',          'linked_okr' => 'OKR1', 'status' => 'AKTIF',    'progress_percentage' => 65,  'description' => 'Pemasangan pipa dan bak penampung air bersih di 3 dusun'],
            ['name' => 'Pengembangan Wisata Agro Binangun',       'linked_okr' => 'OKR2', 'status' => 'AKTIF',    'progress_percentage' => 78,  'description' => 'Pengembangan kawasan wisata pertanian dan edukasi'],
            ['name' => 'Pelatihan Digital Marketing UMKM',        'linked_okr' => 'OKR3', 'status' => 'SELESAI',  'progress_percentage' => 100, 'description' => 'Pelatihan pemasaran digital untuk 50 pelaku UMKM desa'],
            ['name' => 'Musyawarah Desa Tahunan 2024',            'linked_okr' => 'OKR1', 'status' => 'SELESAI',  'progress_percentage' => 100, 'description' => 'Musyawarah penetapan RKP Desa 2025'],
            ['name' => 'Digitalisasi Administrasi Desa',          'linked_okr' => 'OKR3', 'status' => 'AKTIF',    'progress_percentage' => 45,  'description' => 'Implementasi sistem informasi desa berbasis web'],
            ['name' => 'Program Ketahanan Pangan Desa',           'linked_okr' => 'OKR2', 'status' => 'AKTIF',    'progress_percentage' => 55,  'description' => 'Pengembangan lahan pertanian pangan desa seluas 5 Ha'],
            ['name' => 'Renovasi Balai Desa',                     'linked_okr' => 'OKR1', 'status' => 'PENDING',  'progress_percentage' => 10,  'description' => 'Renovasi total balai desa dengan kapasitas 500 orang'],
            ['name' => 'Pelatihan Kompetensi Perangkat Desa',     'linked_okr' => 'OKR3', 'status' => 'AKTIF',    'progress_percentage' => 70,  'description' => 'Sertifikasi kompetensi 30 perangkat desa'],
        ];
        foreach ($programs as &$p) {
            $p['created_at'] = $now;
            $p['updated_at'] = $now;
        }
        DB::table('village_programs')->insert($programs);

        // =============================================
        // 9. REPORTS
        // =============================================
        $reports = [
            ['title' => 'Laporan Kinerja Desa Triwulan I 2024',       'type' => 'Triwulan',  'report_date' => '2024-04-01', 'author_id' => 3, 'status' => 'Published'],
            ['title' => 'Laporan Realisasi APBDes Semester I 2024',    'type' => 'Semester',  'report_date' => '2024-07-01', 'author_id' => 1, 'status' => 'Published'],
            ['title' => 'Laporan Perkembangan BUMDes Juni 2024',       'type' => 'Bulanan',   'report_date' => '2024-07-05', 'author_id' => 3, 'status' => 'Draft'],
            ['title' => 'Evaluasi Program Ketahanan Pangan',           'type' => 'Evaluasi',  'report_date' => '2024-07-10', 'author_id' => 2, 'status' => 'Draft'],
            ['title' => 'Laporan Musyawarah Desa April 2024',          'type' => 'Kegiatan',  'report_date' => '2024-04-20', 'author_id' => 3, 'status' => 'Published'],
        ];
        foreach ($reports as &$r) {
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
        }
        DB::table('reports')->insert($reports);

        // =============================================
        // 10. SYSTEM LOGS - Sample Audit Trail
        // =============================================
        $logs = [
            ['user_id' => 1, 'activity_type' => 'LOGIN',        'module' => 'Auth',           'description' => 'Login berhasil dari browser Chrome 124',             'ip_address' => '192.168.1.10', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subHours(2)],
            ['user_id' => 1, 'activity_type' => 'UPDATE DATA',  'module' => 'OKR Target',      'description' => 'Update target tahunan OKR 2024',                     'ip_address' => '192.168.1.10', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subHours(1)],
            ['user_id' => 3, 'activity_type' => 'CREATE DATA',  'module' => 'OKR1 Partisipasi','description' => 'Input data partisipasi bulan Agustus 2024',          'ip_address' => '192.168.1.15', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subMinutes(45)],
            ['user_id' => 2, 'activity_type' => 'LOGIN',        'module' => 'Auth',           'description' => 'Login berhasil dari browser Safari Mobile',          'ip_address' => '192.168.1.20', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subMinutes(30)],
            ['user_id' => 3, 'activity_type' => 'UPLOAD FILE',  'module' => 'OKR2 PADes',     'description' => 'Upload bukti transfer PADes Unit Agrowisata',        'ip_address' => '192.168.1.15', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subMinutes(20)],
            ['user_id' => 4, 'activity_type' => 'UNAUTHORIZED ACCESS', 'module' => 'OKR Target', 'description' => 'Percobaan akses halaman edit OKR Target tanpa izin', 'ip_address' => '192.168.1.25', 'status' => 'GAGAL', 'created_at' => $now->copy()->subMinutes(10)],
            ['user_id' => 1, 'activity_type' => 'EXPORT DATA',  'module' => 'Audit Log',      'description' => 'Export log sistem ke format CSV',                    'ip_address' => '192.168.1.10', 'status' => 'BERHASIL', 'created_at' => $now->copy()->subMinutes(5)],
            ['user_id' => 3, 'activity_type' => 'UPDATE DATA',  'module' => 'Program Desa',   'description' => 'Update progress program Agrowisata ke 78%',          'ip_address' => '192.168.1.15', 'status' => 'BERHASIL', 'created_at' => $now],
        ];
        DB::table('system_logs')->insert($logs);
    }
}
