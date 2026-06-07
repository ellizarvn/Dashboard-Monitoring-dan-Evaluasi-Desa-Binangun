<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel okr_targets.
 * Master target tahunan induk yang mencakup semua pilar OKR desa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->index();

            // OKR 1: Partisipasi & Kegiatan
            $table->decimal('target_partisipasi_persen', 5, 2)->comment('Target persentase partisipasi masyarakat');
            $table->integer('target_total_kegiatan')->comment('Target jumlah kegiatan desa per tahun');
            $table->integer('target_kehadiran_musyawarah')->comment('Target jumlah orang hadir per sesi musyawarah');

            // OKR 2: Ekonomi BUMDes
            $table->decimal('target_omzet_bumdes', 15, 2)->comment('Target omzet total BUMDes (Rupiah)');
            $table->decimal('target_laba_bersih', 15, 2)->comment('Target laba bersih total BUMDes (Rupiah)');
            $table->decimal('target_kontribusi_pades', 15, 2)->comment('Target kontribusi ke PADes (Rupiah)');

            // OKR 3: SDM & Inovasi
            $table->integer('target_pelatihan_masyarakat')->comment('Target jumlah peserta pelatihan');
            $table->string('target_indeks_inovasi', 50)->comment('Kategori indeks inovasi: Rendah/Sedang/Tinggi/Sangat Tinggi');
            $table->decimal('target_kepuasan_masyarakat', 2, 1)->comment('Target skor kepuasan skala 0-5');

            // Metadata
            $table->text('catatan_strategis')->nullable();
            $table->boolean('is_verified_rkp')->default(false)->comment('Sinkronisasi dengan RKP Desa');
            $table->boolean('is_verified_pagu')->default(false)->comment('Sesuai pagu indikatif');
            $table->boolean('is_verified_bpd')->default(false)->comment('Telah disetujui BPD');
            $table->timestamps();

            $table->unique('year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_targets');
    }
};
